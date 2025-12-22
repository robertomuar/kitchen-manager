<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NoIndex
{
    public function handle(Request $request, Closure $next, string $robots = 'noindex, nofollow'): Response
    {
        $request->attributes->set('seo_robots', $robots);

        /** @var Response $response */
        $response = $next($request);

        return $response->header('X-Robots-Tag', $robots);
    }
}
