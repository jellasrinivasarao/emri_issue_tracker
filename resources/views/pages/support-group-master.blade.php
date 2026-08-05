<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ $title ?? __('Support Group Master') }}</h2>
    </x-slot>

    <div class="py-8 h-full min-h-0 box-border overflow-hidden">
        <div class="mx-auto flex h-full min-h-0 max-w-7xl flex-col box-border px-4 sm:px-6 lg:px-8 overflow-hidden">
            <div class="flex h-full min-h-0 flex-1 flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 bg-slate-50 px-5 py-5">
                        <div class="grid gap-4 md:grid-cols-[1fr_auto_auto] md:items-center">
                            <div>
                                <p class="text-sm text-slate-600">{{ $description ?? __('Manage support groups and group ownership details.') }}</p>
                            </div>
                            <div class="flex items-center justify-end rounded-2xl border border-slate-200 bg-white px-3 py-2 shadow-sm">
                                <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19 a8 8 0 100-16 8 8 0 000 16z"></path></svg>
                                <input id="support-group-search" type="text" placeholder="Search" class="ml-2 w-36 bg-transparent text-sm text-slate-900 placeholder:text-slate-400 outline-none" />
                            </div>
                            @if(data_get($permissions, 'create'))
                                <button type="button" onclick="openSupportGroupMasterModal()" class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">Add New</button>
                            @endif
                        </div>
                </div>
                <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                        <div class="flex flex-wrap items-center gap-3">
                            @if(data_get($permissions, 'export'))
                                <button type="button" onclick="exportSupportGroupTable('csv')" class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-100">Export CSV</button>
                                <button type="button" onclick="exportSupportGroupTable('xlsx')" class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-semibold text-emerald-700 hover:bg-emerald-100">Export XLSX</button>
                                <button type="button" onclick="exportSupportGroupTable('pdf')" class="rounded-lg border border-purple-200 bg-purple-50 px-3 py-2 text-sm font-semibold text-purple-700 hover:bg-purple-100">Export PDF</button>
                            @endif
                        </div>
                </div>
                @if(session('success') || session('error'))
                    <div class="px-5 py-4" id="support-group-message-container">
                        <div id="support-group-message" class="relative rounded-2xl px-4 py-3 text-sm font-semibold shadow-sm {{ session('success') ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                            <span>{{ session('success') ?? session('error') }}</span>
                            <button type="button" onclick="closeSupportGroupMessage()" class="absolute right-3 top-3 rounded-full bg-white/80 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-white focus:outline-none">Close</button>
                        </div>
                    </div>
                @endif
                <div class="flex-1 min-h-0 overflow-hidden">
                    <div class="overflow-x-auto h-full min-h-0">
                        <div class="flex h-full min-h-0 overflow-x-auto overflow-y-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-purple-100 sticky top-0 z-10">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Support Group</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Description</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Status</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                @forelse($supportGroups as $group)
                                    <tr>
                                        <td class="px-5 py-3 text-sm font-semibold text-slate-900">{{ $group->support_group_name }}</td>
                                        <td class="px-5 py-3 text-sm text-slate-600">{{ $group->description ?? '-' }}</td>
                                        <td class="px-5 py-3 text-sm">
                                            @if((int) $group->is_active === 1)
                                                <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">Active</span>
                                            @else
                                                <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">Inactive</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3 text-sm">
                                            <div class="flex flex-wrap items-center gap-2">
                                                @if(data_get($permissions, 'edit'))
                                                    <button type="button"
                                                        data-group-id="{{ $group->support_group_id }}"
                                                        data-group-name="{{ $group->support_group_name }}"
                                                        data-description="{{ $group->description }}"
                                                        data-status="{{ (int) $group->is_active === 1 ? 'active' : 'inactive' }}"
                                                        onclick="editSupportGroup(this.dataset)"
                                                        class="rounded-lg bg-slate-900 px-2.5 py-1.5 text-xs font-semibold text-white hover:bg-slate-800">Edit</button>
                                                @endif

                                                @if((int) $group->is_active === 1 && data_get($permissions, 'deactivate'))
                                                    <form method="POST" action="{{ url('/support-group-master/' . $group->support_group_id . '/toggle') }}" style="display:inline">@csrf
                                                        <button type="submit" class="rounded-lg bg-rose-100 text-rose-700 hover:bg-rose-200 px-2.5 py-1.5 text-xs font-semibold">Disable</button>
                                                    </form>
                                                @elseif((int) $group->is_active !== 1 && data_get($permissions, 'activate'))
                                                    <form method="POST" action="{{ url('/support-group-master/' . $group->support_group_id . '/toggle') }}" style="display:inline">@csrf
                                                        <button type="submit" class="rounded-lg bg-emerald-100 text-emerald-700 hover:bg-emerald-200 px-2.5 py-1.5 text-xs font-semibold">Activate</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="empty-row">
                                        <td colspan="4" class="px-5 py-6 text-center text-sm text-slate-500">No support groups found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="support-group-master-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 px-4 py-8">
        <div class="mx-auto flex max-w-2xl flex-col rounded-3xl bg-white shadow-2xl">
            <form id="support-group-master-form" method="POST" action="">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                <div>
                    <h3 id="support-group-modal-title" class="text-lg font-semibold text-slate-900">Add Support Group</h3>
                    <p class="text-sm text-slate-600">Create or update a support group entry.</p>
                </div>
                <button type="button" onclick="closeSupportGroupMasterModal()" class="rounded-full p-2 bg-slate-100 text-slate-700 hover:bg-slate-200 hover:text-slate-900">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
                <div class="space-y-4 px-5 py-5">
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Group Name</label>
                    <input id="support_group_name" name="support_group_name" type="text" required class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400" placeholder="Enter group name" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Description</label>
                    <textarea id="support_group_description" name="support_group_description" rows="4" required class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400" placeholder="Add description"></textarea>
                </div>
                @csrf
                <input type="hidden" id="support_group_id" name="support_group_id" value="" />
                <input type="hidden" id="support_group_form_method" name="_method" value="POST" />
                <input type="hidden" id="support_group_created_at" name="created_at" value="" />
                <input type="hidden" id="support_group_updated_at" name="updated_at" value="" />
                <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-4">
                    <button type="button" onclick="closeSupportGroupMasterModal()" class="rounded-xl border border-slate-300 bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">Cancel</button>
                    <button type="submit" class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700" id="support-group-modal-submit">Save</button>
                </div>
            </div>
            </form>
        </div>
    </div>

    <script>
        function currentTimestamp() {
            const d = new Date();
            const pad = (n) => n.toString().padStart(2, '0');
            return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())} ${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`;
        }

        function openSupportGroupMasterModal() {
            document.getElementById('support-group-modal-title').textContent = 'Add Support Group';
            document.getElementById('support-group-master-form').action = '{{ route('support-group.master.store') }}';
            document.getElementById('support_group_form_method').value = 'POST';
            document.getElementById('support_group_id').value = '';
            document.getElementById('support_group_name').value = '';
            document.getElementById('support_group_description').value = '';
            const now = currentTimestamp();
            const ca = document.getElementById('support_group_created_at');
            const ua = document.getElementById('support_group_updated_at');
            if (ca) ca.value = now;
            if (ua) ua.value = now;
            document.getElementById('support-group-modal-submit').textContent = 'Save';
            document.getElementById('support-group-master-modal').classList.remove('hidden');
        }

        function closeSupportGroupMasterModal() {
            document.getElementById('support-group-master-modal').classList.add('hidden');
        }

        function editSupportGroup(data) {
            document.getElementById('support-group-modal-title').textContent = 'Edit Support Group';
            document.getElementById('support-group-master-form').action = '{{ url('/support-group-master') }}' + '/' + (data.groupId || '');
            document.getElementById('support_group_form_method').value = 'PUT';
            document.getElementById('support_group_id').value = data.groupId || '';
            document.getElementById('support_group_name').value = data.groupName || '';
            document.getElementById('support_group_description').value = data.description || '';
            const now = currentTimestamp();
            const ua = document.getElementById('support_group_updated_at');
            if (ua) ua.value = now;
            document.getElementById('support-group-modal-submit').textContent = 'Update';
            document.getElementById('support-group-master-modal').classList.remove('hidden');
        }

        function exportSupportGroupTable(format) {
            const params = new URLSearchParams({ format });
            window.location.href = window.location.pathname + '?' + params.toString();
        }

        function closeSupportGroupMessage() {
            const container = document.getElementById('support-group-message-container');
            if (container) {
                container.style.transition = 'opacity 0.4s ease';
                container.style.opacity = '0';
                setTimeout(() => container.remove(), 400);
            }
        }

        function filterSupportGroups() {
            const query = document.getElementById('support-group-search').value.trim().toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            let visibleCount = 0;
            rows.forEach((row) => {
                const text = row.textContent.toLowerCase();
                const match = query === '' || text.includes(query);
                row.classList.toggle('hidden', !match);
                if (match) visibleCount += 1;
            });
            const emptyRow = document.querySelector('tbody tr.empty-row');
            if (emptyRow) emptyRow.classList.toggle('hidden', visibleCount !== 0);
        }

        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('support-group-search');
            if (searchInput) {
                searchInput.addEventListener('input', filterSupportGroups);
            }
        });
    </script>
</x-app-layout>
