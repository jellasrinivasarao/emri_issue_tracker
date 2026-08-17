<!DOCTYPE html>

<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full m-0 overflow-x-hidden" style="height:100%;margin:0;padding:0;">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <title>{{ config('app.name', 'Laravel') }}</title>
        
        <!-- Level 2: Prevent Browser Back/Forward - Logout on Any Back/Forward Attempt -->
        <script>
            (function () {
                // Store a unique page marker to detect navigation
                const pageMarker = Math.random().toString(36).substr(2, 9);
                sessionStorage.setItem('currentPageMarker', pageMarker);

                // Replace current history state
                history.replaceState({ pageMarker: pageMarker }, null, location.href);

                // Listen for back/forward attempts via popstate
                window.addEventListener('popstate', function (event) {
                    // Check if we're navigating backwards in history
                    const previousMarker = sessionStorage.getItem('currentPageMarker');
                    if (!event.state || event.state.pageMarker !== previousMarker) {
                        // User tried to go back/forward - logout immediately
                        performLogout();
                    }
                });

                // Detect page restoration from browser cache (back/forward cache)
                window.addEventListener('pageshow', function (event) {
                    if (event.persisted) {
                        // Page was restored from bfcache - user used back/forward
                        console.log('Page restored from back/forward cache - logging out');
                        performLogout();
                    }
                });

                // Also detect pagehide to prevent caching
                window.addEventListener('pagehide', function (event) {
                    if (event.persisted) {
                        // Browser is putting page in bfcache - prevent by logging out
                        performLogout();
                    }
                });

                function performLogout() {
                    // Clear browser-side application data
                    try {
                        sessionStorage.clear();
                        localStorage.clear();
                    } catch (e) {
                        console.log('Error clearing storage:', e);
                    }

                    // Force logout by submitting a POST request to the logout route
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("logout") }}';
                    
                    // Add CSRF token
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                    
                    form.appendChild(csrfInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            })();
        </script>
        
        <link rel="preconnect" href="https://fonts.bunny.net" />
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
        <style>
            html, body {
                margin: 0 !important;
                padding: 0 !important;
                border: 0 !important;
                height: 100%;
                box-sizing: border-box;
            }
>>>>>>> a85f646f950487e608c82a39ede3ba671fd6d601

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net" />
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    <style>
    html,
    body {
        margin: 0 !important;
        padding: 0 !important;
        border: 0 !important;
        height: 100%;
        box-sizing: border-box;
    }

    *,
    *::before,
    *::after {
        box-sizing: border-box;
    }

    body {
        margin-top: 0 !important;
        padding-top: 0 !important;
    }

    body>div:first-child {
        margin-top: 0 !important;
        padding-top: 0 !important;
    }
    </style>
</head>

<body class="h-full m-0 p-0 font-sans antialiased bg-slate-100 text-slate-900 overflow-x-hidden overflow-y-auto"
    style="min-height:100%;margin:0;padding:0;">
    @php
    $menuGroups = [
    'Main Menu' => ['dashboard', 'role.issue.dashboard', 'issues', 'raise.issue', 'reports'],
    'Administration' => ['administration'],
    'Main Dashboard' => ['role.dashboard'],
    'Admin Teams' => ['state.admin', 'ho.admin', 'vendor.admin'],
    'Organisation Setup' => ['state.master', 'vendor.master', 'service.master', 'project.master', 'application.master',
    'module.master', 'support-group.master', 'project.application.module.mapping', 'project.state.mapping',
    'vendor.state.mapping'],
    'User & Security' => ['user.master', 'role.master', 'privilege.master', 'user.role.mapping', 'user.project.mapping',
    'user.support.group.mapping', 'menu.master', 'role.menu.mapping', 'role.privilege.mapping','mail.configuration'],
    'Operational Configuration' => ['working-schedules', 'holiday.calendar', 'sla.configuration', 'automatic.routing',
    'notification.configuration', 'priority.configuration', 'severity.configuration', 'issue.category.configuration',
    'vendor.level2.mapping'],
    'Audit & Governance' => ['active.inactive.status', 'change.history', 'user.activity.log', 'system.audit.logs'],
    ];

    $menus = auth()->user()->menus;

    $groupedMenus = collect($menuGroups)->mapWithKeys(function ($routeNames, $section) use ($menus) {
    return [$section => $menus->filter(fn($menu) => in_array($menu->route_name, $routeNames))->values()];
    })->filter(fn($items) => $items->isNotEmpty());

    $ungrouped = $menus->reject(fn($menu) => collect($menuGroups)->flatten()->contains($menu->route_name))->values();

    $sectionOpen = collect($menuGroups)->mapWithKeys(function ($routeNames, $section) {
    return [$section => collect($routeNames)->contains(fn($routeName) => request()->routeIs($routeName))];
    });

    $adminAccordionRoutes = collect($menuGroups['Administration'])
    ->merge($menuGroups['Admin Teams'])
    ->merge($menuGroups['Organisation Setup'])
    ->merge($menuGroups['User & Security'])
    ->merge($menuGroups['Operational Configuration'])
    ->merge($menuGroups['Audit & Governance']);

    $sectionOpen['Administration'] = $adminAccordionRoutes->contains(fn($routeName) => request()->routeIs($routeName));

    $resolveMenuRoute = function($routeName) {
    if (!$routeName) {
    return null;
    }
    if (Route::has($routeName)) {
    return $routeName;
    }
    return Route::has($routeName . '.index') ? $routeName . '.index' : null;
    };

    $isMenuActive = function($menuRouteName, $resolvedRouteName) {
    if (!$menuRouteName || !$resolvedRouteName) {
    return false;
    }
    if (Route::has($menuRouteName)) {
    return request()->routeIs($menuRouteName);
    }
    return request()->routeIs($menuRouteName . '*');
    };
    @endphp

    @php $withoutSidebar = $withoutSidebar ?? false; @endphp

    <div class="min-h-screen bg-slate-100 flex flex-col m-0 p-0"
        style="margin:0 !important;padding:0 !important;box-sizing:border-box;"
        x-data="{ sidebarOpen: false, openSections: {{ $sectionOpen->toJson() }}, noSidebar: {{ $withoutSidebar ? 'true' : 'false' }}, init() { window.addEventListener('layout:sidebar', e => { this.noSidebar = !!e.detail.noSidebar }) } }">
        <header class="sticky top-0 z-50 m-0 border-b border-slate-200 bg-white shadow-sm"
            style="position:sticky;top:0;left:0;right:0;margin:0;padding:0;">
            <div class="flex min-h-[64px] w-full flex-wrap items-center justify-between gap-3 px-3 sm:px-4 lg:px-6">
                <div class="flex min-w-0 items-center gap-3">
                    <button @click="sidebarOpen = !sidebarOpen"
                        class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-50 text-slate-700 md:hidden">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <div class="hidden items-center gap-3 md:flex">
                        <div
                            class="flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-950 text-sm font-semibold text-white">
                            EM</div>
                        <div>
                            <p class="text-xs uppercase tracking-[0.24em] text-slate-500">EMRI ISSUE TRACKER</p>
                        </div>
                    </div>
                </div>
                <div class="ml-auto flex min-w-0 flex-wrap items-center gap-3">
                    <div
                        class="inline-flex items-center rounded-full bg-gradient-to-r from-sky-500 via-indigo-500 to-violet-500 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-slate-900/10">
                        <span
                            class="mr-2 rounded-full bg-white/20 px-2 py-0.5 text-[10px] uppercase tracking-[0.30em] text-white">Role</span>
                        <span>{{ auth()->user()->role_names ?: 'Central Admin' }}</span>
                    </div>

                    <button
                        class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-50 text-slate-700 shadow-sm hover:bg-slate-100">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                            </path>
                        </svg>
                    </button>
                    <div
                        class="flex min-w-0 items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-900">
                        <div
                            class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-slate-900 text-sm font-semibold text-white">
                            {{ strtoupper(substr(auth()->user()->name ?? auth()->user()->login_id, 0, 2)) }}</div>
                        <div class="min-w-0 text-left">
                            <p class="truncate max-w-[160px] font-semibold text-slate-900">
                                {{ auth()->user()->name ?? auth()->user()->login_id }}</p>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                    class="text-[11px] text-slate-500 hover:text-slate-700">Logout</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <div class="flex-1 {{ $withoutSidebar ? '' : 'md:flex' }}">

            @php
            $mdFlexClass = $withoutSidebar ? '' : 'md:flex';
            $mobileHiddenClass = $withoutSidebar ? 'hidden' : '';
            @endphp
            <aside x-show="!noSidebar" x-cloak
                class="hidden w-[260px] flex flex-col min-h-0 bg-[#071837] text-white shadow-xl {{ $mdFlexClass }}">
                <div class="border-b border-[#102658] px-6 py-5">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#0f204d] text-lg font-semibold">
                            E</div>
                        <div>
                            <p class="text-[11px] uppercase tracking-[0.28em] text-slate-400">EMRI</p>
                            <p class="mt-1 text-base font-semibold text-white">Issue Tracker</p>
                        </div>
                    </div>
                </div>
                <nav class="flex flex-1 flex-col overflow-y-auto px-3 py-5">
                    <div class="space-y-1">
                        @foreach($groupedMenus->get('Main Menu', collect()) as $menu)
                        @php
                        $resolvedRouteName = $resolveMenuRoute($menu->route_name);
                        $isActive = $isMenuActive($menu->route_name, $resolvedRouteName);
                        $href = $resolvedRouteName ? route($resolvedRouteName) : '#';
                        @endphp
                        <a href="{{ $href }}" data-no-ajax="true"
                            class="flex items-center gap-3 rounded-[16px] px-4 py-3 text-sm font-semibold transition {{ $isActive ? 'bg-[#103d7f] text-white shadow-sm' : 'text-slate-200 hover:bg-[#102c56] hover:text-white' }}">
                            <span
                                class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-900/70 text-slate-300">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="{{ $menu->icon ?? 'M4 6h16M4 12h16M4 18h16' }}"></path>
                                </svg>
                            </span>
                            <span>{{ $menu->display_name }}</span>
                        </a>
                        @endforeach
                    </div>

                    <div class="mt-5 space-y-3">
                        @php
                        $mainDashboardRouteName = 'role.dashboard';
                        $mainDashboardHref = Route::has($mainDashboardRouteName) ? route($mainDashboardRouteName) : '#';
                        @endphp
                        <a href="{{ $mainDashboardHref }}" data-no-ajax="true"
                            class="flex items-center gap-3 rounded-[16px] bg-[#081a3b] px-4 py-3 text-sm font-semibold text-slate-200 transition hover:bg-[#102c56] hover:text-white">
                            <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" stroke="currentColor"
                                stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3 12l9-9 9 9M5 10v10h5V13h4v7h5V10"></path>
                            </svg>
                            <span>Main Dashboard</span>
                        </a>

                        @php
                        $adminSections = ['Admin Teams','Organisation Setup','User & Security','Operational
                        Configuration','Audit & Governance'];
                        $adminItems = collect($adminSections)->flatMap(fn($section) => $groupedMenus->get($section) ??
                        collect());
                        @endphp
                        @if($adminItems->isNotEmpty())
                        <div class="rounded-[18px] border border-[#102858] bg-[#081a3b]">
                            <button @click="openSections['Administration'] = !openSections['Administration']"
                                class="flex w-full items-center justify-between gap-2 px-4 py-3 text-left text-sm font-semibold uppercase tracking-[0.18em] text-slate-400 transition hover:text-white">
                                <span>Administration</span>
                                <svg :class="openSections['Administration'] ? 'rotate-90' : ''"
                                    class="h-4 w-4 transform transition-transform duration-200 text-slate-400"
                                    fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                            <div x-show="openSections['Administration']" x-cloak
                                class="space-y-3 border-t border-[#102858] px-3 pb-3 pt-2">
                                @foreach($adminSections as $section)
                                @if($groupedMenus->has($section))
                                <div class="rounded-[18px] border border-[#102858] bg-[#081a3b]">
                                    <button @click="openSections['{{ $section }}'] = !openSections['{{ $section }}']"
                                        class="flex w-full items-center justify-between gap-2 px-4 py-3 text-left text-sm font-semibold uppercase tracking-[0.18em] text-slate-400 transition hover:text-white">
                                        <span>{{ $section }}</span>
                                        <svg :class="openSections['{{ $section }}'] ? 'rotate-90' : ''"
                                            class="h-4 w-4 transform transition-transform duration-200 text-slate-400"
                                            fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7">
                                            </path>
                                        </svg>
                                    </button>
                                    <div x-show="openSections['{{ $section }}']" x-cloak
                                        class="space-y-1 border-t border-[#102858] px-3 pb-3 pt-2">
                                        @foreach($groupedMenus->get($section) as $menu)
                                        @php
                                        $resolvedRouteName = $resolveMenuRoute($menu->route_name);
                                        $isActive = $isMenuActive($menu->route_name, $resolvedRouteName);
                                        $href = $resolvedRouteName ? route($resolvedRouteName) : '#';
                                        @endphp
                                        <a href="{{ $href }}" data-no-ajax="true"
                                            class="flex items-center gap-3 rounded-[14px] px-4 py-2 text-sm font-medium transition {{ $isActive ? 'bg-[#102c56] text-white' : 'text-slate-300 hover:bg-[#102c56] hover:text-white' }}">
                                            <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none"
                                                stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="{{ $menu->icon ?? 'M4 6h16M4 12h16M4 18h16' }}"></path>
                                            </svg>
                                            <span>{{ $menu->display_name }}</span>
                                        </a>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </nav>

            </aside>

            <div class="relative m-0 flex flex-1 flex-col min-h-0 box-border p-0">
                <div id="page-shell">
                    <main class="flex-1 min-h-0 overflow-y-auto bg-slate-100">
                        <div id="page-content-wrapper"
                            class="mx-auto flex max-w-full w-full flex-col box-border px-4 sm:px-6 lg:px-8 overflow-x-hidden">
                            @if(isset($slot))
                            {{ $slot }}
                            @else
                            @yield('content')
                            @endif
                            @stack('scripts')
                        </div>
                    </main>
                </div>
            </div>
        </div>

        <div x-show="sidebarOpen" x-cloak
            class="fixed inset-0 z-20 bg-slate-900/50 transition-opacity duration-200 md:hidden"
            @click="sidebarOpen = false"></div>
        <aside x-show="sidebarOpen && !noSidebar" x-cloak @click.away="sidebarOpen = false"
            class="fixed inset-y-0 left-0 z-30 w-[260px] overflow-y-auto bg-[#071837] text-slate-100 shadow-xl md:hidden {{ $mobileHiddenClass }}">
            <div class="border-b border-[#102658] px-6 py-6">
                <div class="flex items-center gap-3">
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[#0f204d] text-lg font-semibold">
                        E</div>
                    <div>
                        <p class="text-xs uppercase tracking-[0.26em] text-slate-500">EMRI</p>
                        <p class="mt-1 text-base font-semibold text-white">Issue Tracker</p>
                    </div>
                </div>
            </div>
            <nav class="flex flex-1 flex-col px-3 py-4">
                <div class="space-y-1">
                    @foreach($groupedMenus->get('Main Menu', collect()) as $menu)
                    @php
                    $resolvedRouteName = $resolveMenuRoute($menu->route_name);
                    $isActive = $isMenuActive($menu->route_name, $resolvedRouteName);
                    $href = $resolvedRouteName ? route($resolvedRouteName) : '#';
                    @endphp
                    <a href="{{ $href }}" data-no-ajax="true"
                        class="flex items-center gap-3 rounded-[14px] px-4 py-3 text-sm font-semibold transition {{ $isActive ? 'bg-[#14417a] text-white' : 'text-slate-200 hover:bg-[#102c56] hover:text-white' }}">
                        <svg class="h-4 w-4 shrink-0 text-slate-300" fill="none" stroke="currentColor"
                            stroke-width="1.75" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="{{ $menu->icon ?? 'M4 6h16M4 12h16M4 18h16' }}"></path>
                        </svg>
                        <span>{{ $menu->display_name }}</span>
                    </a>
                    @endforeach
                </div>

                <div class="mt-4 space-y-3">
                    @php
                    $mainDashboardRouteName = 'role.dashboard';
                    $mainDashboardHref = Route::has($mainDashboardRouteName) ? route($mainDashboardRouteName) : '#';
                    @endphp
                    <a href="{{ $mainDashboardHref }}" data-no-ajax="true"
                        class="flex items-center gap-3 rounded-[14px] bg-[#0b1e47] px-4 py-3 text-sm font-semibold text-slate-200 transition hover:bg-[#102c56] hover:text-white">
                        <svg class="h-4 w-4 shrink-0 text-slate-300" fill="none" stroke="currentColor"
                            stroke-width="1.75" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3 12l9-9 9 9M5 10v10h5V13h4v7h5V10"></path>
                        </svg>
                        <span>Main Dashboard</span>
                    </a>

                    @php
                    $adminSections = ['Admin Teams','Organisation Setup','User & Security','Operational
                    Configuration','Audit & Governance'];
                    $adminItems = collect($adminSections)->flatMap(fn($section) => $groupedMenus->get($section) ??
                    collect());
                    @endphp
                    @if($adminItems->isNotEmpty())
                    <div class="rounded-[20px] border border-transparent bg-[#0b1e47]">
                        <button @click="openSections['Administration'] = !openSections['Administration']"
                            class="flex w-full items-center justify-between gap-2 px-4 py-3 text-left text-sm font-semibold uppercase tracking-[0.18em] text-slate-400 transition hover:text-white">
                            <span>Administration</span>
                            <svg :class="openSections['Administration'] ? 'rotate-90' : ''"
                                class="h-4 w-4 transform transition-transform duration-200 text-slate-400" fill="none"
                                stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                        <div x-show="openSections['Administration']" x-cloak
                            class="space-y-3 border-t border-slate-800 px-3 pb-3 pt-2">
                            @foreach($adminSections as $section)
                            @if($groupedMenus->has($section))
                            <div class="rounded-[18px] border border-[#102858] bg-[#081a3b]">
                                <button @click="openSections['{{ $section }}'] = !openSections['{{ $section }}']"
                                    class="flex w-full items-center justify-between gap-2 px-4 py-3 text-left text-sm font-semibold uppercase tracking-[0.18em] text-slate-400 transition hover:text-white">
                                    <span>{{ $section }}</span>
                                    <svg :class="openSections['{{ $section }}'] ? 'rotate-90' : ''"
                                        class="h-4 w-4 transform transition-transform duration-200 text-slate-400"
                                        fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                                <div x-show="openSections['{{ $section }}']" x-cloak
                                    class="space-y-1 border-t border-slate-800 px-3 pb-3 pt-2">
                                    @foreach($groupedMenus->get($section) as $menu)
                                    @php
                                    $resolvedRouteName = $resolveMenuRoute($menu->route_name);
                                    $isActive = $isMenuActive($menu->route_name, $resolvedRouteName);
                                    $href = $resolvedRouteName ? route($resolvedRouteName) : '#';
                                    @endphp
                                    <a href="{{ $href }}" data-no-ajax="true"
                                        class="flex items-center gap-3 rounded-[14px] px-4 py-2 text-sm font-medium transition {{ $isActive ? 'bg-[#102c56] text-white' : 'text-slate-300 hover:bg-[#102c56] hover:text-white' }}">
                                        <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" stroke="currentColor"
                                            stroke-width="1.75" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="{{ $menu->icon ?? 'M4 6h16M4 12h16M4 18h16' }}"></path>
                                        </svg>
                                        <span>{{ $menu->display_name }}</span>
                                    </a>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </nav>
        </aside>
        @stack('scripts')
    </div>
    <script>
    function setHeaderHeightVar() {
        const header = document.querySelector('header');
        if (!header) return;
        const h = header.offsetHeight || 64;
        document.documentElement.style.setProperty('--header-height', h + 'px');
    }
    window.addEventListener('load', setHeaderHeightVar);
    window.addEventListener('resize', setHeaderHeightVar);
    // Also run shortly after load in case fonts/layout change
    setTimeout(setHeaderHeightVar, 250);
    </script>
</body>

</html>