<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_robots_txt_is_available(): void
    {
        $this->get('/robots.txt')
            ->assertOk()
            ->assertSee('User-agent');
    }

    public function test_sitemap_is_available(): void
    {
        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml');
    }
}
