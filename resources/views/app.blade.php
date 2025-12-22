<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- Token CSRF para Axios / Inertia --}}
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title inertia>{{ config('app.name', 'KitchenManager') }}</title>

        @php
            $appName = config('app.name', 'KitchenManager');
            $baseUrl = rtrim(config('app.url'), '/');
            $path = '/' . ltrim(request()->path(), '/');
            $canonical = $baseUrl . ($path === '/' ? '' : $path);
            $defaultDescription = 'Gestiona productos, ubicaciones y stock de cocina con alertas y reportes.';
            $privatePatterns = [
                'login',
                'register',
                'forgot-password',
                'reset-password*',
                'confirm-password',
                'email/verification-notification',
                'verify-email',
                'password*',
                'logout',
                'dashboard',
                'profile*',
                'products*',
                'stock*',
                'locations*',
                'admin*',
                'kitchen*',
                'barcode*',
                'debug*',
            ];
            $shouldNoindex = request()->user() || request()->is($privatePatterns);
        @endphp

        <meta name="description" content="{{ $defaultDescription }}">
        <link rel="canonical" href="{{ $canonical }}">

        @if ($shouldNoindex)
            <meta name="robots" content="noindex, nofollow">
        @endif

        <meta property="og:site_name" content="{{ $appName }}">
        <meta property="og:title" content="{{ $appName }}">
        <meta property="og:description" content="{{ $defaultDescription }}">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ $canonical }}">

        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $appName }}">
        <meta name="twitter:description" content="{{ $defaultDescription }}">

        <script type="application/ld+json">
            {!! json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'WebSite',
                'name' => $appName,
                'url' => $baseUrl,
                'potentialAction' => [
                    '@type' => 'SearchAction',
                    'target' => $baseUrl . '/?q={search_term_string}',
                    'query-input' => 'required name=search_term_string',
                ],
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
        </script>
        <script type="application/ld+json">
            {!! json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'Organization',
                'name' => $appName,
                'url' => $baseUrl,
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
        </script>
        <script type="application/ld+json">
            {!! json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'SoftwareApplication',
                'name' => $appName,
                'applicationCategory' => 'BusinessApplication',
                'operatingSystem' => 'Web',
                'offers' => [
                    '@type' => 'Offer',
                    'price' => '0',
                    'priceCurrency' => 'EUR',
                ],
            ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
        </script>

        {{-- ✅ FAVICON (definitivo) --}}
        <link rel="icon" href="/favicon.ico?v=1" sizes="any">
        <link rel="icon" type="image/png" href="/favicon-32x32.png?v=1" sizes="32x32">
        <link rel="icon" type="image/png" href="/favicon-16x16.png?v=1" sizes="16x16">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png?v=1">
        <link rel="shortcut icon" href="/favicon.ico?v=1">

        {{-- Carga de Vite: SOLO el JS (el CSS va importado desde app.js) --}}
        @vite('resources/js/app.js')

        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
