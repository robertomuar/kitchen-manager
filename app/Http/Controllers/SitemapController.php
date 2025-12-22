<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $baseUrl = rtrim(config('app.url'), '/');
        $now = now();

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

        $routes = collect(Route::getRoutes())
            ->filter(function ($route) use ($excludedPrefixes) {
                $methods = $route->methods();
                if (!in_array('GET', $methods, true) && !in_array('HEAD', $methods, true)) {
                    return false;
                }

                $uri = ltrim($route->uri(), '/');

                if ($uri === '' || $uri === '/') {
                    return true;
                }

                if ($uri === 'sitemap.xml' || $uri === 'robots.txt') {
                    return false;
                }

                if (Str::contains($uri, '{')) {
                    return false;
                }

                $middleware = $route->gatherMiddleware();
                if (array_intersect($middleware, ['auth', 'verified', 'admin'])) {
                    return false;
                }

                foreach ($excludedPrefixes as $prefix) {
                    if ($uri === $prefix || Str::startsWith($uri, $prefix . '/')) {
                        return false;
                    }
                }

                return true;
            })
            ->map(function ($route) use ($baseUrl, $now) {
                $uri = trim($route->uri(), '/');
                $loc = $uri === '' ? $baseUrl : $baseUrl . '/' . $uri;

                $priority = match ($uri) {
                    '' => '1.0',
                    'features' => '0.9',
                    'pricing' => '0.8',
                    'faq' => '0.8',
                    'blog' => '0.8',
                    'contact' => '0.6',
                    'privacy-policy', 'terms', 'cookies-policy' => '0.4',
                    default => '0.5',
                };

                return [
                    'loc' => $loc,
                    'lastmod' => $now->toAtomString(),
                    'priority' => $priority,
                ];
            })
            ->values();

        $latestPost = Post::published()->orderByDesc('published_at')->first();
        $blogIndexLastmod = $latestPost?->published_at?->toAtomString() ?? $now->toAtomString();

        $postEntries = Post::published()
            ->orderByDesc('published_at')
            ->get()
            ->map(function (Post $post) use ($baseUrl) {
                return [
                    'loc' => $baseUrl . '/blog/' . $post->slug,
                    'lastmod' => optional($post->published_at)->toAtomString() ?? now()->toAtomString(),
                    'priority' => '0.7',
                ];
            });

        $urls = $routes
            ->map(function (array $entry) use ($blogIndexLastmod) {
                if (Str::endsWith($entry['loc'], '/blog')) {
                    $entry['lastmod'] = $blogIndexLastmod;
                }

                return $entry;
            })
            ->merge($postEntries)
            ->unique('loc')
            ->values();

        return response()
            ->view('sitemap.xml', [
                'urls' => $urls,
            ])
            ->header('Content-Type', 'application/xml')
            ->header('Cache-Control', 'public, max-age=3600');
    }
}
