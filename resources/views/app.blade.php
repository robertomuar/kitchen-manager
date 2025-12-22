<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @php
            $appName = config('app.name', 'KitchenManager');
            $appUrl = rtrim(config('app.url', url('/')), '/');
            $defaultDescription = 'KitchenManager te ayuda a controlar el stock de tu cocina, planificar reposiciones y evitar desperdicio.';
            $canonicalUrl = url()->current();
            $ogImage = $appUrl . '/sombrero.png';
            $jsonLd = [
                '@context' => 'https://schema.org',
                '@graph' => [
                    [
                        '@type' => 'Organization',
                        'name' => $appName,
                        'url' => $appUrl,
                        'logo' => $ogImage,
                    ],
                    [
                        '@type' => 'WebSite',
                        'name' => $appName,
                        'url' => $appUrl,
                        'potentialAction' => [
                            '@type' => 'SearchAction',
                            'target' => $appUrl . '/?search={search_term_string}',
                            'query-input' => 'required name=search_term_string',
                        ],
                    ],
                ],
            ];
        @endphp

        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- Token CSRF para Axios / Inertia --}}
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title inertia>{{ $appName }}</title>
        <meta name="description" content="{{ $defaultDescription }}">
        <link rel="canonical" href="{{ $canonicalUrl }}">

        <meta property="og:site_name" content="{{ $appName }}">
        <meta property="og:title" content="{{ $appName }}">
        <meta property="og:description" content="{{ $defaultDescription }}">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ $canonicalUrl }}">
        <meta property="og:image" content="{{ $ogImage }}">
        <meta property="og:locale" content="es_ES">

        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $appName }}">
        <meta name="twitter:description" content="{{ $defaultDescription }}">
        <meta name="twitter:image" content="{{ $ogImage }}">

        <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>

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
