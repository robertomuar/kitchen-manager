<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class RobotsController extends Controller
{
    public function __invoke(): Response
    {
        $baseUrl = rtrim(config('app.url'), '/');
        $sitemapUrl = $baseUrl . '/sitemap.xml';

        $lines = [
            'User-agent: *',
            'Disallow: /login',
            'Disallow: /register',
            'Disallow: /password',
            'Disallow: /email',
            'Disallow: /admin',
            'Disallow: /dashboard',
            'Disallow: /profile',
            'Disallow: /products',
            'Disallow: /stock',
            'Disallow: /locations',
            'Disallow: /kitchen',
            'Disallow: /barcode',
            'Sitemap: ' . $sitemapUrl,
        ];

        return response(implode("\n", $lines) . "\n", 200)
            ->header('Content-Type', 'text/plain')
            ->header('Cache-Control', 'public, max-age=3600');
    }
}
