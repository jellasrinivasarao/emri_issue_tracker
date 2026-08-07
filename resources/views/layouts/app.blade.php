<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full overflow-x-hidden">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net" />
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('styles')
    </head>
    <body class="h-full m-0 font-sans antialiased bg-slate-100 text-slate-900 overflow-x-hidden overflow-y-auto">
        <div class="min-h-screen bg-slate-100">
            @php
                $menuGroups = [
                    'Main Menu' => ['dashboard', 'issues', 'raise.issue', 'reports'],
                    'Administration' => ['administration'],
                    'Main Dashboard' => ['role.dashboard'],
                    'Admin Teams' => ['state.admin', 'ho.admin', 'vendor.admin'],
                    'Organisation Setup' => ['state.master', 'vendor.master', 'service.master', 'project.master', 'application.master', 'module.master', 'support-group.master', 'project.application.module.mapping', 'project.state.mapping', 'vendor.state.mapping'],
                    'User & Security' => ['user.master', 'role.master', 'privilege.master', 'user.role.mapping', 'user.project.mapping', 'user.support.group.mapping', 'menu.master', 'role.menu.mapping', 'role.privilege.mapping'],
                    'Operational Configuration' => ['working.hours', 'holiday.calendar', 'sla.configuration', 'automatic.routing', 'notification.configuration', 'priority.configuration', 'severity.configuration', 'issue.category.configuration', 'vendor.level2.mapping'],
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
            @endphp

            <div class="md:flex h-screen" x-data="{ sidebarOpen: false, openSections: {{ $sectionOpen->toJson() }} }">
                @unless($withoutSidebar)
                <aside class="fixed inset-y-0 left-0 z-20 hidden w-[260px] flex-col overflow-hidden border-r border-slate-200 bg-white text-slate-700 shadow-[0_12px_35px_-15px_rgba(15,23,42,0.25)] md:flex">
                    <div class="border-b border-slate-200 px-5 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-900 text-sm font-semibold text-white">EM</div>
                            <div class="min-w-0">
                                <p class="text-[10px] font-semibold uppercase tracking-[0.3em] text-slate-400">EMRI</p>
                                <p class="truncate text-sm font-semibold text-slate-900">Issue Tracker</p>
                            </div>
                        </div>
                    </div>
                    <nav class="flex flex-1 flex-col overflow-y-auto px-3 py-4">
                        <div class="space-y-1">
                            @foreach($groupedMenus->get('Main Menu', collect()) as $menu)
                                @php
                                    $isActive = $menu->route_name ? request()->routeIs($menu->route_name) : false;
                                    $href = $menu->route_name && Route::has($menu->route_name) ? route($menu->route_name) : '#';
                                @endphp
                                <a href="{{ $href }}" class="flex items-center gap-3 rounded-[14px] px-3 py-2.5 text-sm font-medium transition {{ $isActive ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-2xl bg-white text-slate-500 shadow-sm {{ $isActive ? 'text-white bg-slate-800' : '' }}">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $menu->icon ?? 'M4 6h16M4 12h16M4 18h16' }}"></path></svg>
                                    </span>
                                    <span>{{ $menu->display_name }}</span>
                                </a>
                            @endforeach
                        </div>

                        <div class="mt-4 rounded-[18px] border border-slate-200 bg-slate-50/80 p-2">
                            @php
                                $mainDashboardRouteName = 'role.dashboard';
                                $mainDashboardHref = Route::has($mainDashboardRouteName) ? route($mainDashboardRouteName) : '#';
                            @endphp
                            <a href="{{ $mainDashboardHref }}" class="flex items-center gap-3 rounded-[14px] px-3 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 hover:text-slate-900">
                                <svg class="h-4 w-4 shrink-0 text-slate-500" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v10h5V13h4v7h5V10"></path></svg>
                                <span>Main Dashboard</span>
                            </a>

                            @php
                                $adminSections = ['Admin Teams','Organisation Setup','User & Security','Operational Configuration','Audit & Governance'];
                                $adminItems = collect($adminSections)->flatMap(fn($section) => $groupedMenus->get($section) ?? collect());
                            @endphp
                            @if($adminItems->isNotEmpty())
                                <div class="mt-2 rounded-[16px] border border-slate-200 bg-white">
                                    <button @click="openSections['Administration'] = !openSections['Administration']" class="flex w-full items-center justify-between gap-2 px-3 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500 transition hover:text-slate-900">
                                        <span>Administration</span>
                                        <svg :class="openSections['Administration'] ? 'rotate-90' : ''" class="h-4 w-4 transform transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                                    </button>
                                    <div x-show="openSections['Administration']" x-cloak class="space-y-2 border-t border-slate-200 px-2 pb-2 pt-2">
                                        @foreach($adminSections as $section)
                                            @if($groupedMenus->has($section))
                                                <div class="rounded-[14px] border border-slate-200 bg-slate-50/70">
                                                    <button @click="openSections['{{ $section }}'] = !openSections['{{ $section }}']" class="flex w-full items-center justify-between gap-2 px-3 py-2 text-left text-sm font-semibold text-slate-600 transition hover:text-slate-900">
                                                        <span>{{ $section }}</span>
                                                        <svg :class="openSections['{{ $section }}'] ? 'rotate-90' : ''" class="h-4 w-4 transform transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                                                    </button>
                                                    <div x-show="openSections['{{ $section }}']" x-cloak class="space-y-1 border-t border-slate-200 px-2 pb-2 pt-2">
                                                        @foreach($groupedMenus->get($section) as $menu)
                                                            @php
                                                                $isActive = $menu->route_name ? request()->routeIs($menu->route_name) : false;
                                                                $href = $menu->route_name && Route::has($menu->route_name) ? route($menu->route_name) : '#';
                                                            @endphp
                                                            <a href="{{ $href }}" class="flex items-center gap-3 rounded-[12px] px-3 py-2 text-sm font-medium transition {{ $isActive ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                                                <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $menu->icon ?? 'M4 6h16M4 12h16M4 18h16' }}"></path></svg>
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
                @endunless

                <div class="relative flex flex-1 flex-col min-h-0 box-border {{ $withoutSidebar ? '' : 'md:pl-[260px]' }}">
                    <div id="page-shell">
                        <header class="sticky top-0 z-50 flex flex-wrap min-h-[48px] items-center justify-between gap-3 border-b border-slate-200 bg-white px-3 shadow-sm">
                            <div class="flex min-w-0 items-center gap-3">
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
                            <div class="ml-auto flex min-w-0 flex-wrap items-center gap-3">
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-700 flex-shrink-0">
                                    Role: <span class="font-semibold text-slate-900">{{ auth()->user()->role_names ?: 'Central Admin' }}</span>
                                </div>
                            <button class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-50 text-slate-700 shadow-sm hover:bg-slate-100">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            </button>
                            <div class="flex min-w-0 items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm text-slate-900">
                                <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-slate-900 text-sm font-semibold text-white">{{ strtoupper(substr(auth()->user()->name ?? auth()->user()->login_id, 0, 2)) }}</div>
                                <div class="min-w-0 text-left">
                                    <p class="truncate max-w-[160px] font-semibold text-slate-900">{{ auth()->user()->name ?? auth()->user()->login_id }}</p>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="text-[11px] text-slate-500 hover:text-slate-700">Logout</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </header>

                    <main class="flex-1 min-h-0 overflow-y-auto bg-slate-100">
                        <div id="page-content-wrapper" class="mx-auto flex h-full min-h-0 max-w-full w-full flex-col box-border px-4 py-5 sm:px-6 lg:px-8 overflow-x-hidden">
                            {{ $slot }}
                            @stack('scripts')
                        </div>
                    </main>
                    </div>
                </div>
            </div>

            <div x-show="sidebarOpen" x-cloak class="fixed inset-0 z-20 bg-slate-900/50 transition-opacity duration-200 md:hidden" @click="sidebarOpen = false"></div>
            <aside x-show="sidebarOpen" x-cloak @click.away="sidebarOpen = false" class="fixed inset-y-0 left-0 z-30 w-[260px] overflow-y-auto border-r border-slate-200 bg-white text-slate-700 shadow-xl md:hidden">
                <div class="border-b border-slate-200 px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-900 text-sm font-semibold text-white">EM</div>
                        <div>
                            <p class="text-[10px] font-semibold uppercase tracking-[0.3em] text-slate-400">EMRI</p>
                            <p class="mt-1 text-sm font-semibold text-slate-900">Issue Tracker</p>
                        </div>
                    </div>
                </div>
                <nav class="flex flex-1 flex-col px-3 py-4">
                    <div class="space-y-1">
                        @foreach($groupedMenus->get('Main Menu', collect()) as $menu)
                            @php
                                $isActive = $menu->route_name ? request()->routeIs($menu->route_name) : false;
                                $href = $menu->route_name && Route::has($menu->route_name) ? route($menu->route_name) : '#';
                            @endphp
                            <a href="{{ $href }}" class="flex items-center gap-3 rounded-[14px] px-3 py-2.5 text-sm font-medium transition {{ $isActive ? 'bg-slate-900 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                <span class="inline-flex h-9 w-9 items-center justify-center rounded-2xl bg-white text-slate-500 shadow-sm {{ $isActive ? 'bg-slate-800 text-white' : '' }}">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $menu->icon ?? 'M4 6h16M4 12h16M4 18h16' }}"></path></svg>
                                </span>
                                <span>{{ $menu->display_name }}</span>
                            </a>
                        @endforeach
                    </div>

                    <div class="mt-4 rounded-[18px] border border-slate-200 bg-slate-50/80 p-2">
                        @php
                            $mainDashboardRouteName = 'role.dashboard';
                            $mainDashboardHref = Route::has($mainDashboardRouteName) ? route($mainDashboardRouteName) : '#';
                        @endphp
                        <a href="{{ $mainDashboardHref }}" class="flex items-center gap-3 rounded-[14px] px-3 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 hover:text-slate-900">
                            <svg class="h-4 w-4 shrink-0 text-slate-500" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l9-9 9 9M5 10v10h5V13h4v7h5V10"></path></svg>
                            <span>Main Dashboard</span>
                        </a>

                        @php
                            $adminSections = ['Admin Teams','Organisation Setup','User & Security','Operational Configuration','Audit & Governance'];
                            $adminItems = collect($adminSections)->flatMap(fn($section) => $groupedMenus->get($section) ?? collect());
                        @endphp
                        @if($adminItems->isNotEmpty())
                            <div class="mt-2 rounded-[16px] border border-slate-200 bg-white">
                                <button @click="openSections['Administration'] = !openSections['Administration']" class="flex w-full items-center justify-between gap-2 px-3 py-2.5 text-left text-[11px] font-semibold uppercase tracking-[0.24em] text-slate-500 transition hover:text-slate-900">
                                    <span>Administration</span>
                                    <svg :class="openSections['Administration'] ? 'rotate-90' : ''" class="h-4 w-4 transform transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                                </button>
                                <div x-show="openSections['Administration']" x-cloak class="space-y-2 border-t border-slate-200 px-2 pb-2 pt-2">
                                    @foreach($adminSections as $section)
                                        @if($groupedMenus->has($section))
                                            <div class="rounded-[14px] border border-slate-200 bg-slate-50/70">
                                                <button @click="openSections['{{ $section }}'] = !openSections['{{ $section }}']" class="flex w-full items-center justify-between gap-2 px-3 py-2 text-left text-sm font-semibold text-slate-600 transition hover:text-slate-900">
                                                    <span>{{ $section }}</span>
                                                    <svg :class="openSections['{{ $section }}'] ? 'rotate-90' : ''" class="h-4 w-4 transform transition-transform duration-200" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path></svg>
                                                </button>
                                                <div x-show="openSections['{{ $section }}']" x-cloak class="space-y-1 border-t border-slate-200 px-2 pb-2 pt-2">
                                                    @foreach($groupedMenus->get($section) as $menu)
                                                        @php
                                                            $isActive = $menu->route_name ? request()->routeIs($menu->route_name) : false;
                                                            $href = $menu->route_name && Route::has($menu->route_name) ? route($menu->route_name) : '#';
                                                        @endphp
                                                        <a href="{{ $href }}" class="flex items-center gap-3 rounded-[12px] px-3 py-2 text-sm font-medium transition {{ $isActive ? 'bg-slate-900 text-white' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}">
                                                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $menu->icon ?? 'M4 6h16M4 12h16M4 18h16' }}"></path></svg>
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
    </body>
</html>
