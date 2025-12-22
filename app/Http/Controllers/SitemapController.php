<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $baseUrl = rtrim(config('app.url'), '/');
        $generatedAt = now()->toAtomString();

        $urls = collect([
            ['loc' => $baseUrl, 'lastmod' => $generatedAt, 'priority' => '1.0'],
            ['loc' => $baseUrl . '/features', 'lastmod' => $generatedAt, 'priority' => '0.8'],
            ['loc' => $baseUrl . '/faq', 'lastmod' => $generatedAt, 'priority' => '0.7'],
            ['loc' => $baseUrl . '/pricing', 'lastmod' => $generatedAt, 'priority' => '0.7'],
            ['loc' => $baseUrl . '/contact', 'lastmod' => $generatedAt, 'priority' => '0.6'],
            ['loc' => $baseUrl . '/privacy-policy', 'lastmod' => $generatedAt, 'priority' => '0.3'],
            ['loc' => $baseUrl . '/terms', 'lastmod' => $generatedAt, 'priority' => '0.3'],
            ['loc' => $baseUrl . '/blog', 'lastmod' => $generatedAt, 'priority' => '0.7'],
        ])->merge(
            Post::query()
                ->published()
                ->orderByDesc('published_at')
                ->get(['slug', 'published_at', 'updated_at'])
                ->map(function (Post $post) use ($baseUrl) {
                    return [
                        'loc' => $baseUrl . '/blog/' . $post->slug,
                        'lastmod' => optional($post->updated_at ?? $post->published_at)->toAtomString(),
                        'priority' => '0.6',
                    ];
                })
        );

        return response()
            ->view('sitemap.xml', [
                'urls' => $urls,
                'generatedAt' => $generatedAt,
            ])
            ->header('Content-Type', 'application/xml');
    }
}
