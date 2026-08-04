<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ $title ?? 'User–Role Mapping' }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 bg-slate-50 px-5 py-5">
                    <p class="text-sm text-slate-600">{{ $description ?? 'Map users to roles and manage user role assignments.' }}</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-100">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-900">User</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-900">Login ID</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-900">Role</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-900">Active</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse($mappings as $mapping)
                                <tr>
                                    <td class="px-5 py-3 text-sm font-semibold text-slate-900">{{ $mapping->user_name }}</td>
                                    <td class="px-5 py-3 text-sm text-slate-600">{{ $mapping->login_id }}</td>
                                    <td class="px-5 py-3 text-sm text-slate-600">{{ $mapping->role_name }}</td>
                                    <td class="px-5 py-3 text-sm">{{ $mapping->is_active ? 'Yes' : 'No' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-6 text-center text-sm text-slate-500">No user-role mappings found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
