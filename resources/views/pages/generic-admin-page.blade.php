<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $title ?? 'Administration' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="mb-4 text-sm text-slate-600">{{ $description ?? 'Manage this configuration area.' }}</p>

                    <div class="space-y-4">
                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6">
                            <h3 class="text-lg font-semibold text-slate-900">{{ $title ?? 'Admin Page' }}</h3>
                            <p class="mt-2 text-sm text-slate-600">This page is generated from the generic admin page template. You can replace this placeholder with the actual section implementation.</p>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                                <h4 class="font-semibold text-slate-900">Configuration</h4>
                                <p class="mt-2 text-sm text-slate-600">Add form fields, tables, and buttons here for the {{ strtolower($title ?: 'configuration') }} section.</p>
                            </div>
                            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                                <h4 class="font-semibold text-slate-900">Access Control</h4>
                                <p class="mt-2 text-sm text-slate-600">This route is protected by menu access and will display only to authorized roles.</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
