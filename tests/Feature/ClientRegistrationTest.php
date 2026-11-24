<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ClientRegistrationTest extends TestCase
{
    public function test_client_temp_email_migration_preserves_existing_rows_and_can_be_rolled_back(): void
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');

        (require database_path('migrations/2025_01_12_142325_create_tclient_temp_table.php'))->up();
        DB::table('tclient_temp')->insert(['client_id' => 'existing']);

        $migration = require database_path('migrations/2026_11_24_000002_add_email_to_tclient_temp_table.php');
        $migration->up();

        $this->assertDatabaseHas('tclient_temp', ['client_id' => 'existing', 'email' => null]);
        DB::table('tclient_temp')->where('client_id', 'existing')->update(['email' => 'client@example.test']);
        $this->assertDatabaseHas('tclient_temp', ['client_id' => 'existing', 'email' => 'client@example.test']);

        $migration->down();
        $this->assertFalse(Schema::hasColumn('tclient_temp', 'email'));
        $this->assertDatabaseHas('tclient_temp', ['client_id' => 'existing']);
    }

    public function test_reference_code_is_required_and_must_exist(): void
    {
        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite.database' => ':memory:',
        ]);
        DB::purge('sqlite');

        Schema::create('tcompany', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('noref');
            $table->string('type');
        });
        DB::table('tcompany')->insert([
            ['id' => 'company-1', 'noref' => 'REF123', 'type' => '2'],
            ['id' => 'supplier-1', 'noref' => 'SUP123', 'type' => '1'],
        ]);

        $this->postJson('/pendaftaran-client', [])->assertUnprocessable()->assertJsonValidationErrors('noref');
        $this->postJson('/pendaftaran-client', ['noref' => 'UNKNOWN'])->assertUnprocessable()->assertJsonValidationErrors('noref');
        $this->postJson('/pendaftaran-client', ['noref' => 'SUP123'])->assertUnprocessable()->assertJsonValidationErrors('noref');
        $this->postJson('/pendaftaran-client', ['noref' => 'REF123'])->assertUnprocessable()->assertJsonMissingValidationErrors('noref');
        $this->postJson('/pendaftaran-client', ['email' => 'bukan-email'])->assertUnprocessable()->assertJsonValidationErrors('email');
    }

    public function test_reference_check_only_accepts_distributor_codes(): void
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('tcompany', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('noref');
            $table->string('type');
        });
        DB::table('tcompany')->insert([
            ['id' => 'd1', 'noref' => 'DIST123', 'type' => '2'],
            ['id' => 's1', 'noref' => 'SUP123', 'type' => '1'],
        ]);

        $this->postJson('/pendaftaran-client/periksa-referensi', ['noref' => 'DIST123'])->assertOk()->assertJsonPath('valid', true);
        $this->postJson('/pendaftaran-client/periksa-referensi', ['noref' => 'SUP123'])->assertOk()->assertJsonPath('valid', false);
        $this->postJson('/pendaftaran-client/periksa-referensi', ['noref' => 'OTHER'])->assertOk()->assertJsonPath('valid', false);
        $this->postJson('/pendaftaran-client/periksa-referensi', ['noref' => ''])->assertUnprocessable();
    }

    public function test_registration_layout_exposes_active_brand_domain(): void
    {
        $this->withoutVite();

        $expected = config('brand.active') === 'gia' ? '.pustaka-gia.id' : '.pustakanusantara.id';
        $this->assertSame($expected, config('brand.current.website_suffix'));
        $this->get('/pendaftaran-client')->assertOk()->assertSee($expected, false);
    }
}
