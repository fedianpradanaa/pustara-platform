<?php

namespace Tests\Feature;

use Tests\TestCase;

class BrandSignatureTest extends TestCase
{
    public function test_default_signature_uses_the_active_brand_name(): void
    {
        foreach (['Pustaka Nusantara', 'GIA e-Library'] as $name) {
            config(['brand.current.name' => $name]);

            $this->get('/signature')
                ->assertOk()
                ->assertSee('Direktur ' . $name . '.')
                ->assertDontSee('PT. PUSTAKA NUSANTARA');
        }
    }
}
