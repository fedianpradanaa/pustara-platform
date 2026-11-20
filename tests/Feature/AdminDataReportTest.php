<?php

namespace Tests\Feature;

use App\Http\Controllers\Core\Report\AdminDataController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AdminDataReportTest extends TestCase
{
    public function test_book_report_reads_published_books_without_a_status_column(): void
    {
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
            'database.connections.sqlite.foreign_key_constraints' => false,
        ]);
        DB::purge('sqlite');

        $migration = require database_path('migrations/2024_09_15_182349_create_tbook_table.php');
        $migration->up();
        Schema::create('tcompany', function (Blueprint $table) {
            $table->string('id');
            $table->string('name');
            $table->string('type');
        });
        Schema::create('tcompany_category', function (Blueprint $table) {
            $table->string('client_id');
            $table->string('category_id');
            $table->string('category_desc');
        });
        Schema::create('tpublisher', function (Blueprint $table) {
            $table->string('client_id');
            $table->string('id');
            $table->string('description');
        });

        DB::table('tcompany')->insert(['id' => 's1', 'name' => 'Supplier', 'type' => '1']);
        DB::table('tcompany_category')->insert(['client_id' => 's1', 'category_id' => 'c1', 'category_desc' => 'Kategori']);
        DB::table('tpublisher')->insert(['client_id' => 's1', 'id' => 'p1', 'description' => 'Penerbit']);
        DB::table('tbook')->insert(['book_id' => 'b1', 'supplier_id' => 's1', 'category_id' => 'c1', 'publisher_id' => 'p1', 'title' => 'Buku']);

        $response = app(AdminDataController::class)->books(Request::create('/report/admin-data/books'));
        $body = $response->getData(true);

        $this->assertArrayNotHasKey('error', $body);
        $this->assertCount(1, $body['data']);
        $this->assertSame('Supplier', $body['data'][0]['supplier_name']);
        $this->assertSame('Kategori', $body['data'][0]['category_name']);
        $this->assertSame('Penerbit', $body['data'][0]['publisher_name']);
    }
}
