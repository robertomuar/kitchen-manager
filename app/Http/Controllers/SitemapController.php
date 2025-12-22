<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $baseUrl = rtrim(config('app.url'), '/');
        $excludedPrefixes = [
            'login',
            'register',
            'forgot-password',
            'reset-password',
            'confirm-password',
            'email/verification-notification',
            'dashboard',
            'admin',
            'profile',
            'password',
            'verify-email',
            'logout',
        ];

        $urls = collect(Route::getRoutes())
            ->filter(function ($route) use ($excludedPrefixes) {
                $methods = $route->methods();
                if (!in_array('GET', $methods, true)) {
                    return false;
                }

                $uri = ltrim($route->uri(), '/');

                if ($uri === 'sitemap.xml') {
                    return true;
                }

                if (str_contains($uri, '{')) {
                    return false;
                }

                $middleware = $route->gatherMiddleware();
                if (array_intersect($middleware, ['auth', 'verified', 'admin'])) {
                    return false;
                }

                foreach ($excludedPrefixes as $prefix) {
                    if ($uri === $prefix || str_starts_with($uri, $prefix . '/')) {
                        return false;
                    }
                }

                return true;
            })
            ->map(function ($route) use ($baseUrl) {
                $uri = trim($route->uri(), '/');
                if ($uri === '' || $uri === '/') {
                    return $baseUrl;
                }

                return $baseUrl . '/' . $uri;
            })
            ->unique()
            ->values();

        return response()
            ->view('sitemap.xml', [
                'urls' => $urls,
                'generatedAt' => now()->toAtomString(),
            ])
            ->header('Content-Type', 'application/xml');
    }
}
