<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BlogController extends Controller
{
    public function index(Request $request): Response
    {
        $baseUrl = rtrim(config('app.url'), '/');
        $canonical = $baseUrl . '/blog';

        $posts = Post::query()
            ->published()
            ->orderByDesc('published_at')
            ->paginate(8)
            ->withQueryString()
            ->through(function (Post $post) {
                return [
                    'title' => $post->title,
                    'slug' => $post->slug,
                    'excerpt' => $post->excerpt,
                    'published_at' => optional($post->published_at)->toDateString(),
                ];
            });

        return Inertia::render('Blog/Index', [
            'baseUrl' => $baseUrl,
            'canonical' => $canonical,
            'title' => 'Blog',
            'posts' => $posts,
        ]);
    }

    public function show(Request $request, string $slug): Response
    {
        $post = Post::query()
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $baseUrl = rtrim(config('app.url'), '/');
        $canonical = $post->canonical ?: ($baseUrl . '/blog/' . $post->slug);

        $ogImage = null;
        if (!empty($post->og_image)) {
            $ogImage = str_starts_with($post->og_image, 'http')
                ? $post->og_image
                : $baseUrl . '/' . ltrim($post->og_image, '/');
        }

        return Inertia::render('Blog/Show', [
            'baseUrl' => $baseUrl,
            'canonical' => $canonical,
            'title' => $post->meta_title,
            'post' => [
                'title' => $post->title,
                'slug' => $post->slug,
                'excerpt' => $post->excerpt,
                'content' => $post->content,
                'meta_description' => $post->meta_description,
                'og_image' => $ogImage,
                'published_at' => optional($post->published_at)->toDateString(),
                'updated_at' => optional($post->updated_at)->toDateString(),
            ],
        ]);
    }
}
