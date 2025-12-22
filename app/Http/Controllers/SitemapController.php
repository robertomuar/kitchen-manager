<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class SitemapController extends Controller
{
    public function index(): Response
    {
        $excludedMiddleware = ['auth', 'verified', 'admin'];
        $excludedPrefixes = [
            'admin',
            'dashboard',
            'products',
            'stock',
            'locations',
            'profile',
            'kitchen',
            'login',
            'register',
            'forgot-password',
            'reset-password',
            'verify-email',
            'confirm-password',
            'email/verification-notification',
            'debug',
        ];

        $urls = collect(Route::getRoutes())
            ->filter(function ($route) use ($excludedMiddleware, $excludedPrefixes) {
                if (!in_array('GET', $route->methods(), true)) {
                    return false;
                }

                $uri = $route->uri();

                if (Str::contains($uri, '{')) {
                    return false;
                }

                foreach ($excludedPrefixes as $prefix) {
                    if ($uri === $prefix || Str::startsWith($uri, $prefix . '/')) {
                        return false;
                    }
                }

                $routeMiddleware = $route->gatherMiddleware();
                foreach ($excludedMiddleware as $middleware) {
                    if (in_array($middleware, $routeMiddleware, true)) {
                        return false;
                    }
                }

                if ($route->getName() && Str::startsWith($route->getName(), 'debug')) {
                    return false;
                }

                return true;
            })
            ->map(function ($route) {
                $uri = $route->uri();

                return [
                    'loc' => url($uri === '/' ? '/' : '/' . ltrim($uri, '/')),
                    'lastmod' => now()->toAtomString(),
                ];
            })
            ->values();

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml');
    }
}
