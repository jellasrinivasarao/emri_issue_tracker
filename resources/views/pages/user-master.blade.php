<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ $title ?? __('User Master') }}</h2>
    </x-slot>

    <div class="py-8 h-full min-h-0 box-border overflow-hidden">
        <div class="mx-auto flex h-full min-h-0 max-w-7xl flex-col box-border px-4 sm:px-6 lg:px-8 overflow-hidden">
            <div class="flex h-full min-h-0 flex-1 flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 bg-slate-50 px-5 py-5">
                        <div class="grid gap-4 md:grid-cols-[1fr_auto_auto] md:items-center">
                            <div>
                                <p class="text-sm text-slate-600">{{ $description ?? __('Manage users, login details, and role assignments.') }}</p>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-end rounded-2xl border border-slate-200 bg-white px-3 py-2 shadow-sm">
                                    <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19 a8 8 0 100-16 8 8 0 000 16z"></path></svg>
                                    <input id="user-search" type="text" placeholder="Search" class="ml-2 w-36 bg-transparent text-sm text-slate-900 placeholder:text-slate-400 outline-none" />
                                </div>
                                @if(data_get($permissions, 'export'))
                                    <button type="button" onclick="exportUserTable('csv')" class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-100">Export CSV</button>
                                    <button type="button" onclick="exportUserTable('xlsx')" class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-semibold text-emerald-700 hover:bg-emerald-100">Export XLSX</button>
                                    <button type="button" onclick="exportUserTable('pdf')" class="rounded-lg border border-purple-200 bg-purple-50 px-3 py-2 text-sm font-semibold text-purple-700 hover:bg-purple-100">Export PDF</button>
                                @endif
                            </div>
                            <div class="flex justify-end">
                                @if(data_get($permissions, 'create'))
                                    <button type="button" onclick="openUserMasterModal()" class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">Add New</button>
                                @endif
                            </div>
                        </div>
                </div>

                @if(session('success') || session('error'))
                    <div class="px-5 py-4" id="user-message-container">
                        <div id="user-message" class="relative rounded-2xl px-4 py-3 text-sm font-semibold shadow-sm {{ session('success') ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                            <span>{{ session('success') ?? session('error') }}</span>
                            <button type="button" onclick="closeUserMessage()" class="absolute right-3 top-3 rounded-full bg-white/80 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-white focus:outline-none">Close</button>
                        </div>
                    </div>
                @endif

                <div class="flex-1 min-h-0 overflow-hidden">
                    <div class="overflow-x-auto h-full min-h-0">
                        <div class="flex h-full min-h-0 overflow-x-auto overflow-y-auto">
                            <table class="min-w-full divide-y divide-slate-200">
                                <thead class="bg-purple-100 sticky top-0 z-10">
                                    <tr>
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Employee Code</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">User Name</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Login ID</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Email</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Mobile</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Role</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Status</th>
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 bg-white">
                                    @forelse($users as $user)
                                        <tr>
                                            <td class="px-5 py-3 text-sm font-semibold text-slate-900">{{ $user->employee_code }}</td>
                                            <td class="px-5 py-3 text-sm text-slate-600">{{ $user->user_name }}</td>
                                            <td class="px-5 py-3 text-sm text-slate-600">{{ $user->login_id }}</td>
                                            <td class="px-5 py-3 text-sm text-slate-600">{{ $user->official_email }}</td>
                                            <td class="px-5 py-3 text-sm text-slate-600">{{ $user->mobile_number ?? '-' }}</td>
                                            <td class="px-5 py-3 text-sm text-slate-600">{{ $user->roles->pluck('role_name')->join(', ') ?: '-' }}</td>
                                            <td class="px-5 py-3 text-sm">{{ $user->is_active ? 'Active' : 'Inactive' }}</td>
                                            <td class="px-5 py-3 text-sm">
                                                <div class="flex flex-wrap items-center gap-2">
                                                @if(data_get($permissions, 'edit'))
                                                    <button type="button"
                                                        data-user-id="{{ $user->user_id }}"
                                                        data-user-name="{{ $user->user_name }}"
                                                        data-login-id="{{ $user->login_id }}"
                                                        data-official-email="{{ $user->official_email }}"
                                                        data-mobile-number="{{ $user->mobile_number }}"
                                                        data-role-id="{{ $user->role_id }}"
                                                        data-user-status="{{ $user->user_status }}"
                                                        onclick="editUser(this.dataset)"
                                                        class="rounded-lg bg-slate-900 px-2.5 py-1.5 text-xs font-semibold text-white hover:bg-slate-800">Edit</button>
                                                @endif
                                                @if($user->is_active && data_get($permissions, 'deactivate'))
                                                    <form method="POST" action="{{ route('user.master.toggle', ['user_id' => $user->user_id]) }}" class="inline">
                                                        @csrf
                                                        <button type="submit" class="rounded-lg px-2.5 py-1.5 text-xs font-semibold bg-rose-100 text-rose-700 hover:bg-rose-200">Disable</button>
                                                    </form>
                                                @elseif(!$user->is_active && data_get($permissions, 'activate'))
                                                    <form method="POST" action="{{ route('user.master.toggle', ['user_id' => $user->user_id]) }}" class="inline">
                                                        @csrf
                                                        <button type="submit" class="rounded-lg px-2.5 py-1.5 text-xs font-semibold bg-emerald-100 text-emerald-700 hover:bg-emerald-200">Activate</button>
                                                    </form>
                                                @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="empty-row">
                                            <td colspan="8" class="px-5 py-6 text-center text-sm text-slate-500">No users found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="user-master-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 px-4 py-8">
        <div class="mx-auto flex max-w-2xl flex-col rounded-3xl bg-white shadow-2xl">
            <form id="user-master-form" method="POST" action="">
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                    <div>
                        <h3 id="user-modal-title" class="text-lg font-semibold text-slate-900">Add User</h3>
                        <p class="text-sm text-slate-600">Create or update a user record in the system.</p>
                    </div>
                    <button type="button" onclick="closeUserMasterModal()" class="rounded-full p-2 bg-slate-100 text-slate-700 hover:bg-slate-200 hover:text-slate-900">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="space-y-4 px-5 py-5">
                    @csrf
                    <input type="hidden" id="user_id" name="user_id" value="" />
                    <input type="hidden" id="user_form_method" name="_method" value="POST" />

                    <div class="grid gap-4 md:grid-cols-2">
                        <div id="employee_code_group">
                            <label class="mb-1 block text-sm font-medium text-slate-700">Employee Code</label>
                            <input id="employee_code" name="employee_code" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400" placeholder="Enter employee code" />
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">User Name</label>
                            <input id="user_name" name="user_name" type="text" required class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400" placeholder="Enter user name" />
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div id="login_id_group">
                            <label class="mb-1 block text-sm font-medium text-slate-700">Login ID</label>
                            <input id="login_id" name="login_id" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400" placeholder="Enter login ID" />
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Official Email</label>
                            <input id="official_email" name="official_email" type="email" required class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400" placeholder="Enter official email" />
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Mobile Number</label>
                            <input id="mobile_number" name="mobile_number" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400" placeholder="Enter mobile number" />
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Role</label>
                            <select id="role_id" name="role_id" required class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400 bg-white">
                                <option value="">Select role</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->role_id }}">{{ $role->role_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Password</label>
                            <input id="password" name="password" type="password" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400" placeholder="Enter password" />
                            <p class="mt-2 text-xs text-slate-500">Leave blank while editing to keep the current password.</p>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Status</label>
                            <select id="user_status" name="user_status" required class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400 bg-white">
                                <option value="Active">Active</option>
                                <option value="Inactive">Inactive</option>
                                <option value="Suspended">Suspended</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-4">
                        <button type="button" onclick="closeUserMasterModal()" class="rounded-xl border border-slate-300 bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">Cancel</button>
                        <button type="submit" class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700" id="user-modal-submit">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openUserMasterModal() {
            document.getElementById('user-modal-title').textContent = 'Add User';
            document.getElementById('user-master-form').action = '{{ route('user.master.store') }}';
            document.getElementById('user_form_method').value = 'POST';
            document.getElementById('user_id').value = '';
            document.getElementById('employee_code').value = '';
            document.getElementById('user_name').value = '';
            document.getElementById('login_id').value = '';
            document.getElementById('official_email').value = '';
            document.getElementById('mobile_number').value = '';
            document.getElementById('role_id').value = '';
            document.getElementById('password').value = '';
            document.getElementById('user_status').value = 'Active';
            // For Add mode hide employee_code and login_id fields (they will be generated server-side)
            const empGroup = document.getElementById('employee_code_group');
            const loginGroup = document.getElementById('login_id_group');
            if (empGroup) empGroup.classList.add('hidden');
            if (loginGroup) loginGroup.classList.add('hidden');
            document.getElementById('user-modal-submit').textContent = 'Save';
            document.getElementById('user-master-modal').classList.remove('hidden');
        }

        function closeUserMasterModal() {
            document.getElementById('user-master-modal').classList.add('hidden');
        }

        function editUser(data) {
            document.getElementById('user-modal-title').textContent = 'Edit User';
            document.getElementById('user-master-form').action = '{{ url('/user-master') }}' + '/' + (data.userId || '');
            document.getElementById('user_form_method').value = 'PUT';
            document.getElementById('user_id').value = data.userId || '';
            document.getElementById('employee_code').value = data.employeeCode || '';
            document.getElementById('user_name').value = data.userName || '';
            document.getElementById('login_id').value = data.loginId || '';
            document.getElementById('official_email').value = data.officialEmail || '';
            document.getElementById('mobile_number').value = data.mobileNumber || '';
            document.getElementById('role_id').value = data.roleId || '';
            document.getElementById('password').value = '';
            document.getElementById('user_status').value = data.userStatus || 'Active';
            // In Edit mode show employee_code and login_id so they can be updated
            const empGroup = document.getElementById('employee_code_group');
            const loginGroup = document.getElementById('login_id_group');
            if (empGroup) empGroup.classList.remove('hidden');
            if (loginGroup) loginGroup.classList.remove('hidden');
            document.getElementById('user-modal-submit').textContent = 'Update';
            document.getElementById('user-master-modal').classList.remove('hidden');
        }

        function exportUserTable(format) {
            const params = new URLSearchParams({ format });
            window.location.href = '{{ route('user.master') }}?' + params.toString();
        }

        function closeUserMessage() {
            const container = document.getElementById('user-message-container');
            if (container) {
                container.style.transition = 'opacity 0.4s ease';
                container.style.opacity = '0';
                setTimeout(() => container.remove(), 400);
            }
        }

        function filterUsers() {
            const query = document.getElementById('user-search').value.trim().toLowerCase();
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
            const searchInput = document.getElementById('user-search');
            if (searchInput) {
                searchInput.addEventListener('input', filterUsers);
            }
        });
    </script>
</x-app-layout>
