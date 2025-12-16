<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- Token CSRF para Axios / Inertia --}}
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title inertia>{{ config('app.name', 'KitchenManager') }}</title>

        {{-- ✅ FAVICON (icono pestaña navegador) --}}
        <link rel="icon" type="image/png" href="{{ asset('sombrero.png') }}?v=1">
        <link rel="apple-touch-icon" href="{{ asset('sombrero.png') }}?v=1">

        {{-- Carga de Vite: SOLO el JS (el CSS va importado desde app.js) --}}
        @vite('resources/js/app.js')

        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
