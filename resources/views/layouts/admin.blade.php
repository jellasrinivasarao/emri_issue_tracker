<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        @yield('title', 'Centralised Admin')
    </title>

    @vite([
    'resources/css/app.css',
    'resources/js/app.js'
    ])

    @stack('styles')

</head>

<body>

    @include('layouts.partials.header')

    @include('layouts.partials.sidebar')

    <main class="main-content">

        @include('layouts.partials.alerts')

        @yield('content')

    </main>

    @stack('scripts')

</body>

</html>