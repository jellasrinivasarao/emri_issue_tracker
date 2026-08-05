<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ $title ?? __('Role Master') }}</h2>
    </x-slot>

    <div class="py-8 h-full min-h-0 box-border overflow-hidden">
        <div class="mx-auto flex h-full min-h-0 max-w-7xl flex-col box-border px-4 sm:px-6 lg:px-8 overflow-hidden">
            <div class="flex h-full min-h-0 flex-1 flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 bg-slate-50 px-5 py-5">
                    <div class="grid gap-4 md:grid-cols-[1fr_auto_auto] md:items-center">
                        <div>
                            <p class="text-sm text-slate-600">{{ $description ?? __('Manage roles, role categories, and role definitions.') }}</p>
                        </div>
                        <div class="flex items-center justify-end rounded-2xl border border-slate-200 bg-white px-3 py-2 shadow-sm">
                            <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"></path></svg>
                            <input id="role-search" type="text" placeholder="Search" class="ml-2 w-36 bg-transparent text-sm text-slate-900 placeholder:text-slate-400 outline-none" />
                        </div>
                        <button type="button" onclick="openRoleMasterModal()" class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">Add New</button>
                    </div>
                </div>

                <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                    <div class="flex flex-wrap items-center gap-3">
                        @if(data_get($permissions, 'export'))
                            <button type="button" onclick="exportRoleTable('csv')" class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-100">Export CSV</button>
                            <button type="button" onclick="exportRoleTable('xlsx')" class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-semibold text-emerald-700 hover:bg-emerald-100">Export XLSX</button>
                            <button type="button" onclick="exportRoleTable('pdf')" class="rounded-lg border border-purple-200 bg-purple-50 px-3 py-2 text-sm font-semibold text-purple-700 hover:bg-purple-100">Export PDF</button>
                        @endif
                    </div>
                </div>

                @if(session('success') || session('error'))
                    <div class="px-5 py-4" id="role-message-container">
                        <div id="role-message" class="relative rounded-2xl px-4 py-3 text-sm font-semibold shadow-sm {{ session('success') ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                            <span>{{ session('success') ?? session('error') }}</span>
                            <button type="button" onclick="closeRoleMessage()" class="absolute right-3 top-3 rounded-full bg-white/80 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-white focus:outline-none">Close</button>
                        </div>
                    </div>
                @endif

                <div class="flex-1 min-h-0 overflow-hidden">
                    <div class="overflow-x-auto h-full min-h-0">
                        <div class="flex h-full min-h-0 overflow-x-auto overflow-y-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-purple-100 sticky top-0 z-10">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Role Code</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Role Name</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Category</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Description</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">System Role</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                @forelse($roles as $role)
                                    <tr>
                                        <td class="px-5 py-3 text-sm font-semibold text-slate-900">{{ $role->role_code }}</td>
                                        <td class="px-5 py-3 text-sm text-slate-600">{{ $role->role_name }}</td>
                                        <td class="px-5 py-3 text-sm text-slate-600">{{ $role->role_category ?? '-' }}</td>
                                        <td class="px-5 py-3 text-sm text-slate-600">{{ $role->description ?? '-' }}</td>
                                        <td class="px-5 py-3 text-sm">{{ $role->is_system_role ? 'Yes' : 'No' }}</td>
                                        <td class="px-5 py-3 text-sm">
                                            <div class="flex flex-wrap items-center gap-2">
                                                @if(data_get($permissions, 'edit'))
                                                    <button type="button"
                                                        data-role-id="{{ $role->role_id }}"
                                                        data-role-code="{{ $role->role_code }}"
                                                        data-role-name="{{ $role->role_name }}"
                                                        data-role-category="{{ $role->role_category ?? '' }}"
                                                        data-description="{{ $role->description ?? '' }}"
                                                        onclick="editRole(this.dataset)"
                                                        class="rounded-lg bg-slate-900 px-2.5 py-1.5 text-xs font-semibold text-white hover:bg-slate-800">Edit</button>
                                                @endif
                                                @if((int) $role->is_system_role === 1 && data_get($permissions, 'deactivate'))
                                                    <form method="POST" action="{{ route('role.master.toggle', ['role_id' => $role->role_id]) }}" class="inline">
                                                        @csrf
                                                        <button type="submit" class="rounded-lg px-2.5 py-1.5 text-xs font-semibold bg-rose-100 text-rose-700 hover:bg-rose-200">Disable</button>
                                                    </form>
                                                @elseif((int) $role->is_system_role !== 1 && data_get($permissions, 'activate'))
                                                    <form method="POST" action="{{ route('role.master.toggle', ['role_id' => $role->role_id]) }}" class="inline">
                                                        @csrf
                                                        <button type="submit" class="rounded-lg px-2.5 py-1.5 text-xs font-semibold bg-emerald-100 text-emerald-700 hover:bg-emerald-200">Activate</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="empty-row">
                                        <td colspan="6" class="px-5 py-6 text-center text-sm text-slate-500">No roles found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="role-master-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 px-4 py-8">
        <div class="mx-auto flex max-w-2xl flex-col rounded-3xl bg-white shadow-2xl">
            <form id="role-master-form" method="POST" action="">
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                    <div>
                        <h3 id="role-modal-title" class="text-lg font-semibold text-slate-900">Add Role</h3>
                        <p class="text-sm text-slate-600">Create or update a role entry in the system.</p>
                    </div>
                    <button type="button" onclick="closeRoleMasterModal()" class="rounded-full p-2 bg-slate-100 text-slate-700 hover:bg-slate-200 hover:text-slate-900">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="space-y-4 px-5 py-5">
                    @csrf
                    <input type="hidden" id="role_id" name="role_id" value="" />
                    <input type="hidden" id="role_form_method" name="_method" value="POST" />

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Role Name</label>
                            <input id="role_name" name="role_name" type="text" required class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400" placeholder="Enter role name" />
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Role Category</label>
                            <input id="role_category" name="role_category" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400" placeholder="Enter role category" />
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Description</label>
                        <textarea id="role_description" name="description" rows="4" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400" placeholder="Add description"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-4">
                        <button type="button" onclick="closeRoleMasterModal()" class="rounded-xl border border-slate-300 bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">Cancel</button>
                        <button type="submit" class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700" id="role-modal-submit">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openRoleMasterModal() {
            document.getElementById('role-modal-title').textContent = 'Add Role';
            document.getElementById('role-master-form').action = '{{ route('role.master.store') }}';
            document.getElementById('role_form_method').value = 'POST';
            document.getElementById('role_id').value = '';
            document.getElementById('role_name').value = '';
            document.getElementById('role_category').value = '';
            document.getElementById('role_description').value = '';
            document.getElementById('role-modal-submit').textContent = 'Save';
            document.getElementById('role-master-modal').classList.remove('hidden');
        }

        function closeRoleMasterModal() {
            document.getElementById('role-master-modal').classList.add('hidden');
        }

        function editRole(data) {
            document.getElementById('role-modal-title').textContent = 'Edit Role';
            document.getElementById('role-master-form').action = '{{ url('/role-master') }}' + '/' + (data.roleId || '');
            document.getElementById('role_form_method').value = 'PUT';
            document.getElementById('role_id').value = data.roleId || '';
            document.getElementById('role_name').value = data.roleName || '';
            document.getElementById('role_category').value = data.roleCategory || '';
            document.getElementById('role_description').value = data.description || '';
            document.getElementById('role-modal-submit').textContent = 'Update';
            document.getElementById('role-master-modal').classList.remove('hidden');
        }

        function exportRoleTable(format) {
            const params = new URLSearchParams({ format });
            window.location.href = '{{ route('role.master') }}?' + params.toString();
        }

        function closeRoleMessage() {
            const container = document.getElementById('role-message-container');
            if (container) {
                container.style.transition = 'opacity 0.4s ease';
                container.style.opacity = '0';
                setTimeout(() => container.remove(), 400);
            }
        }

        function filterRoles() {
            const query = document.getElementById('role-search').value.trim().toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            let visibleCount = 0;
            rows.forEach((row) => {
                if (row.classList.contains('empty-row')) {
                    return;
                }
                const text = row.textContent.toLowerCase();
                const match = query === '' || text.includes(query);
                row.classList.toggle('hidden', !match);
                if (match) visibleCount += 1;
            });
            const emptyRow = document.querySelector('tbody tr.empty-row');
            if (emptyRow) {
                emptyRow.classList.toggle('hidden', visibleCount !== 0);
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('role-search');
            if (searchInput) {
                searchInput.addEventListener('input', filterRoles);
            }
        });
    </script>
</x-app-layout>
