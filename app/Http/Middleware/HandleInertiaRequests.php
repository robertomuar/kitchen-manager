<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'app' => [
                'name' => config('app.name'),
                'url' => rtrim(config('app.url'), '/'),
            ],
            'auth' => [
                'user' => fn () => $request->user()
                    ? [
                        'id'       => $request->user()->id,
                        'name'     => $request->user()->name,
                        'email'    => $request->user()->email,
                        'is_admin' => (bool) ($request->user()->is_admin ?? false),
                    ]
                    : null,
            ],
        ]);
    }
}
