@php
    use Illuminate\Support\Facades\Route;
@endphp

<x-app-layout :without-sidebar="true">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ auth()->user()->role_names ?: 'Role Dashboard' }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.22em] text-slate-500">Role-based landing</p>
                        <h1 class="mt-2 text-3xl font-semibold text-slate-900">{{ auth()->user()->name ?? auth()->user()->login_id }}</h1>
                        <p class="mt-3 text-sm text-slate-600">This page shows the menu items available for your assigned role.</p>
                    </div>
                    <div class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-4 py-3 text-sm font-semibold text-slate-700">
                        {{ auth()->user()->role_names ?: 'No role assigned' }}
                    </div>
                </div>
            </div>

            <div class="mt-6 rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-900">Menu access for your role</h3>
                        <p class="mt-1 text-sm text-slate-600">Select a permitted section from the list below.</p>
                    </div>
                    <div class="rounded-full bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700">
                        {{ auth()->user()->menus->count() }} items
                    </div>
                </div>

                <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    @forelse(auth()->user()->menus as $menu)
                        <a href="{{ $menu->route_name && Route::has($menu->route_name) ? route($menu->route_name) : ($menu->uri ?? '#') }}" class="group rounded-[22px] border border-slate-200 bg-slate-50 p-6 transition hover:border-slate-300 hover:bg-slate-100">
                            <div class="flex items-center justify-between gap-3">
                                <div class="rounded-2xl bg-slate-900 p-3 text-white shadow-sm">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $menu->icon ?? 'M4 6h16M4 12h16M4 18h16' }}"></path></svg>
                                </div>
                                <span class="rounded-full bg-white px-3 py-1 text-[10px] font-semibold uppercase tracking-[0.24em] text-slate-500">Open</span>
                            </div>
                            <h4 class="mt-6 text-xl font-semibold text-slate-900">{{ $menu->display_name }}</h4>
                            <p class="mt-2 text-sm text-slate-600">{{ $menu->route_name ?? $menu->uri }}</p>
                        </a>
                    @empty
                        <div class="rounded-3xl border border-slate-200 bg-slate-50 p-8 text-slate-600 shadow-sm">
                            <h4 class="text-lg font-semibold text-slate-900">No menu access assigned</h4>
                            <p class="mt-2 text-sm">Contact your administrator to assign role-based menu permissions.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
