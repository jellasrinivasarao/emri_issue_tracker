<x-app-layout :without-sidebar="true">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Central Admin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="mb-4">{{ __('Central Admin has access to all system pages and configuration areas.') }}</p>

                    <div class="grid gap-4 md:grid-cols-2">
                        <a href="{{ route('dashboard') }}" class="block rounded-lg border border-gray-200 bg-slate-50 p-5 hover:border-indigo-500 hover:bg-indigo-50">
                            <h3 class="text-lg font-semibold">{{ __('Dashboard') }}</h3>
                            <p class="mt-2 text-sm text-gray-600">{{ __('View the main dashboard and system summaries.') }}</p>
                        </a>
                        <a href="{{ route('issues') }}" class="block rounded-lg border border-gray-200 bg-slate-50 p-5 hover:border-indigo-500 hover:bg-indigo-50">
                            <h3 class="text-lg font-semibold">{{ __('Issues') }}</h3>
                            <p class="mt-2 text-sm text-gray-600">{{ __('Open the issue grid and review issue workflows.') }}</p>
                        </a>
                        <a href="{{ route('raise.issue') }}" class="block rounded-lg border border-gray-200 bg-slate-50 p-5 hover:border-indigo-500 hover:bg-indigo-50">
                            <h3 class="text-lg font-semibold">{{ __('Raise Issue') }}</h3>
                            <p class="mt-2 text-sm text-gray-600">{{ __('Create a new issue with service and project details.') }}</p>
                        </a>
                        <a href="{{ route('reports') }}" class="block rounded-lg border border-gray-200 bg-slate-50 p-5 hover:border-indigo-500 hover:bg-indigo-50">
                            <h3 class="text-lg font-semibold">{{ __('Reports') }}</h3>
                            <p class="mt-2 text-sm text-gray-600">{{ __('Access analytic reports and dashboards.') }}</p>
                        </a>
                        <a href="{{ route('administration') }}" class="block rounded-lg border border-gray-200 bg-slate-50 p-5 hover:border-indigo-500 hover:bg-indigo-50">
                            <h3 class="text-lg font-semibold">{{ __('Administration') }}</h3>
                            <p class="mt-2 text-sm text-gray-600">{{ __('Manage system configuration, masters, and routing.') }}</p>
                        </a>
                        <a href="{{ route('state.admin') }}" class="block rounded-lg border border-gray-200 bg-slate-50 p-5 hover:border-indigo-500 hover:bg-indigo-50">
                            <h3 class="text-lg font-semibold">{{ __('State Admin') }}</h3>
                            <p class="mt-2 text-sm text-gray-600">{{ __('View and manage state administration pages.') }}</p>
                        </a>
                        <a href="{{ route('ho.admin') }}" class="block rounded-lg border border-gray-200 bg-slate-50 p-5 hover:border-indigo-500 hover:bg-indigo-50">
                            <h3 class="text-lg font-semibold">{{ __('HO Admin') }}</h3>
                            <p class="mt-2 text-sm text-gray-600">{{ __('View and manage HO administration pages.') }}</p>
                        </a>
                        <a href="{{ route('vendor.admin') }}" class="block rounded-lg border border-gray-200 bg-slate-50 p-5 hover:border-indigo-500 hover:bg-indigo-50">
                            <h3 class="text-lg font-semibold">{{ __('Vendor Admin') }}</h3>
                            <p class="mt-2 text-sm text-gray-600">{{ __('View and manage vendor administration pages.') }}</p>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
