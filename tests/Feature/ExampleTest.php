<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_landing_page_advertises_social_preview_and_icons(): void
    {
        $this->withoutVite();

        $response = $this->get('/');

        $response->assertOk()
            ->assertSee(asset('images/logo/social-preview.png'), false)
            ->assertSee('content="image/png"', false)
            ->assertSee('content="1200"', false)
            ->assertSee('content="630"', false)
            ->assertSee(asset('favicon.ico'), false)
            ->assertSee(asset('images/logo/favicon.svg'), false)
            ->assertSee(asset('images/logo/apple-touch-icon.png'), false);
    }

    public function test_registration_page_advertises_the_same_icons(): void
    {
        $this->withoutVite();

        $this->get('/pendaftaran-supplier')
            ->assertOk()
            ->assertSee(asset('favicon.ico'), false)
            ->assertSee(asset('images/logo/favicon.svg'), false)
            ->assertSee(asset('images/logo/apple-touch-icon.png'), false);
    }
}
