<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ $title ?? __('User Master') }}</h2>
    </x-slot>

    <div class="py-8 h-full min-h-0 box-border">
        <div class="mx-auto flex h-full min-h-0 w-full max-w-full flex-col box-border px-4 sm:px-6 lg:px-8">
            <div class="flex h-full min-h-0 flex-1 flex-col rounded-3xl border border-slate-200 bg-white shadow-sm">
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
                                    <button id="btn-open-user-master" type="button" class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">Add New</button>
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

                @if ($errors->any())
                    <div class="px-5 py-4">
                        <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
                            <div class="font-semibold">Please correct the errors below.</div>
                            <ul class="mt-2 list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
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
                                                        data-state-id="{{ $user->state_id ?? '' }}"
                                                        data-vendor-id="{{ $user->vendor_id ?? '' }}"
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
        <div class="mx-auto flex max-w-2xl flex-col rounded-3xl bg-white shadow-2xl overflow-hidden" style="max-height: calc(100vh - 4rem);">
            <form id="user-master-form" method="POST" action="{{ route('user.master.store') }}">
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                    <div>
                        <h3 id="user-modal-title" class="text-lg font-semibold text-slate-900">Add User</h3>
                        <p class="text-sm text-slate-600">Create or update a user record in the system.</p>
                    </div>
                    <button type="button" onclick="closeUserMasterModal()" class="rounded-full p-2 bg-slate-100 text-slate-700 hover:bg-slate-200 hover:text-slate-900">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="overflow-y-auto px-5 py-5" style="max-height: calc(100vh - 18rem);">
                    @csrf
                    <input type="hidden" id="user_id" name="user_id" value="{{ old('user_id', '') }}" />
                    <input type="hidden" id="user_form_method" name="_method" value="POST" />

                    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                        <div class="mb-5 flex flex-wrap items-center justify-between gap-4">
                            <div class="min-w-0">
                                <h3 class="text-lg font-semibold text-slate-900">User Information</h3>
                                <p class="mt-1 text-sm text-slate-600">Create a new user or update existing user details in the system.</p>
                            </div>
                        </div>

                        <div id="employee_code_group" class="hidden mb-4">
                            <label class="text-sm font-medium text-slate-700">Employee Code</label>
                            <input id="employee_code" name="employee_code" type="text" value="{{ old('employee_code') }}" class="w-full rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:ring-1 focus:ring-emerald-100" placeholder="Enter employee code" />
                        </div>

                        <div id="login_id_group" class="hidden mb-4">
                            <label class="text-sm font-medium text-slate-700">Login ID</label>
                            <input id="login_id" name="login_id" type="text" value="{{ old('login_id') }}" class="w-full rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:ring-1 focus:ring-emerald-100" placeholder="Enter login ID" />
                        </div>

                        <div class="grid gap-4 grid-cols-1 md:grid-cols-2">
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-slate-700">User Name <span class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-slate-400">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                    </span>
                                    <input id="user_name" name="user_name" type="text" required value="{{ old('user_name') }}" class="w-full rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 pl-12 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:ring-1 focus:ring-emerald-100" placeholder="Enter user name" />
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-slate-700">Password <span class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-slate-400">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                                    </span>
                                    <input id="password" name="password" type="password" class="w-full rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 pl-12 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:ring-1 focus:ring-emerald-100" placeholder="Enter password" />
                                </div>
                                <p class="text-xs text-slate-500">Leave blank while editing to keep the current password.</p>
                            </div>
                        </div>

                        <div class="mt-4 grid gap-4 grid-cols-1 md:grid-cols-2">
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-slate-700">Official Email <span class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-slate-400">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16v16H4z"/><path d="M22 6l-10 7L2 6"/></svg>
                                    </span>
                                    <input id="official_email" name="official_email" type="email" required value="{{ old('official_email') }}" class="w-full rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 pl-12 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:ring-1 focus:ring-emerald-100" placeholder="Enter official email" />
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label class="text-sm font-medium text-slate-700">Mobile Number <span class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-slate-400">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6A19.79 19.79 0 013 4.18 2 2 0 015 2h3a2 2 0 012 1.72 12.86 12.86 0 00.7 2.81 2 2 0 01-.45 2.11L9.91 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.86 12.86 0 002.81.7A2 2 0 0122 16.92z"/></svg>
                                    </span>
                                    <input id="mobile_number" name="mobile_number" type="tel" maxlength="10" pattern="[0-9]{10}" value="{{ old('mobile_number') }}" oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,10)" class="w-full rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 pl-12 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:ring-1 focus:ring-emerald-100" placeholder="Enter 10 digit mobile number" />
                                </div>
                                <p class="text-xs text-slate-500">Only 10 digits allowed.</p>
                            </div>
                        </div>

                        <div class="mt-4 grid gap-4 grid-cols-1 md:grid-cols-3">
                            <div class="space-y-2">
                                <label class="text-sm font-medium text-slate-700">Role <span class="text-rose-500">*</span></label>
                                <div class="relative">
                                    <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-slate-400">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a4 4 0 014 4v2h2a2 2 0 012 2v2a6 6 0 01-6 6h-4a6 6 0 01-6-6v-2a2 2 0 012-2h2V6a4 4 0 014-4z"/></svg>
                                    </span>
                                    <select id="role_id" name="role_id" required class="w-full rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 pl-12 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:ring-1 focus:ring-emerald-100">
                                        <option value="">Select role</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->role_id }}" {{ old('role_id') == $role->role_id ? 'selected' : '' }}>{{ $role->role_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <div id="state_group_wrapper" class="hidden">
                                    <label class="text-sm font-medium text-slate-700">State <span class="text-slate-400 text-xs">(Select one or more)</span></label>
                                    <div id="state-select" class="relative">
                                        <div class="flex flex-wrap items-center gap-2 rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 shadow-sm transition focus-within:border-slate-400 focus-within:ring-1 focus-within:ring-emerald-100" data-multi-select="states" style="min-height: 4rem;">
                                            <div class="flex flex-wrap gap-2" data-multi-select-chips></div>
                                            <input type="text" class="min-w-[120px] flex-1 bg-transparent text-sm text-slate-900 placeholder:text-slate-400 outline-none" placeholder="Search and select states" data-multi-select-input autocomplete="off" />
                                        </div>
                                        <div class="absolute left-0 right-0 z-50 mt-1 hidden max-h-60 overflow-auto rounded-xl border border-slate-200 bg-white shadow-xl" data-multi-select-list></div>
                                        <div data-multi-select-hidden class="hidden"></div>
                                    </div>
                                    <p class="text-xs text-slate-500">This field will be enabled for State Admin / State IT roles.</p>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <div id="vendor_group" class="hidden">
                                    <label class="text-sm font-medium text-slate-700">Vendor</label>
                                    <div class="relative">
                                        <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-slate-400">
                                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="3" ry="3"/></svg>
                                        </span>
                                        <select id="vendor_id" name="vendor_id" class="w-full rounded-3xl border border-slate-200 bg-slate-50 px-4 py-3 pl-12 text-sm text-slate-900 outline-none transition focus:border-slate-400 focus:ring-1 focus:ring-emerald-100 hidden">
                                            <option value="">Select vendor</option>
                                            @foreach($vendors as $vendor)
                                                <option value="{{ $vendor->vendor_id }}" {{ old('vendor_id') == $vendor->vendor_id ? 'selected' : '' }}>{{ $vendor->vendor_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <p class="text-xs text-slate-500">This field will be enabled for Vendor Admin role.</p>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-4">
                        <button type="button" onclick="closeUserMasterModal()" class="rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-100">Cancel</button>
                        <button type="submit" class="rounded-xl bg-slate-900 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-slate-800" id="user-modal-submit">Save User</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        const rolesMap = @json($roles->pluck('role_name','role_id'));
        const currentUserStateId = @json(auth()->user()->state_id ?? null);
        const currentUserIsStateAdmin = @json(auth()->user()->hasRole('State Admin'));
        const currentUserIsVendorAdmin = @json(auth()->user()->hasRole('Vendor Admin'));
        const currentUserVendorId = @json(auth()->user()->vendor_id ?? null);
        // Multi-select state helper (reused pattern from other pages)
        const stateOptions = @json($states ?? []);
        const oldStateIds = @json(old('state_ids', []));
        const oldUserId = @json(old('user_id', ''));
        const hasValidationErrors = {{ $errors->any() ? 'true' : 'false' }};
        function formatStateLabel(item) {
            return item.state_name || '';
        }

        function getStateById(list, id) {
            return list.find((item) => String(item.state_id) === String(id));
        }

        function initMultiSelectStates() {
            const stateElement = document.querySelector(`[data-multi-select="states"]`);
            if (!stateElement) {
                return {
                    setItems() {},
                    setOptions() {},
                };
            }

            const container = stateElement.closest('#state-select');
            if (!container) {
                return {
                    setItems() {},
                    setOptions() {},
                };
            }

            const input = container.querySelector('[data-multi-select-input]');
            const list = container.querySelector('[data-multi-select-list]');
            const chipsContainer = container.querySelector('[data-multi-select-chips]');
            const hiddenContainer = container.querySelector('[data-multi-select-hidden]');
            if (!input || !list || !chipsContainer || !hiddenContainer) {
                return {
                    setItems() {},
                    setOptions() {},
                };
            }

            const selected = [];
            let options = [];

            function buildOptionButton(option) {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'w-full px-3 py-2 text-left text-sm text-slate-700 hover:bg-slate-50';
                button.dataset.value = option.state_id;
                button.textContent = formatStateLabel(option);
                return button;
            }

            function renderList(filter = '') {
                list.innerHTML = '';
                const query = filter.trim().toLowerCase();

                const filtered = options.filter((option) => {
                    const label = formatStateLabel(option).toLowerCase();
                    const alreadySelected = selected.includes(String(option.state_id));
                    const matchesSearch = query === '' || label.includes(query);
                    return !alreadySelected && matchesSearch;
                });

                if (filtered.length === 0) {
                    const empty = document.createElement('div');
                    empty.className = 'px-3 py-2 text-sm text-slate-500';
                    empty.textContent = 'No results found.';
                    list.appendChild(empty);
                    return;
                }

                filtered.forEach((option) => list.appendChild(buildOptionButton(option)));
            }

            function renderChips() {
                chipsContainer.innerHTML = '';
                hiddenContainer.innerHTML = '';

                selected.forEach((value) => {
                    const option = getStateById(options, value);
                    if (!option) return;

                    const chip = document.createElement('span');
                    chip.className = 'inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-sm text-slate-700';
                    chip.textContent = formatStateLabel(option);

                    const removeButton = document.createElement('button');
                    removeButton.type = 'button';
                    removeButton.className = 'rounded-full bg-slate-200 px-1 text-slate-500 hover:bg-slate-300';
                    removeButton.textContent = '×';
                    removeButton.addEventListener('click', (event) => {
                        event.stopPropagation();
                        removeSelected(value);
                    });

                    chip.appendChild(removeButton);
                    chipsContainer.appendChild(chip);

                    const hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = 'state_ids[]';
                    hidden.value = value;
                    hiddenContainer.appendChild(hidden);
                });
            }

            function addSelected(value) {
                if (!selected.includes(String(value))) {
                    selected.push(String(value));
                    renderChips();
                    renderList(input.value);
                }
            }

            function removeSelected(value) {
                const index = selected.indexOf(String(value));
                if (index !== -1) {
                    selected.splice(index, 1);
                    renderChips();
                    renderList(input.value);
                }
            }

            input.addEventListener('input', () => {
                renderList(input.value);
                list.classList.remove('hidden');
            });

            input.addEventListener('focus', () => {
                renderList(input.value);
                list.classList.remove('hidden');
            });

            list.addEventListener('click', (event) => {
                const button = event.target.closest('button[data-value]');
                if (!button) {
                    return;
                }
                addSelected(button.dataset.value);
            });

            document.addEventListener('click', (event) => {
                if (!container.contains(event.target)) {
                    list.classList.add('hidden');
                }
            });

            function setItems(values = []) {
                selected.splice(0, selected.length, ...values.map(String));
                renderChips();
            }

            function setOptions(nextOptions) {
                options = nextOptions;
                renderList(input.value);
            }

            renderList();
            renderChips();

            return { setItems, setOptions };
        }

        let stateSelectMulti;
        try {
            stateSelectMulti = initMultiSelectStates();
            if (stateSelectMulti) {
                stateSelectMulti.setOptions(stateOptions || []);
            }
        } catch (error) {
            console.error('State multi-select initialization failed:', error);
        }

        function showUserMasterModal(reset = true) {
            const modal = document.getElementById('user-master-modal');
            if (!modal) {
                console.error('showUserMasterModal: modal element not found');
                return;
            }

            const form = document.getElementById('user-master-form');
            const modalTitle = document.getElementById('user-modal-title');
            const methodInput = document.getElementById('user_form_method');
            const userIdInput = document.getElementById('user_id');
            const employeeInput = document.getElementById('employee_code');
            const nameInput = document.getElementById('user_name');
            const loginInput = document.getElementById('login_id');
            const emailInput = document.getElementById('official_email');
            const mobileInput = document.getElementById('mobile_number');
            const roleInput = document.getElementById('role_id');
            const passwordInput = document.getElementById('password');
            const empGroup = document.getElementById('employee_code_group');
            const loginGroup = document.getElementById('login_id_group');
            const submitBtn = document.getElementById('user-modal-submit');

            if (modalTitle) modalTitle.textContent = 'Add User';
            if (form) form.action = '{{ route('user.master.store') }}';
            if (methodInput) methodInput.value = 'POST';
            if (submitBtn) submitBtn.textContent = 'Save';
            if (empGroup) empGroup.classList.add('hidden');
            if (loginGroup) loginGroup.classList.add('hidden');

            if (reset) {
                if (userIdInput) userIdInput.value = '';
                if (employeeInput) employeeInput.value = '';
                if (nameInput) nameInput.value = '';
                if (loginInput) loginInput.value = '';
                if (emailInput) emailInput.value = '';
                if (mobileInput) mobileInput.value = '';
                if (roleInput) roleInput.value = '';
                if (passwordInput) passwordInput.value = '';
                if (stateSelectMulti) {
                    stateSelectMulti.setItems([]);
                }
            }

            modal.classList.remove('hidden');
            updateFieldsByRole();
        }

        function openUserMasterModal() {
            showUserMasterModal(true);
        }

        function closeUserMasterModal() {
            const modal = document.getElementById('user-master-modal');
            if (modal) {
                modal.classList.add('hidden');
            }
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
            // In Edit mode show employee_code and login_id so they can be updated
            const empGroup = document.getElementById('employee_code_group');
            const loginGroup = document.getElementById('login_id_group');
            if (empGroup) empGroup.classList.remove('hidden');
            if (loginGroup) loginGroup.classList.remove('hidden');
            document.getElementById('user-modal-submit').textContent = 'Update';
            document.getElementById('user-master-modal').classList.remove('hidden');
            // populate state(s) and vendor
            const vendorSelect = document.getElementById('vendor_id');
            if (stateSelectMulti) {
                if (data.stateId) {
                    const ids = String(data.stateId).split(',').map(s => s.trim()).filter(Boolean);
                    stateSelectMulti.setItems(ids);
                } else {
                    stateSelectMulti.setItems([]);
                }
            }
            if (vendorSelect) {
                vendorSelect.value = currentUserIsVendorAdmin ? (currentUserVendorId || '') : (data.vendorId || '');
            }
            updateFieldsByRole();
        }

        function updateFieldsByRole() {
            const roleSelect = document.getElementById('role_id');
            if (!roleSelect) return;
            const roleId = roleSelect.value;
            const roleName = rolesMap[roleId] ? String(rolesMap[roleId]).toLowerCase() : '';

            const stateGroup = document.getElementById('state_group_wrapper');
            const vendorGroup = document.getElementById('vendor_group');

            // Default hide
            if (stateGroup) stateGroup.classList.add('hidden');
            if (vendorGroup) vendorGroup.classList.add('hidden');

            // Show state multi-select ONLY when a State role is selected
            if (roleName.includes('state')) {
                if (stateGroup) stateGroup.classList.remove('hidden');
                // if current user is a State Admin, limit selectable states to their own
                if (stateSelectMulti) {
                    if (currentUserIsStateAdmin && currentUserStateId) {
                        const allowed = stateOptions.filter(s => String(s.state_id) === String(currentUserStateId));
                        stateSelectMulti.setOptions(allowed);
                    } else {
                        stateSelectMulti.setOptions(stateOptions);
                    }
                }
            } else {
                // hide and clear selections when role is not state-related
                if (stateGroup) stateGroup.classList.add('hidden');
                if (stateSelectMulti) {
                    stateSelectMulti.setItems([]);
                }
            }

            // If current user is Vendor Admin, hide vendor selection and auto-assign vendor_id.
            if (currentUserIsVendorAdmin) {
                if (vendorGroup) vendorGroup.classList.add('hidden');
                const vendorSelect = document.getElementById('vendor_id');
                if (vendorSelect) {
                    vendorSelect.classList.add('hidden');
                    vendorSelect.value = currentUserVendorId || '';
                }
            } else if (roleName.includes('vendor') && roleName.includes('admin')) {
                if (vendorGroup) vendorGroup.classList.remove('hidden');
                const vendorSelect = document.getElementById('vendor_id');
                if (vendorSelect) {
                    vendorSelect.classList.remove('hidden');
                }
            } else {
                const vendorSelect = document.getElementById('vendor_id');
                if (vendorSelect) {
                    vendorSelect.classList.add('hidden');
                    vendorSelect.value = '';
                }
            }

            // For Vendor IT and HO IT, keep state and vendor disabled (hidden above)
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

        function initUserMasterPage() {
            const searchInput = document.getElementById('user-search');
            if (searchInput) {
                searchInput.addEventListener('input', filterUsers);
            }
            const roleSelect = document.getElementById('role_id');
            if (roleSelect) {
                roleSelect.addEventListener('change', updateFieldsByRole);
            }
            const addUserButton = document.getElementById('btn-open-user-master');
            if (addUserButton) {
                addUserButton.addEventListener('click', openUserMasterModal);
            }

            try {
                stateSelectMulti = initMultiSelectStates();
                if (stateSelectMulti) {
                    stateSelectMulti.setOptions(stateOptions || []);
                }
            } catch (error) {
                console.error('State multi-select initialization failed:', error);
            }
        }

        function setEditModeFromOldInput() {
            if (!oldUserId) {
                return;
            }

            const form = document.getElementById('user-master-form');
            const methodInput = document.getElementById('user_form_method');
            const empGroup = document.getElementById('employee_code_group');
            const loginGroup = document.getElementById('login_id_group');
            const submitBtn = document.getElementById('user-modal-submit');

            if (form) {
                form.action = '{{ url('/user-master') }}/' + oldUserId;
            }
            if (methodInput) {
                methodInput.value = 'PUT';
            }
            if (empGroup) {
                empGroup.classList.remove('hidden');
            }
            if (loginGroup) {
                loginGroup.classList.remove('hidden');
            }
            if (submitBtn) {
                submitBtn.textContent = 'Update';
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function () {
                initUserMasterPage();
                if (hasValidationErrors) {
                    showUserMasterModal(false);
                    setEditModeFromOldInput();
                    if (stateSelectMulti && oldStateIds.length > 0) {
                        stateSelectMulti.setItems(oldStateIds);
                    }
                }
            });
        } else {
            initUserMasterPage();
            if (hasValidationErrors) {
                showUserMasterModal(false);
                setEditModeFromOldInput();
                if (stateSelectMulti && oldStateIds.length > 0) {
                    stateSelectMulti.setItems(oldStateIds);
                }
            }
        }

        window.openUserMasterModal = openUserMasterModal;
        window.closeUserMasterModal = closeUserMasterModal;
        window.editUser = editUser;
    </script>
</x-app-layout>
