<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-100 font-sans text-slate-900 antialiased">
        <div class="relative min-h-screen overflow-hidden bg-slate-100">
            <div class="absolute inset-0 bg-gradient-to-br from-sky-50 via-slate-100 to-slate-100"></div>
            <div class="relative flex min-h-screen flex-col items-center justify-center px-4 py-10">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
