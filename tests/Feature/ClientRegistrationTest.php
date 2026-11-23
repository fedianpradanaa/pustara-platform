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
        });
        DB::table('tcompany')->insert(['id' => 'company-1', 'noref' => 'REF123']);

        $this->postJson('/pendaftaran-client', [])->assertUnprocessable()->assertJsonValidationErrors('noref');
        $this->postJson('/pendaftaran-client', ['noref' => 'UNKNOWN'])->assertUnprocessable()->assertJsonValidationErrors('noref');
        $this->postJson('/pendaftaran-client', ['noref' => 'REF123'])->assertUnprocessable()->assertJsonMissingValidationErrors('noref');
        $this->postJson('/pendaftaran-client', ['email' => 'bukan-email'])->assertUnprocessable()->assertJsonValidationErrors('email');
    }

    public function test_registration_layout_exposes_active_brand_domain(): void
    {
        $this->withoutVite();

        $expected = config('brand.active') === 'gia' ? '.pustaka-gia.id' : '.pustakanusantara.id';
        $this->assertSame($expected, config('brand.current.website_suffix'));
        $this->get('/pendaftaran-client')->assertOk()->assertSee($expected, false);
    }
}
