<?php

namespace Tests\Feature;

use Tests\TestCase;

class LegalPagesTest extends TestCase
{
    public function testLegalNoticePageLoads(): void
    {
        $response = $this->get('/legal/aviso-legal');

        $response->assertStatus(200);
    }

    public function testLegalTermsPageLoads(): void
    {
        $response = $this->get('/legal/terminos');

        $response->assertStatus(200);
    }

    public function testLegalPrivacyPageLoads(): void
    {
        $response = $this->get('/legal/privacidad');

        $response->assertStatus(200);
    }

    public function testLegalCookiesPageLoads(): void
    {
        $response = $this->get('/legal/cookies');

        $response->assertStatus(200);
    }
}
