<x-app-layout>
    <x-slot name="header"><h2 class="text-xl font-semibold text-gray-800">{{ $title }}</h2></x-slot>
    <div class="py-8"><div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="border-b border-slate-200 bg-slate-50 px-5 py-5">
                <div class="grid gap-4 md:grid-cols-[1fr_auto_auto] md:items-center">
                    <p class="text-sm text-slate-600">{{ $description }}</p>
                    <div class="flex items-center gap-3">
                        <div class="flex items-center rounded-2xl border border-slate-200 bg-white px-3 py-2 shadow-sm">
                            <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"></path></svg>
                            <input id="group-search" type="text" placeholder="Search" class="ml-2 w-36 bg-transparent text-sm text-slate-900 placeholder:text-slate-400 outline-none">
                        </div>
                        @if(data_get($permissions, 'export'))
                            <button type="button" onclick="exportGroupTable('csv')" class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-100">Export CSV</button>
                            <button type="button" onclick="exportGroupTable('xlsx')" class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-semibold text-emerald-700 hover:bg-emerald-100">Export XLSX</button>
                            <button type="button" onclick="exportGroupTable('pdf')" class="rounded-lg border border-purple-200 bg-purple-50 px-3 py-2 text-sm font-semibold text-purple-700 hover:bg-purple-100">Export PDF</button>
                        @endif
                    </div>
                    <div class="flex justify-end">
                        @if(data_get($permissions, 'create'))<button type="button" onclick="openGroupModal()" class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Add New</button>@endif
                    </div>
                </div>
            </div>
            @if(session('success') || session('error'))<div class="mx-5 mt-4 rounded-2xl px-4 py-3 text-sm font-semibold {{ session('success') ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">{{ session('success') ?? session('error') }}</div>@endif
            <div class="overflow-x-auto"><div class="max-h-[420px] overflow-auto"><table class="min-w-full divide-y divide-slate-200">
                <thead class="sticky top-0 z-10 bg-purple-100"><tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Group Name</th><th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Description</th><th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Active</th><th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Action</th>
                </tr></thead><tbody class="divide-y divide-slate-200">
                @forelse($groups as $group)<tr>
                    <td class="px-5 py-3 text-sm font-semibold text-slate-900">{{ $group->group_name }}</td>
                    <td class="px-5 py-3 text-sm text-slate-600">{{ $group->description ?: '-' }}</td>
                    <td class="px-5 py-3 text-sm"><span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $group->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">{{ $group->is_active ? 'Active' : 'Inactive' }}</span></td>
                    <td class="px-5 py-3 text-sm"><div class="flex flex-wrap items-center gap-2">@if(data_get($permissions, 'edit'))<button type="button" onclick='editGroup(@json($group))' class="rounded-lg bg-slate-900 px-2.5 py-1.5 text-xs font-semibold text-white hover:bg-slate-800">Edit</button>@endif
                        @if((int) $group->is_active === 1 && data_get($permissions, 'deactivate'))
                            <form method="POST" action="{{ route('group.master.toggle', ['group_id' => $group->group_id]) }}" class="inline">@csrf<button type="submit" class="rounded-lg bg-rose-100 px-2.5 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-200">Disable</button></form>
                        @elseif((int) $group->is_active !== 1 && data_get($permissions, 'activate'))
                            <form method="POST" action="{{ route('group.master.toggle', ['group_id' => $group->group_id]) }}" class="inline">@csrf<button type="submit" class="rounded-lg bg-emerald-100 px-2.5 py-1.5 text-xs font-semibold text-emerald-700 hover:bg-emerald-200">Activate</button></form>
                        @endif</div></td>
                </tr>@empty<tr><td colspan="4" class="px-5 py-8 text-center text-sm text-slate-500">No groups found.</td></tr>@endforelse
                </tbody></table></div></div>
        </div>
    </div></div>
    <div id="group-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 px-4 py-8"><div class="mx-auto max-w-xl rounded-3xl bg-white shadow-2xl"><form id="group-form" method="POST">
        @csrf <input type="hidden" name="_method" id="group-method" value="POST">
        <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4"><h3 id="group-title" class="text-lg font-semibold text-slate-900">Add Group</h3><button type="button" onclick="closeGroupModal()" class="rounded-full bg-slate-100 px-3 py-1 text-slate-700">×</button></div>
        <div class="space-y-4 px-5 py-5"><div><label class="mb-1 block text-sm font-medium text-slate-700">Group Name</label><input id="group_name" name="group_name" required class="w-full rounded-xl border border-slate-200 px-3 py-2.5"></div><div><label class="mb-1 block text-sm font-medium text-slate-700">Description</label><textarea id="description" name="description" class="w-full rounded-xl border border-slate-200 px-3 py-2.5"></textarea></div><div class="flex justify-end gap-3 border-t border-slate-200 pt-4"><button type="button" onclick="closeGroupModal()" class="rounded-xl border border-slate-300 px-4 py-2 text-sm font-semibold">Cancel</button><button class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white">Save</button></div></div>
    </form></div></div>
    <script>
        function openGroupModal() { document.getElementById('group-title').textContent = 'Add Group'; document.getElementById('group-form').action = '{{ route('group.master.store') }}'; document.getElementById('group-method').value = 'POST'; document.getElementById('group_name').value = ''; document.getElementById('description').value = ''; document.getElementById('group-modal').classList.remove('hidden'); }
        function editGroup(group) { document.getElementById('group-title').textContent = 'Edit Group'; document.getElementById('group-form').action = '{{ url('/group-master') }}/' + group.group_id; document.getElementById('group-method').value = 'PUT'; document.getElementById('group_name').value = group.group_name; document.getElementById('description').value = group.description || ''; document.getElementById('group-modal').classList.remove('hidden'); }
        function closeGroupModal() { document.getElementById('group-modal').classList.add('hidden'); }
        function exportGroupTable(format) { window.location.href = '{{ route('group.master') }}?format=' + encodeURIComponent(format); }
        function filterGroups() {
            const query = document.getElementById('group-search').value.trim().toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            let visibleCount = 0;
            rows.forEach((row) => {
                if (row.querySelector('td[colspan]')) return;
                const matches = query === '' || row.textContent.toLowerCase().includes(query);
                row.classList.toggle('hidden', !matches);
                if (matches) visibleCount += 1;
            });
            const emptyRow = document.querySelector('tbody tr td[colspan]')?.closest('tr');
            if (emptyRow && emptyRow.querySelector('td[colspan="4"]')) emptyRow.classList.toggle('hidden', visibleCount !== 0);
        }
        document.addEventListener('DOMContentLoaded', () => document.getElementById('group-search')?.addEventListener('input', filterGroups));
    </script>
</x-app-layout>