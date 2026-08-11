<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ $title ?? __('Mail Configuration') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 bg-slate-50 px-5 py-5">
                    <div class="grid gap-4 md:grid-cols-[1fr_auto_auto] md:items-center">
                        <div>
                            <p class="text-sm text-slate-600">Create and manage notification delivery rules for projects, roles, and operational scopes.</p>
                        </div>
                        <div class="flex items-center justify-end">
                            <div class="flex items-center justify-end rounded-2xl border border-slate-200 bg-white px-3 py-2 shadow-sm">
                                <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"></path></svg>
                                <input id="mail-search" type="text" placeholder="Search" class="ml-2 w-36 bg-transparent text-sm text-slate-900 placeholder:text-slate-400 outline-none" />
                            </div>
                        </div>
                        <div class="flex items-center justify-end gap-3">
                            @if(data_get($permissions, 'export'))
                                <button type="button" class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-100">Export CSV</button>
                                <button type="button" class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-semibold text-emerald-700 hover:bg-emerald-100">Export XLSX</button>
                                <button type="button" class="rounded-lg border border-purple-200 bg-purple-50 px-3 py-2 text-sm font-semibold text-purple-700 hover:bg-purple-100">Export PDF</button>
                            @endif

                            @if(data_get($permissions, 'create'))
                                <button type="button" onclick="openMailConfigurationModal()" class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">Add Rule</button>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <div class="max-h-[420px] overflow-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-purple-100 sticky top-0 z-10">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">State</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">To Mail IDs</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">CC Mail IDs</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Status</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="mail-configuration-table-body" class="divide-y divide-slate-200 bg-white">
                                @forelse($mailConfigurations ?? [] as $configuration)
                                    <tr>
                                        <td class="px-5 py-4 text-sm font-semibold text-slate-900">{{ $configuration->state_name ?? 'All States' }}</td>
                                        <td class="px-5 py-4 text-sm text-slate-600">{{ implode(', ', json_decode($configuration->to_emails, true) ?: []) }}</td>
                                        <td class="px-5 py-4 text-sm text-slate-600">{{ implode(', ', json_decode($configuration->cc_emails, true) ?: []) ?: '-' }}</td>
                                        <td class="px-5 py-4 text-sm">
                                            <span class="rounded-full {{ $configuration->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }} px-2.5 py-1 text-xs font-semibold">
                                                {{ $configuration->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-4 text-sm text-slate-700">
                                            <div class="flex flex-wrap gap-2">
                                                @if(data_get($permissions, 'edit'))
                                                    <button type="button" class="rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50">Edit</button>
                                                @endif
                                                @if($configuration->is_active && data_get($permissions, 'deactivate'))
                                                    <button type="button" class="rounded-full border border-rose-200 bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700 hover:bg-rose-100">Deactivate</button>
                                                @elseif(!$configuration->is_active && data_get($permissions, 'activate'))
                                                    <button type="button" class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 hover:bg-emerald-100">Activate</button>
                                                @endif
                                                @if(data_get($permissions, 'delete'))
                                                    <button type="button" class="rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50">Delete</button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="empty-row">
                                        <td colspan="5" class="px-5 py-6 text-center text-sm text-slate-500">No mail configuration has been added yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="mail-configuration-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 px-4 py-8">
        <div class="mx-auto w-full max-w-3xl overflow-hidden rounded-[32px] bg-white shadow-[0_30px_90px_-30px_rgba(15,23,42,0.35)]">
            <form id="mail-configuration-form" class="flex flex-col">
                <div class="border-b border-slate-200 px-6 py-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-sky-600">New rule</p>
                            <h3 id="mail-modal-title" class="mt-2 text-2xl font-semibold text-slate-900">Create Mail Configuration</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-600">Configure notification recipients for the selected state.</p>
                        </div>
                        <button type="button" onclick="closeMailConfigurationModal()" class="inline-flex h-11 w-11 items-center justify-center rounded-full bg-slate-100 text-slate-700 hover:bg-slate-200">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>
                <div class="space-y-6 px-6 py-6">
                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-700">State</label>
                        <div class="rounded-[20px] border border-slate-200 bg-slate-50 px-4 py-3">
                            <select id="state-select" class="w-full rounded-3xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-900 outline-none focus:border-slate-400">
                                <option value="">Select state</option>
                                @if(auth()->user()?->hasRole('Central Admin') || auth()->user()?->hasRole('HO Admin'))
                                    <option value="all">All States</option>
                                @endif
                                @foreach($states as $state)
                                    <option value="{{ $state->state_name }}">{{ $state->state_name }}</option>
                                @endforeach
                            </select>
                            <p class="mt-2 text-xs text-slate-500">Choose the state that this rule applies to.</p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-700">To Mail IDs</label>
                        <div class="rounded-[20px] border border-slate-200 bg-slate-50 px-4 py-3">
                            <div class="flex flex-wrap gap-2 mb-3" id="from-email-chips"></div>
                            <input id="from-email-input" type="email" placeholder="Type email and press Enter, Comma, or Tab" class="w-full bg-transparent text-sm text-slate-900 outline-none placeholder:text-slate-400" onkeydown="handleEmailInput(event, 'from')" autocomplete="off" />
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-sm font-medium text-slate-700">CC Mail IDs</label>
                        <div class="rounded-[20px] border border-slate-200 bg-slate-50 px-4 py-3">
                            <div class="flex flex-wrap gap-2 mb-3" id="cc-email-chips"></div>
                            <input id="cc-email-input" type="email" placeholder="Type email and press Enter, Comma, or Tab" class="w-full bg-transparent text-sm text-slate-900 outline-none placeholder:text-slate-400" onkeydown="handleEmailInput(event, 'cc')" autocomplete="off" />
                        </div>
                    </div>

                    <div id="mail-form-error" class="hidden rounded-xl border border-rose-100 bg-rose-50 px-4 py-3 text-sm text-rose-700"></div>
                </div>

                <div class="flex flex-col gap-3 border-t border-slate-200 px-6 py-5 sm:flex-row sm:justify-end">
                    <button type="button" onclick="closeMailConfigurationModal()" class="inline-flex items-center justify-center rounded-[18px] border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</button>
                    <button type="button" onclick="clearMailConfigForm()" class="inline-flex items-center justify-center rounded-[18px] border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">Reset</button>
                    <button type="button" onclick="saveMailConfiguration()" class="inline-flex items-center justify-center rounded-[18px] bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700">Save</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const fromEmails = [];
        const ccEmails = [];

        function openMailConfigurationModal() {
            document.getElementById('mail-configuration-modal').classList.remove('hidden');
            document.getElementById('mail-configuration-modal').classList.add('flex');
            clearMailConfigForm();
        }

        function closeMailConfigurationModal() {
            document.getElementById('mail-configuration-modal').classList.add('hidden');
            document.getElementById('mail-configuration-modal').classList.remove('flex');
            clearMailConfigForm();
        }

        function clearMailConfigForm() {
            document.getElementById('state-select').value = '';
            document.getElementById('from-email-input').value = '';
            document.getElementById('cc-email-input').value = '';
            fromEmails.length = 0;
            ccEmails.length = 0;
            renderEmailChips('from');
            renderEmailChips('cc');
            hideMailFormError();
        }

        function handleEmailInput(event, type) {
            if (event.key === 'Enter' || event.key === ',') {
                event.preventDefault();
                const input = event.target;
                const value = input.value.replace(/,/g, '').trim();
                if (!value || !validateEmail(value)) {
                    return;
                }

                if (type === 'from' && !fromEmails.includes(value)) {
                    fromEmails.push(value);
                }

                if (type === 'cc' && !ccEmails.includes(value)) {
                    ccEmails.push(value);
                }

                renderEmailChips(type);
                input.value = '';
            }
        }

        function renderEmailChips(type) {
            const container = document.getElementById(`${type}-email-chips`);
            const emails = type === 'from' ? fromEmails : ccEmails;

            container.innerHTML = emails.map(email => `
                <span class="inline-flex items-center rounded-full bg-slate-100 px-3 py-1 text-sm text-slate-700">
                    ${email}
                    <button type="button" onclick="removeEmail('${type}', '${email}')" class="ml-2 text-slate-400 hover:text-slate-700">&times;</button>
                </span>
            `).join('');
        }

        function removeEmail(type, email) {
            if (type === 'from') {
                const index = fromEmails.indexOf(email);
                if (index !== -1) {
                    fromEmails.splice(index, 1);
                }
            } else {
                const index = ccEmails.indexOf(email);
                if (index !== -1) {
                    ccEmails.splice(index, 1);
                }
            }

            renderEmailChips(type);
        }

        function validateEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        }

        function showMailFormError(message) {
            const errorContainer = document.getElementById('mail-form-error');
            errorContainer.textContent = message;
            errorContainer.classList.remove('hidden');
        }

        function hideMailFormError() {
            const errorContainer = document.getElementById('mail-form-error');
            errorContainer.classList.add('hidden');
            errorContainer.textContent = '';
        }

        function saveMailConfiguration() {
            const stateValue = document.getElementById('state-select').value;
            const stateLabel = stateValue === 'all' ? 'All States' : stateValue;

            if (!stateValue) {
                showMailFormError('Please select a state.');
                return;
            }

            if (fromEmails.length === 0) {
                showMailFormError('Please add at least one To mail ID.');
                return;
            }

            const tableBody = document.getElementById('mail-configuration-table-body');
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="px-6 py-4 text-sm font-semibold text-slate-900">${stateLabel}</td>
                <td class="px-6 py-4 text-sm text-slate-600">${fromEmails.join(', ')}</td>
                <td class="px-6 py-4 text-sm text-slate-600">${ccEmails.join(', ') || '-'}</td>
                <td class="px-6 py-4 text-sm">
                    <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">Active</span>
                </td>
                <td class="px-6 py-4 text-sm text-slate-700">
                    <div class="flex flex-wrap gap-2">
                        <button type="button" class="rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50">Edit</button>
                        <button type="button" class="rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50">Delete</button>
                    </div>
                </td>
            `;

            const emptyRow = tableBody.querySelector('.empty-row');
            if (emptyRow) {
                emptyRow.remove();
            }

            tableBody.appendChild(row);
            closeMailConfigurationModal();
        }

        function filterMailConfigurations() {
            const query = document.getElementById('mail-search').value.trim().toLowerCase();
            const rows = document.querySelectorAll('#mail-configuration-table-body tr');
            let visibleCount = 0;

            rows.forEach((row) => {
                if (row.classList.contains('empty-row')) {
                    return;
                }

                const text = row.textContent.toLowerCase();
                const match = query === '' || text.includes(query);

                if (match) {
                    row.classList.remove('hidden');
                    visibleCount += 1;
                } else {
                    row.classList.add('hidden');
                }
            });

            const emptyRow = document.querySelector('#mail-configuration-table-body .empty-row');
            if (emptyRow) {
                emptyRow.classList.toggle('hidden', visibleCount !== 0);
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('mail-search');
            if (searchInput) {
                searchInput.addEventListener('input', filterMailConfigurations);
            }
        });
    </script>
</x-app-layout>
