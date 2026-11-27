<?php

namespace Tests\Feature;

use App\Http\Controllers\Core\Transactions\ApprovalBooksController;
use App\Http\Controllers\Core\Upload\UploadBooksController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
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
}
