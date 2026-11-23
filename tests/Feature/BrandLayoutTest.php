<?php

namespace Tests\Feature;

use Tests\TestCase;

class BrandLayoutTest extends TestCase
{
    public function test_login_and_dashboard_layouts_expose_the_active_brand(): void
    {
        $this->withoutVite();

        foreach ([
            ['name' => 'GIA e-Library', 'logo_path' => 'images/gia/logo.png'],
            ['name' => 'Pustaka Nusantara', 'logo_path' => 'images/logo/logo.png'],
        ] as $brand) {
            config(['brand.current.name' => $brand['name'], 'brand.current.logo_path' => $brand['logo_path']]);

            foreach (['layouts.login', 'layouts.app'] as $layout) {
                $html = view($layout)->render();

                $this->assertStringContainsString('window.brandConfig = ', $html);
                $this->assertStringContainsString(json_encode($brand['name']), $html);
                $this->assertStringContainsString(json_encode($brand['logo_path']), $html);
            }
        }

    }

    public function test_pages_use_the_active_brand_png_favicon(): void
    {
        $this->withoutVite();

        foreach (['images/logo/favicon.png', 'images/gia/favicon.png'] as $path) {
            config(['brand.current.favicon_path' => $path]);
            $icon = '<link rel="icon" href="' . asset($path) . '" type="image/png" />';

            foreach (['welcome', 'layouts.login', 'layouts.app', 'layouts.register', 'layouts.verify'] as $layout) {
                $this->assertStringContainsString($icon, view($layout)->render());
            }

            $this->get('/signature')->assertOk()->assertSee($icon, false);
        }
    }
}
