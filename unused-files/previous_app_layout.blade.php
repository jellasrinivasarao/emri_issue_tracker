<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net" />
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-100 text-slate-900">
        <div x-data="{ sidebarOpen: false }" class="min-h-screen">
            <div class="md:flex">
                @unless($withoutSidebar)
                <aside class="fixed inset-y-0 left-0 z-40 hidden w-[260px] flex-col overflow-hidden bg-slate-950 text-slate-100 shadow-xl md:flex">
                    <div class="border-b border-slate-800 px-6 py-5">
                        <div class="flex items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-900 text-lg font-semibold">E</div>
                            <div>
                                <p class="text-xs uppercase tracking-[0.24em] text-slate-500">EMRI</p>
                                <p class="mt-1 text-base font-semibold text-white">Issue Tracker</p>
                            </div>
                        </div>
                    </div>
                    <nav class="flex flex-1 flex-col overflow-y-auto px-3 py-4">
                        <div class="space-y-1">
                            @foreach(auth()->user()->menus as $menu)
                                @php
                                    $isActive = $menu->route_name ? request()->routeIs($menu->route_name) : false;
                                    $href = $menu->route_name && Route::has($menu->route_name) ? route($menu->route_name) : '#';
                                @endphp
                                <a href="{{ $href }}" class="flex items-center gap-3 rounded-[10px] px-4 py-3 text-sm font-semibold transition {{ $isActive ? 'bg-slate-800 text-white' : 'text-slate-200 hover:bg-slate-800 hover:text-white' }}">
                                    <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $menu->icon ?? 'M4 6h16M4 12h16M4 18h16' }}"></path></svg>
                                    <span>{{ $menu->display_name }}</span>
                                </a>
                            @endforeach
                        </div>
                    </nav>
                    <div class="border-t border-slate-800 px-6 py-4">
                        <button class="flex w-full items-center justify-center gap-2 rounded-lg border border-slate-700 bg-slate-900 px-3 py-2 text-sm font-semibold text-slate-200 transition hover:bg-slate-800">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                            Collapse menu
                        </button>
                    </div>
                </aside>
                @endunless

                <div class="relative flex flex-1 flex-col {{ $withoutSidebar ? '' : 'md:pl-[260px]' }}">
                    <header class="sticky top-0 z-30 flex h-[60px] items-center justify-between gap-3 border-b border-slate-200 bg-white px-4 shadow-sm">
                        <div class="flex items-center gap-3">
                            <button @click="sidebarOpen = !sidebarOpen" class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-50 text-slate-700 md:hidden">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                            </button>
                            <div class="hidden items-center gap-3 md:flex">
                                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-950 text-sm font-semibold text-white">EM</div>
                                <div>
                                    <p class="text-xs uppercase tracking-[0.24em] text-slate-500">EMRI ISSUE TRACKER</p>
                                </div>
                            </div>
                        </div>
                        @isset($header)
                            <div class="flex flex-1 justify-center px-2">
                                <h1 class="truncate text-sm font-semibold text-slate-900 md:text-base">{{ $header }}</h1>
                            </div>
                        @endisset
                        <div class="flex items-center gap-3">
                            <div class="hidden rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700 lg:flex">
                                Role: <span class="font-semibold text-slate-900">{{ auth()->user()->role_names ?: 'Central Admin' }}</span>
                            </div>
                            <button class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-50 text-slate-700 shadow-sm hover:bg-slate-100">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            </button>
                            <div class="hidden sm:flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-900">
                                <div class="flex h-9 w-9 items-center justify-center rounded-full bg-slate-900 text-sm font-semibold text-white">{{ strtoupper(substr(auth()->user()->name ?? auth()->user()->login_id, 0, 2)) }}</div>
                                <div class="text-left">
                                    <p class="font-semibold">{{ auth()->user()->name ?? auth()->user()->login_id }}</p>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="text-[11px] text-slate-500 hover:text-slate-700">Logout</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </header>

                    <main class="min-h-[calc(100vh-60px)] overflow-auto bg-slate-100">
                        <div class="mx-auto max-w-[1760px] px-4 py-5 sm:px-6 lg:px-8">
                            {{ $slot }}
                        </div>
                    </main>
                </div>
            </div>

            <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-20 bg-slate-900/50 transition-opacity duration-200 md:hidden" @click="sidebarOpen = false"></div>
            <aside x-show="sidebarOpen" x-cloak @click.away="sidebarOpen = false" class="fixed inset-y-0 left-0 z-30 w-[260px] overflow-y-auto bg-slate-950 text-slate-100 shadow-xl md:hidden">
                <div class="border-b border-slate-800 px-6 py-5">
                    <div class="flex items-center gap-3">
                        <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-900 text-lg font-semibold">E</div>
                        <div>
                            <p class="text-xs uppercase tracking-[0.24em] text-slate-500">EMRI</p>
                            <p class="mt-1 text-base font-semibold text-white">Issue Tracker</p>
                        </div>
                    </div>
                </div>
                <nav class="flex flex-1 flex-col px-3 py-4">
                    <div class="space-y-1">
                        @foreach(auth()->user()->menus as $menu)
                            @php
                                $isActive = $menu->route_name ? request()->routeIs($menu->route_name) : false;
                                $href = $menu->route_name && Route::has($menu->route_name) ? route($menu->route_name) : '#';
                            @endphp
                            <a href="{{ $href }}" class="flex items-center gap-3 rounded-[10px] px-4 py-3 text-sm font-semibold transition {{ $isActive ? 'bg-slate-800 text-white' : 'text-slate-200 hover:bg-slate-800 hover:text-white' }}">
                                <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $menu->icon ?? 'M4 6h16M4 12h16M4 18h16' }}"></path></svg>
                                <span>{{ $menu->display_name }}</span>
                            </a>
                        @endforeach
                    </div>
                </nav>
            </aside>
        </div>
    </body>
</html>
