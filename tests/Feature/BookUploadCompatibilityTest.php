<?php

namespace Tests\Feature;

use App\Http\Controllers\Core\Transactions\ApprovalBooksController;
use App\Http\Controllers\Core\Transactions\UploadPurchaseOrderController;
use App\Http\Controllers\Core\Upload\UploadBooksController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class BookUploadCompatibilityTest extends TestCase
{
    private string $diskRoot;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'filesystems.default' => 'local',
            'app.key' => 'base64:'.base64_encode(str_repeat('a', 32)),
        ]);
        DB::purge('sqlite');

        $this->diskRoot = sys_get_temp_dir().DIRECTORY_SEPARATOR.'pustara-book-test-'.bin2hex(random_bytes(8));
        Storage::set('local', Storage::build(['driver' => 'local', 'root' => $this->diskRoot.DIRECTORY_SEPARATOR.'local']));
        Storage::set('public', Storage::build(['driver' => 'local', 'root' => $this->diskRoot.DIRECTORY_SEPARATOR.'public']));

        (require database_path('migrations/2024_10_07_172303_create_tbook_draft_table.php'))->up();
        (require database_path('migrations/2024_09_15_182349_create_tbook_table.php'))->up();
        (require database_path('migrations/2026_11_27_000001_add_book_sale_flags.php'))->up();
        (require database_path('migrations/2025_01_25_141233_create_tmapping_book_table.php'))->up();
        Schema::create('tpo_detail', function (Blueprint $table) {
            $table->string('client_id');
            $table->string('po_number');
            $table->string('po_date');
            $table->string('book_id');
            $table->integer('qty');
            $table->float('sellprice')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->string('created_by')->nullable();
        });
        Schema::create('tpo_header', function (Blueprint $table) {
            $table->string('client_id');
            $table->string('po_number');
            $table->string('po_date');
            $table->string('status');
            $table->string('po_type')->nullable();
            $table->float('po_amount')->nullable();
            $table->float('po_discount')->nullable();
            $table->float('persentase_supplier')->default(0);
            $table->string('distributor_id')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->string('created_by')->nullable();
        });
        Schema::create('tclient', function (Blueprint $table) {
            $table->string('client_id');
            $table->string('company_id');
            $table->string('instansi_name')->nullable();
            $table->string('flag_appr')->default('Y');
        });
        Schema::create('tcompany', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->string('type');
        });
        (require database_path('migrations/2024_12_19_233132_create_tpo_paid_off_table.php'))->up();
        Schema::create('tpublisher', function (Blueprint $table) {
            $table->string('id');
            $table->string('client_id');
            $table->string('description')->nullable();
        });
        Schema::create('tcompany_category', function (Blueprint $table) {
            $table->string('category_id');
            $table->string('client_id');
            $table->string('category_desc')->nullable();
        });
        $this->actingAs(new User(['email' => 'admin@example.test', 'role' => 0]));
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->diskRoot);
        parent::tearDown();
    }

    public function test_both_existing_and_step_by_step_book_files_can_be_approved(): void
    {
        foreach ([
            ['old', 'books_draft/', 'covers_draft/'],
            ['step', 'tmp/books_tmp/', 'tmp/covers_tmp/'],
        ] as [$id, $bookPath, $coverPath]) {
            $filename = $id.'.gns';
            $cover = $id.'.jpg';
            DB::table('tbook_draft')->insert([
                'book_id' => $id,
                'supplier_id' => 'supplier',
                'filename' => $filename,
                'cover' => $cover,
                'status' => '2',
            ]);
            Storage::disk('local')->put($bookPath.$filename, encrypt('pdf-'.$id));
            Storage::disk('public')->put($coverPath.$cover, 'cover-'.$id);
        }

        $request = Request::create('/approval', 'POST', ['old', 'step']);
        $this->app->instance('request', $request);
        $response = app(ApprovalBooksController::class)->store($request);

        $this->assertSame(201, $response->getStatusCode(), $response->getContent());
        foreach (['old', 'step'] as $id) {
            $this->assertSame('3', DB::table('tbook_draft')->where('book_id', $id)->value('status'));
            $this->assertSame('pdf-'.$id, decrypt(Storage::disk('local')->get('books/'.$id.'.gns')));
            $this->assertSame('cover-'.$id, Storage::disk('public')->get('covers/'.$id.'.jpg'));
        }
    }

    public function test_existing_draft_file_can_be_downloaded_and_previewed(): void
    {
        Storage::disk('local')->put('books_draft/existing.gns', encrypt('pdf contents'));
        $request = Request::create('/', 'GET', ['file' => 'existing.gns']);

        $supplierResponse = app(UploadBooksController::class)->downloadFile($request);
        $approvalResponse = app(ApprovalBooksController::class)->viewFile($request);

        $this->assertSame('pdf contents', decrypt($supplierResponse->getContent()));
        $this->assertSame('pdf contents', $approvalResponse->getContent());
    }

    public function test_existing_draft_cover_is_shown_when_completing_book_data(): void
    {
        $this->actingAs(new User(['client_id' => 'supplier']));
        DB::table('tbook_draft')->insert([
            'book_id' => 'existing',
            'supplier_id' => 'supplier',
            'filename' => 'existing.gns',
            'cover' => 'existing.jpg',
        ]);
        Storage::disk('public')->put('covers_draft/existing.jpg', 'cover');
        $request = Request::create('/', 'GET', ['param' => 'selected-data', 'data' => ['existing']]);
        $this->app->instance('request', $request);

        $response = app(UploadBooksController::class)->show('unused');

        $this->assertSame(route('encrypt-books-cover', ['location' => 'draft', 'filename' => 'existing.jpg']), $response->getData(true)[0]['path_cover']);
    }

    public function test_revision_preserves_published_book_and_mapping_until_admin_approval(): void
    {
        DB::table('tbook')->insert(['book_id' => 'book', 'supplier_id' => 'supplier', 'title' => 'Lama', 'filename' => 'book.gns', 'cover' => 'book.jpg']);
        DB::table('tbook_draft')->insert(['book_id' => 'book', 'supplier_id' => 'supplier', 'title' => 'Lama', 'filename' => 'book.gns', 'cover' => 'book.jpg', 'status' => '3']);
        DB::table('tmapping_book')->insert(['client_id' => 'client', 'po_number' => 'po', 'book_id' => 'book']);
        DB::table('tpublisher')->insert(['id' => 'publisher', 'client_id' => 'supplier']);
        DB::table('tcompany_category')->insert(['category_id' => 'category', 'client_id' => 'supplier']);
        Storage::disk('local')->put('books/book.gns', encrypt('pdf'));
        Storage::disk('public')->put('covers/book.jpg', 'cover');
        $this->withoutMiddleware()->actingAs(new User(['client_id' => 'supplier', 'role' => 1, 'email' => 'supplier@example.test']));

        $this->patchJson('/upload/book-management/book/availability', ['is_available' => false])->assertOk();
        $data = ['title' => 'Baru', 'writer' => 'Penulis', 'isbn' => '123456789', 'eisbn' => '123', 'size' => 1, 'year' => '2025', 'volume' => 1, 'edition' => 1, 'page' => 1, 'sinopsis' => 'Sinopsis', 'sellprice' => 1, 'rentprice' => 1, 'retailprice' => 1, 'city' => 'Kota', 'age' => 1, 'publisher_id' => 'publisher', 'category_id' => 'category'];
        $this->postJson('/upload/book-management/book/revision', $data)->assertOk();
        $this->assertSame('Lama', DB::table('tbook')->where('book_id', 'book')->value('title'));
        $this->assertEquals(1, DB::table('tbook')->where('book_id', 'book')->value('is_editing'));
        $this->assertEquals('2', DB::table('tbook_draft')->where('book_id', 'book')->value('status'));
        $this->patchJson('/upload/book-management/book/availability', ['is_available' => true])->assertStatus(409);
        $this->postJson('/upload/book-management/book/revision', $data)->assertStatus(409);

        $this->actingAs(new User(['role' => 0, 'email' => 'admin@example.test']));
        $request = Request::create('/reject', 'POST', ['book']);
        $this->app->instance('request', $request);
        $request->setUserResolver(fn () => auth()->user());
        $this->assertSame(201, app(ApprovalBooksController::class)->reject($request)->getStatusCode());
        $this->assertEquals(1, DB::table('tbook')->where('book_id', 'book')->value('is_editing'));

        $this->actingAs(new User(['client_id' => 'supplier', 'role' => 1, 'email' => 'supplier@example.test']));
        $this->postJson('/upload/book-management/book/revision', $data)->assertOk();
        $this->actingAs(new User(['role' => 0, 'email' => 'admin@example.test']));
        $request = Request::create('/approval', 'POST', ['book']);
        $this->app->instance('request', $request);
        $request->setUserResolver(fn () => auth()->user());
        $this->assertSame(201, app(ApprovalBooksController::class)->store($request)->getStatusCode());
        $book = DB::table('tbook')->where('book_id', 'book')->first();
        $this->assertSame('Baru', $book->title);
        $this->assertEquals(0, $book->is_available);
        $this->assertEquals(0, $book->is_editing);
        $this->assertSame(1, DB::table('tmapping_book')->count());
    }

    public function test_other_suppliers_cannot_manage_a_published_book(): void
    {
        DB::table('tbook')->insert(['book_id' => 'book', 'supplier_id' => 'owner']);
        $this->withoutMiddleware()->actingAs(new User(['client_id' => 'other', 'role' => 1]));

        $this->patchJson('/upload/book-management/book/availability', ['is_available' => false])->assertNotFound();
        $this->assertEquals(1, DB::table('tbook')->where('book_id', 'book')->value('is_available'));
    }

    public function test_only_unpublished_unmapped_owned_drafts_can_be_deleted(): void
    {
        DB::table('tbook_draft')->insert([
            ['book_id' => 'draft', 'supplier_id' => 'supplier', 'status' => '1', 'filename' => 'draft.gns', 'cover' => 'draft.jpg'],
            ['book_id' => 'mapped', 'supplier_id' => 'supplier', 'status' => '1', 'filename' => 'mapped.gns', 'cover' => 'mapped.jpg'],
        ]);
        DB::table('tmapping_book')->insert(['client_id' => 'client', 'po_number' => 'po', 'book_id' => 'mapped']);
        Storage::disk('local')->put('tmp/books_tmp/draft.gns', 'file');
        Storage::disk('public')->put('tmp/covers_tmp/draft.jpg', 'cover');
        $this->withoutMiddleware()->actingAs(new User(['client_id' => 'supplier', 'role' => 1]));

        $this->deleteJson('/upload/book-management/mapped')->assertStatus(409);
        $this->deleteJson('/upload/book-management/draft')->assertOk();
        $this->assertFalse(DB::table('tbook_draft')->where('book_id', 'draft')->exists());
        $this->assertFalse(Storage::disk('local')->exists('tmp/books_tmp/draft.gns'));
        $this->assertFalse(Storage::disk('public')->exists('tmp/covers_tmp/draft.jpg'));
        $this->assertTrue(DB::table('tmapping_book')->where('book_id', 'mapped')->exists());
    }

    public function test_withdrawn_book_cannot_be_added_to_new_po_but_existing_po_can_be_approved(): void
    {
        DB::table('tbook')->insert(['book_id' => 'book', 'supplier_id' => 'supplier', 'is_available' => false]);
        DB::table('tclient')->insert(['client_id' => 'client', 'company_id' => 'distributor']);
        DB::table('tpo_header')->insert(['client_id' => 'client', 'po_number' => 'existing', 'po_date' => '2025-01-01', 'status' => '1']);
        DB::table('tpo_detail')->insert(['client_id' => 'client', 'po_number' => 'existing', 'po_date' => '2025-01-01', 'book_id' => 'book', 'qty' => 1]);
        $this->actingAs(new User(['role' => 0, 'email' => 'admin@example.test']));

        $sheet = new Spreadsheet();
        $sheet->getActiveSheet()->setTitle('DATA 01');
        foreach (['B1' => 'client', 'B2' => 'new', 'B3' => '2025-01-01', 'B4' => '10', 'A8' => 'book', 'B8' => '1'] as $cell => $value) {
            $sheet->getActiveSheet()->setCellValue($cell, $value);
        }
        File::ensureDirectoryExists($this->diskRoot);
        $path = $this->diskRoot.DIRECTORY_SEPARATOR.'po.xlsx';
        (new Xlsx($sheet))->save($path);
        $request = Request::create('/', 'POST', [], [], ['file_master' => new UploadedFile($path, 'po.xlsx', null, null, true)]);
        $this->assertSame(422, app(UploadPurchaseOrderController::class)->uploadExcel($request)->getStatusCode());
        $this->assertFalse(DB::table('tpo_header')->where('po_number', 'new')->exists());
        DB::table('tbook')->where('book_id', 'book')->update(['is_available' => true, 'is_editing' => true]);
        $this->assertSame(422, app(UploadPurchaseOrderController::class)->uploadExcel($request)->getStatusCode());
        $this->assertFalse(DB::table('tpo_header')->where('po_number', 'new')->exists());

        $request = Request::create('/', 'PUT', ['client_id' => 'client', 'po_number' => 'existing', 'po_date' => '2025-01-01']);
        $request->setUserResolver(fn () => auth()->user());
        $this->assertSame(201, app(UploadPurchaseOrderController::class)->update($request, '2')->getStatusCode());
        $this->assertTrue(DB::table('tmapping_book')->where('book_id', 'book')->where('client_id', 'client')->exists());
    }

    public function test_supplier_list_includes_separate_sale_and_revision_flags(): void
    {
        DB::table('tbook')->insert(['book_id' => 'book', 'supplier_id' => 'supplier', 'is_available' => true, 'is_editing' => true]);
        DB::table('tbook_draft')->insert([
            ['book_id' => 'book', 'supplier_id' => 'supplier', 'status' => '2', 'createdate' => '2025-01-01', 'sellprice' => 1250, 'rentprice' => 250, 'retailprice' => 2000],
            ['book_id' => 'draft', 'supplier_id' => 'supplier', 'status' => '1', 'createdate' => '2025-01-01', 'sellprice' => 0, 'rentprice' => 0, 'retailprice' => 0],
        ]);
        $this->actingAs(new User(['client_id' => 'supplier', 'role' => 1]));

        $body = app(UploadBooksController::class)->index(Request::create('/'))->getData(true);
        $items = collect($body['data'])->keyBy('book_id');
        $this->assertEquals(1, $items['book']['is_available']);
        $this->assertEquals(1, $items['book']['is_editing']);
        $this->assertEquals(1250, $items['book']['sellprice_raw']);
        $this->assertEquals(250, $items['book']['rentprice_raw']);
        $this->assertEquals(2000, $items['book']['retailprice_raw']);
        $this->assertNull($items['draft']['is_available']);
    }

    public function test_admin_mapping_creates_approved_purchase_and_only_sent_orders_reach_the_supplier(): void
    {
        DB::table('tclient')->insert(['client_id' => 'client', 'company_id' => 'distributor', 'instansi_name' => 'Client']);
        DB::table('tcompany')->insert(['id' => 'supplier', 'name' => 'Supplier', 'type' => '1']);
        DB::table('tbook')->insert(['book_id' => 'book', 'supplier_id' => 'supplier', 'title' => 'Buku', 'sellprice' => 1250]);
        $this->withoutMiddleware()->actingAs(new User(['role' => 0, 'email' => 'admin@example.test']));

        $response = $this->postJson('/transactions/book-mapping', [
            'client_id' => 'client', 'po_type' => '1', 'persentase_supplier' => 70,
            'books' => [['book_id' => 'book', 'qty' => 2]],
        ])->assertCreated();
        $number = $response->json('po_number');
        $header = DB::table('tpo_header')->where('po_number', $number)->first();
        $this->assertSame('2', $header->status);
        $this->assertEquals(70, $header->persentase_supplier);
        $this->assertEquals(1250, DB::table('tpo_detail')->where('po_number', $number)->value('sellprice'));
        $this->assertEquals(2, DB::table('tmapping_book')->where('po_number', $number)->value('copy'));
        $this->assertSame(0, DB::table('tpo_paid_off')->count());

        $this->actingAs(new User(['role' => 1, 'client_id' => 'supplier']));
        $this->getJson('/report/po-report')->assertOk()->assertJsonCount(0, 'data');
        $this->putJson('/transactions/po-upload/3', ['client_id' => 'client', 'po_number' => $number, 'po_date' => $header->po_date])->assertForbidden();
        $this->actingAs(new User(['role' => 0, 'email' => 'admin@example.test']));
        $this->putJson('/transactions/po-upload/3', ['client_id' => 'client', 'po_number' => $number, 'po_date' => $header->po_date])->assertStatus(201);
        $this->actingAs(new User(['role' => 1, 'client_id' => 'supplier']));
        $this->getJson('/report/po-report')->assertOk()->assertJsonCount(1, 'data');
        $this->actingAs(new User(['role' => 1, 'client_id' => 'other']));
        $this->getJson('/report/po-report')->assertOk()->assertJsonCount(0, 'data');
    }

    public function test_grants_are_mapped_and_sent_at_zero_price_in_new_and_existing_po_flows(): void
    {
        DB::table('tclient')->insert(['client_id' => 'client', 'company_id' => 'distributor', 'instansi_name' => 'Client']);
        DB::table('tcompany')->insert(['id' => 'supplier', 'name' => 'Supplier', 'type' => '3']);
        DB::table('tbook')->insert(['book_id' => 'book', 'supplier_id' => 'supplier', 'title' => 'Buku', 'sellprice' => 1250]);
        $this->withoutMiddleware()->actingAs(new User(['role' => 0, 'email' => 'admin@example.test']));
        $response = $this->postJson('/transactions/book-mapping', [
            'client_id' => 'client', 'po_type' => '2', 'books' => [['book_id' => 'book', 'qty' => 3]],
        ])->assertCreated();
        $number = $response->json('po_number');
        $this->assertEquals('3', DB::table('tpo_header')->where('po_number', $number)->value('status'));
        $this->assertEquals(0, DB::table('tpo_detail')->where('po_number', $number)->value('sellprice'));
        $this->assertTrue(DB::table('tmapping_book')->where('po_number', $number)->exists());
        $this->assertTrue(DB::table('tpo_paid_off')->where('po_number', $number)->exists());

        DB::table('tpo_header')->insert(['client_id' => 'client', 'po_number' => 'legacy', 'po_date' => '2025-01-01', 'po_type' => '2', 'status' => '1']);
        DB::table('tpo_detail')->insert(['client_id' => 'client', 'po_number' => 'legacy', 'po_date' => '2025-01-01', 'book_id' => 'book', 'qty' => 1, 'sellprice' => 1250]);
        $this->putJson('/transactions/po-upload/2', ['client_id' => 'client', 'po_number' => 'legacy', 'po_date' => '2025-01-01'])->assertStatus(201);
        $this->assertEquals('3', DB::table('tpo_header')->where('po_number', 'legacy')->value('status'));
        $this->assertEquals(0, DB::table('tpo_detail')->where('po_number', 'legacy')->value('sellprice'));
        $this->assertTrue(DB::table('tmapping_book')->where('po_number', 'legacy')->exists());
        $this->assertTrue(DB::table('tpo_paid_off')->where('po_number', 'legacy')->exists());
        DB::table('tpo_header')->insert(['client_id' => 'client', 'po_number' => 'approved-gift', 'po_date' => '2025-01-01', 'po_type' => '2', 'status' => '2']);
        DB::table('tpo_detail')->insert(['client_id' => 'client', 'po_number' => 'approved-gift', 'po_date' => '2025-01-01', 'book_id' => 'book', 'qty' => 1, 'sellprice' => 1250]);
        DB::table('tmapping_book')->insert(['client_id' => 'client', 'po_number' => 'approved-gift', 'book_id' => 'book', 'copy' => 1]);
        $this->putJson('/transactions/po-upload/3', ['client_id' => 'client', 'po_number' => 'approved-gift', 'po_date' => '2025-01-01'])->assertStatus(201);
        $this->assertEquals(0, DB::table('tpo_detail')->where('po_number', 'approved-gift')->value('sellprice'));
        $this->assertEquals(1, DB::table('tmapping_book')->where('po_number', 'approved-gift')->count());
        $this->postJson('/transactions/po-paid', [
            'client_id' => 'client', 'supplier_id' => 'supplier', 'po_number' => 'legacy', 'po_date' => '2025-01-01',
            'file_images' => UploadedFile::fake()->create('payment.jpg', 10, 'image/jpeg'),
        ])->assertStatus(409);
        $this->actingAs(new User(['role' => 1, 'client_id' => 'supplier']));
        $this->getJson('/report/po-report')->assertOk()->assertJsonCount(3, 'data');
    }

    public function test_mapping_is_admin_only_and_unavailable_books_cannot_be_mapped(): void
    {
        DB::table('tclient')->insert(['client_id' => 'client', 'company_id' => 'distributor']);
        DB::table('tcompany')->insert(['id' => 'supplier', 'name' => 'Supplier', 'type' => '1']);
        DB::table('tbook')->insert([
            ['book_id' => 'withdrawn', 'supplier_id' => 'supplier', 'is_available' => false, 'is_editing' => false],
            ['book_id' => 'editing', 'supplier_id' => 'supplier', 'is_available' => true, 'is_editing' => true],
        ]);
        $this->withoutMiddleware()->actingAs(new User(['role' => 1, 'client_id' => 'supplier']));
        $this->getJson('/transactions/book-mapping')->assertForbidden();
        $this->postJson('/transactions/book-mapping', [])->assertForbidden();
        $this->actingAs(new User(['role' => 2, 'client_id' => 'distributor']));
        $this->getJson('/transactions/book-mapping')->assertForbidden();
        $this->actingAs(new User(['role' => 0, 'email' => 'admin@example.test']));
        $this->getJson('/transactions/book-mapping')->assertOk()->assertJsonCount(0, 'books');
        foreach (['withdrawn', 'editing'] as $id) {
            $this->postJson('/transactions/book-mapping', [
                'client_id' => 'client', 'po_type' => '2', 'books' => [['book_id' => $id, 'qty' => 1]],
            ])->assertStatus(409);
        }
        $this->assertSame(0, DB::table('tpo_header')->count());
        $this->assertSame(0, DB::table('tmapping_book')->count());
    }
}
