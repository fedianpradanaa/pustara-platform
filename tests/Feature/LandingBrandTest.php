<?php

namespace Tests\Feature;

use Tests\TestCase;

class LandingBrandTest extends TestCase
{
    public function test_landing_exposes_the_active_brand_content_and_share_metadata(): void
    {
        $this->withoutVite();

        $expected = [
            'pustara' => [
                'name' => 'Pustaka Nusantara',
                'email' => 'info@pustakanusantara.id',
                'title' => 'Pustaka Nusantara | Wujudkan Perpustakaan Digital Lembaga',
                'hero' => 'Buka lebih banyak pintu menuju pengetahuan.',
            ],
            'gia' => [
                'name' => 'GIA e-Library',
                'email' => 'info@pustaka-gia.id',
                'title' => 'GIA e-Library | Ruang Baca Digital untuk Lembaga Anda',
                'hero' => 'Satu ruang baca. Banyak cerita untuk berkembang.',
            ],
        ][config('brand.active')];

        $brand = config('brand.current');
        $this->assertSame($expected['name'], $brand['name']);
        $this->assertSame($expected['email'], $brand['contact_email']);
        $this->assertSame($expected['title'], $brand['social_title']);
        $this->assertSame($expected['hero'], $brand['landing']['hero_title']);

        $this->get('/')
            ->assertOk()
            ->assertSee('<meta property="og:title" content="' . $expected['title'] . '"', false)
            ->assertSee('<meta property="og:description" content="' . $brand['social_description'] . '"', false)
            ->assertSee('<meta name="twitter:title" content="' . $expected['title'] . '"', false)
            ->assertSee(json_encode($expected['email']), false)
            ->assertSee(json_encode($expected['hero']), false);
    }
}
