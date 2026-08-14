<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ $title ?? 'Role–Menu Mapping' }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 bg-slate-50 px-5 py-5">
                    <div class="grid gap-4 md:grid-cols-[1fr_auto] md:items-center">
                        <p class="text-sm text-slate-600">{{ $description ?? 'Manage role permissions and menu access mapping.' }}</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-100">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-900">Role</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-900">Menu</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-900">Route</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-900">URI</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-900">Allowed</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse($mappings as $mapping)
                                <tr>
                                    <td class="px-5 py-3 text-sm font-semibold text-slate-900">{{ $mapping->role_name }}</td>
                                    <td class="px-5 py-3 text-sm text-slate-600">{{ $mapping->menu_name }}</td>
                                    <td class="px-5 py-3 text-sm text-slate-600">{{ $mapping->route_name }}</td>
                                    <td class="px-5 py-3 text-sm text-slate-600">{{ $mapping->uri ?? '-' }}</td>
                                    <td class="px-5 py-3 text-sm">{{ $mapping->is_allowed ? 'Yes' : 'No' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-6 text-center text-sm text-slate-500">No role-menu mappings found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
