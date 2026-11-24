<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ClientRegistrationTest extends TestCase
{
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
