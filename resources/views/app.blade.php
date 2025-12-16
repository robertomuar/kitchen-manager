<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- Token CSRF para Axios / Inertia --}}
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title inertia>{{ config('app.name', 'KitchenManager') }}</title>

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
