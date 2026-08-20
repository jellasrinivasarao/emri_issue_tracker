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
                            <p class="text-sm text-slate-600">Create and manage notification delivery rules for
                                projects, roles, and operational scopes.</p>
                        </div>
                        <div class="flex items-center justify-end">
                            <div
                                class="flex items-center justify-end rounded-2xl border border-slate-200 bg-white px-3 py-2 shadow-sm">
                                <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"></path>
                                </svg>
                                <input id="mail-search" type="text" placeholder="Search"
                                    class="ml-2 w-36 bg-transparent text-sm text-slate-900 placeholder:text-slate-400 outline-none" />
                            </div>
                        </div>
                        <div class="flex items-center justify-end gap-3">
                            @if(data_get($permissions, 'export'))
                            <button type="button"
                                class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-100">Export
                                CSV</button>
                            <button type="button"
                                class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-semibold text-emerald-700 hover:bg-emerald-100">Export
                                XLSX</button>
                            <button type="button"
                                class="rounded-lg border border-purple-200 bg-purple-50 px-3 py-2 text-sm font-semibold text-purple-700 hover:bg-purple-100">Export
                                PDF</button>
                            @endif

                            @if(data_get($permissions, 'create'))
                            <button type="button" onclick="openMailConfigurationModal()"
                                class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">Add
                                Rule</button>
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
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Project</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Application</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">To Mail IDs</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">CC Mail IDs</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Status</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="mail-configuration-table-body" class="divide-y divide-slate-200 bg-white">
                                @forelse($mailConfigurations ?? [] as $configuration)
                                    @php
                                        $toEmails = is_array($configuration->to_emails) ? $configuration->to_emails : (json_decode((string) $configuration->to_emails, true) ?: []);
                                        $ccEmails = is_array($configuration->cc_emails) ? $configuration->cc_emails : (json_decode((string) $configuration->cc_emails, true) ?: []);
                                    @endphp
                                    @foreach($configuration->application_rows as $applicationRow)
                                        <tr>
                                            <td class="px-5 py-4 text-sm font-semibold text-slate-900">{{ $configuration->state_name ?? 'All States' }}</td>
                                            <td class="px-5 py-4 text-sm font-semibold text-slate-900">{{ $configuration->project?->project_name ?? 'All Projects' }}</td>
                                            <td class="px-5 py-4 text-sm font-semibold text-slate-900">{{ $applicationRow['name'] }}</td>
                                            <td class="px-5 py-4 text-sm text-slate-600">{{ implode(', ', $toEmails) }}</td>
                                            <td class="px-5 py-4 text-sm text-slate-600">{{ implode(', ', $ccEmails) ?: '-' }}</td>
                                            <td class="px-5 py-4 text-sm"><span class="rounded-full {{ $applicationRow['is_active'] ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }} px-2.5 py-1 text-xs font-semibold">{{ $applicationRow['is_active'] ? 'Active' : 'Inactive' }}</span></td>
                                            <td class="px-5 py-4 text-sm text-slate-700">
                                                <div class="flex flex-wrap gap-2">
                                                    @if(data_get($permissions, 'edit'))
                                                        <button type="button" onclick="editMailConfiguration(@js(['id' => $configuration->mail_configuration_id, 'state_id' => $configuration->state_id, 'project_id' => $configuration->project_id, 'application_id' => $applicationRow['id'], 'state_name' => $configuration->state_name, 'to_emails' => $toEmails, 'cc_emails' => $ccEmails]))" class="rounded-full border border-slate-200 bg-white px-3 py-1 text-xs font-semibold text-slate-700 hover:bg-slate-50">Edit</button>
                                                    @endif
                                                    @if($applicationRow['is_active'] && data_get($permissions, 'deactivate'))
                                                        <form method="POST" action="{{ route('mail.configuration.application.toggle', [$configuration->mail_configuration_id, $applicationRow['id']]) }}" class="inline">@csrf<button type="submit" class="rounded-full border border-rose-200 bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700 hover:bg-rose-100">Disable</button></form>
                                                    @elseif(!$applicationRow['is_active'] && data_get($permissions, 'activate'))
                                                        <form method="POST" action="{{ route('mail.configuration.application.toggle', [$configuration->mail_configuration_id, $applicationRow['id']]) }}" class="inline">@csrf<button type="submit" class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-semibold text-emerald-700 hover:bg-emerald-100">Enable</button></form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @empty
                                    <tr class="empty-row"><td colspan="7" class="px-5 py-6 text-center text-sm text-slate-500">No mail configuration has been added yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="mail-configuration-modal"
        class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 px-4 py-6 sm:py-8">
        <div
            class="mx-auto w-full max-w-5xl overflow-hidden rounded-[24px] border border-slate-200 bg-white shadow-[0_30px_90px_-30px_rgba(15,23,42,0.35)]">
            <form id="mail-configuration-form" method="POST" action="{{ route('mail.configuration.store') }}"
                class="flex flex-col">
                @csrf
                <input type="hidden" id="mail-form-method" name="_method" value="" />
                <input type="hidden" id="state_id_input" name="state_id" value="" />
                <input type="hidden" id="project_id_input" name="project_id" value="" />
                <input type="hidden" id="application_id_input" name="application_id" value="" />
                <input type="hidden" id="state_name_input" name="state_name" value="" />
                <input type="hidden" id="to_emails_input" name="to_emails" value="" />
                <input type="hidden" id="cc_emails_input" name="cc_emails" value="" />
                <div class="border-b border-slate-200 px-6 py-7 sm:px-10 sm:py-8">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-blue-600">New rule</p>
                            <h3 id="mail-modal-title" class="mt-2 text-3xl font-semibold tracking-tight text-slate-950">Create Mail
                                Configuration</h3>
                            <p class="mt-2 text-sm leading-6 text-slate-600">Configure notification recipients for the
                                selected state.</p>
                        </div>
                        <button type="button" onclick="closeMailConfigurationModal()"
                            class="inline-flex h-11 w-11 items-center justify-center rounded-full bg-slate-100 text-slate-800 transition hover:bg-slate-200">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="space-y-8 px-6 py-7 sm:px-10 sm:py-8">
                    <div class="grid gap-6 md:grid-cols-3">
                    <div class="space-y-2">
                        <label class="text-base font-semibold text-slate-800">State</label>
                        <div id="state-picker" class="relative mt-2">
                            <select id="state-select" class="hidden">
                                <option value="">Select state</option>
                                @foreach($states as $state)
                                <option value="{{ $state->state_id }}" data-state-name="{{ $state->state_name }}">{{ $state->state_name }}</option>
                                @endforeach
                            </select>
                            <button type="button" id="state-trigger" class="flex min-h-[50px] w-full items-center justify-between rounded-2xl border border-slate-200 bg-white px-4 py-3 text-left text-sm text-slate-700 shadow-sm transition hover:border-slate-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                                <span id="state-trigger-label">Select state</span>
                                <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" /></svg>
                            </button>
                            <div id="state-options" class="absolute left-0 right-0 z-40 mt-1 hidden overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl">
                                <div class="border-b border-slate-100 p-2"><input id="state-search" type="search" placeholder="Search states" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100" autocomplete="off" /></div>
                                <div id="state-option-list" class="max-h-48 overflow-auto p-1"></div>
                            </div>
                            <p class="mt-2 text-xs text-slate-500">Choose the state that this rule applies to.</p>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-base font-semibold text-slate-800">Project</label>
                        <div id="project-picker" class="relative mt-2">
                            <select id="project-select" disabled class="hidden">
                                <option value="">Select project</option>
                            </select>
                            <button type="button" id="project-trigger" disabled class="flex min-h-[50px] w-full items-center justify-between rounded-2xl border border-slate-200 bg-slate-100 px-4 py-3 text-left text-sm text-slate-500 shadow-sm transition disabled:cursor-not-allowed">
                                <span id="project-trigger-label">Select project</span>
                                <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" /></svg>
                            </button>
                            <div id="project-options" class="absolute left-0 right-0 z-40 mt-1 hidden overflow-hidden rounded-xl border border-slate-200 bg-white shadow-xl">
                                <div class="border-b border-slate-100 p-2"><input id="project-search" type="search" placeholder="Search projects" class="w-full rounded-lg border border-slate-200 px-3 py-2 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100" autocomplete="off" /></div>
                                <div id="project-option-list" class="max-h-48 overflow-auto p-1"></div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-base font-semibold text-slate-800">Application</label>
                        <div id="application-multi-select" class="relative mt-2">
                            <div class="flex min-h-[50px] flex-wrap items-center gap-2 rounded-2xl border border-slate-200 bg-white px-3 py-2 shadow-sm transition focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-100">
                                <div id="application-chips" class="flex flex-wrap gap-1"></div>
                                <input id="application-search" type="search" disabled placeholder="Search applications" class="min-w-[100px] flex-1 bg-transparent px-1 py-1 text-sm text-slate-900 outline-none placeholder:text-slate-400" autocomplete="off" />
                            </div>
                            <div id="application-options" class="absolute left-0 right-0 z-30 mt-1 hidden max-h-56 overflow-auto rounded-xl border border-slate-200 bg-white p-1 shadow-xl"></div>
                        </div>
                    </div>
                    </div>

                    <div class="grid gap-6 md:grid-cols-2">
                    <div class="space-y-2">
                        <label class="text-base font-semibold text-slate-800">To Mail IDs</label>
                        <div class="mt-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                            <div class="flex flex-wrap gap-2 mb-3" id="from-email-chips"></div>
                            <div class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-2 py-1 focus-within:border-blue-400 focus-within:ring-2 focus-within:ring-blue-100">
                                <input id="from-email-input" type="email" placeholder="Type email and press Enter, Comma, or Tab" class="min-w-0 flex-1 bg-transparent px-2 py-2 text-sm text-slate-900 outline-none placeholder:text-slate-400" onkeydown="handleEmailInput(event, 'from')" autocomplete="off" />
                                <button type="button" onclick="addEmailFromInput('from')" aria-label="Add To email" class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-600 text-xl font-medium leading-none text-white transition hover:bg-blue-700">+</button>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label class="text-base font-semibold text-slate-800">CC Mail IDs</label>
                        <div class="mt-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3">
                            <div class="flex flex-wrap gap-2 mb-3" id="cc-email-chips"></div>
                            <div class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-2 py-1 focus-within:border-blue-400 focus-within:ring-2 focus-within:ring-blue-100">
                                <input id="cc-email-input" type="email" placeholder="Type email and press Enter, Comma, or Tab" class="min-w-0 flex-1 bg-transparent px-2 py-2 text-sm text-slate-900 outline-none placeholder:text-slate-400" onkeydown="handleEmailInput(event, 'cc')" autocomplete="off" />
                                <button type="button" onclick="addEmailFromInput('cc')" aria-label="Add CC email" class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-blue-600 text-xl font-medium leading-none text-white transition hover:bg-blue-700">+</button>
                            </div>
                        </div>
                    </div>

                    </div>

                    <div id="mail-form-error"
                        class="hidden rounded-xl border border-rose-100 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                    </div>
                </div>

                <div class="flex flex-col gap-3 border-t border-slate-200 px-6 py-6 sm:flex-row sm:justify-end sm:px-10">
                    <button type="button" onclick="closeMailConfigurationModal()"
                        class="inline-flex items-center justify-center rounded-[18px] border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</button>
                    <button type="button" onclick="clearMailConfigForm()"
                        class="inline-flex items-center justify-center rounded-[18px] border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">Reset</button>
                    <button type="button" onclick="saveMailConfiguration()"
                        class="inline-flex items-center justify-center rounded-[18px] bg-emerald-600 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-emerald-700">Save</button>
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
        document.getElementById('mail-configuration-form').action = @js(route('mail.configuration.store'));
        document.getElementById('mail-form-method').value = '';
        document.getElementById('mail-modal-title').textContent = 'Create Mail Configuration';
        document.getElementById('state-select').value = '';
        document.getElementById('state-trigger-label').textContent = 'Select state';
        document.getElementById('state-options').classList.add('hidden');
        document.getElementById('state_id_input').value = '';
        document.getElementById('project_id_input').value = '';
        document.getElementById('application_id_input').value = '';
        resetDependentSelect('project-select', 'Select project');
        resetApplicationPicker();
        document.getElementById('from-email-input').value = '';
        document.getElementById('cc-email-input').value = '';
        fromEmails.length = 0;
        ccEmails.length = 0;
        renderEmailChips('from');
        renderEmailChips('cc');
        hideMailFormError();
    }

    async function editMailConfiguration(configuration) {
        clearMailConfigForm();
        document.getElementById('mail-configuration-modal').classList.remove('hidden');
        document.getElementById('mail-configuration-modal').classList.add('flex');
        document.getElementById('mail-modal-title').textContent = 'Edit Mail Configuration';
        document.getElementById('mail-configuration-form').action = `{{ url('/mail-configuration') }}/${configuration.id}`;
        document.getElementById('mail-form-method').value = 'PUT';

        document.getElementById('state-select').value = String(configuration.state_id || '');
        document.getElementById('state-select').dispatchEvent(new Event('change', { bubbles: true }));
        document.getElementById('state-trigger-label').textContent = configuration.state_name || 'Select state';

        await loadProjects(configuration.state_id);
        document.getElementById('project-select').value = String(configuration.project_id || '');
        document.getElementById('project-select').dispatchEvent(new Event('change', { bubbles: true }));
        document.getElementById('project-trigger-label').textContent = document.getElementById('project-select').selectedOptions[0]?.textContent || 'Select project';

        await loadApplications(configuration.project_id);
        const application = applicationOptions.find(row => String(row.application_id) === String(configuration.application_id));
        if (application) {
            selectedApplications = [application];
            renderApplicationChips();
        }

        fromEmails.push(...(Array.isArray(configuration.to_emails) ? configuration.to_emails : []));
        ccEmails.push(...(Array.isArray(configuration.cc_emails) ? configuration.cc_emails : []));
        renderEmailChips('from');
        renderEmailChips('cc');
    }

    function resetDependentSelect(id, placeholder) {
        const select = document.getElementById(id);
        select.innerHTML = `<option value="">${placeholder}</option>`;
        select.disabled = true;
        if (id === 'project-select') {
            const trigger = document.getElementById('project-trigger');
            trigger.disabled = true;
            trigger.classList.remove('border-blue-500', 'bg-white', 'text-slate-700');
            trigger.classList.add('bg-slate-100', 'text-slate-500');
            document.getElementById('project-trigger-label').textContent = placeholder;
            document.getElementById('project-options').classList.add('hidden');
        }
    }

    function fillDependentSelect(id, rows, valueKey, labelKey, placeholder) {
        const select = document.getElementById(id);
        select.innerHTML = `<option value="">${placeholder}</option>`;
        rows.forEach(row => {
            const option = document.createElement('option');
            option.value = row[valueKey];
            option.textContent = row[labelKey];
            select.appendChild(option);
        });
        select.disabled = rows.length === 0;
        if (id === 'project-select') {
            const trigger = document.getElementById('project-trigger');
            trigger.disabled = rows.length === 0;
            trigger.classList.toggle('bg-slate-100', rows.length === 0);
            trigger.classList.toggle('text-slate-500', rows.length === 0);
            trigger.classList.toggle('bg-white', rows.length > 0);
            trigger.classList.toggle('text-slate-700', rows.length > 0);
            renderProjectOptions();
        }
    }

    function renderProjectOptions() {
        const select = document.getElementById('project-select');
        const query = document.getElementById('project-search').value.trim().toLowerCase();
        document.getElementById('project-option-list').innerHTML = Array.from(select.options)
            .filter(option => option.value !== '')
            .filter(option => option.textContent.toLowerCase().includes(query))
            .map(option => `<button type="button" data-project-id="${option.value}" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-slate-700 transition hover:bg-blue-50 hover:text-blue-700">${option.textContent}</button>`)
            .join('') || '<p class="px-3 py-2 text-sm text-slate-500">No projects found</p>';
    }

    function renderStateOptions() {
        const select = document.getElementById('state-select');
        const options = document.getElementById('state-option-list');
        const query = document.getElementById('state-search').value.trim().toLowerCase();
        options.innerHTML = Array.from(select.options)
            .filter(option => option.value !== '')
            .filter(option => option.textContent.toLowerCase().includes(query))
            .map(option => `<button type="button" data-state-id="${option.value}" class="block w-full rounded-lg px-3 py-2 text-left text-sm text-slate-700 transition hover:bg-blue-50 hover:text-blue-700">${option.textContent}</button>`)
            .join('') || '<p class="px-3 py-2 text-sm text-slate-500">No states found</p>';
    }

    async function loadProjects(stateId) {
        resetDependentSelect('project-select', 'Select project');
        resetApplicationPicker();
        if (!stateId) return;
        const response = await fetch(`{{ route('mail.configuration.projects') }}?state_id=${encodeURIComponent(stateId)}`, { headers: { 'Accept': 'application/json' } });
        fillDependentSelect('project-select', await response.json(), 'project_id', 'project_name', 'Select project');
    }

    async function loadApplications(projectId) {
        resetApplicationPicker();
        if (!projectId) return;
        const response = await fetch(`{{ route('mail.configuration.applications') }}?project_id=${encodeURIComponent(projectId)}`, { headers: { 'Accept': 'application/json' } });
        applicationOptions = await response.json();
        renderApplicationOptions();
    }

    let applicationOptions = [];
    let selectedApplications = [];

    function resetApplicationPicker() {
        selectedApplications = [];
        applicationOptions = [];
        document.getElementById('application-search').value = '';
        document.getElementById('application-search').disabled = true;
        document.getElementById('application-chips').innerHTML = '';
        document.getElementById('application-options').innerHTML = '';
        document.getElementById('application-options').classList.add('hidden');
    }

    function renderApplicationOptions(show = false) {
        const search = document.getElementById('application-search');
        const options = document.getElementById('application-options');
        search.disabled = applicationOptions.length === 0;
        search.classList.toggle('bg-slate-100', applicationOptions.length === 0);
        const query = search.value.trim().toLowerCase();
        const visible = applicationOptions.filter(app => String(app.application_name).toLowerCase().includes(query));
        const selectedVisible = visible.filter(app => selectedApplications.some(selected => String(selected.application_id) === String(app.application_id)));
        const allVisibleSelected = visible.length > 0 && selectedVisible.length === visible.length;
        options.innerHTML = visible.length
            ? `<label class="flex cursor-pointer items-center gap-2 rounded-lg border-b border-slate-100 px-3 py-2 text-sm font-semibold text-slate-800 hover:bg-slate-50">
                    <input type="checkbox" data-select-all-applications class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500" ${allVisibleSelected ? 'checked' : ''} />
                    <span>Select all${query ? ' matching' : ''} applications</span>
               </label>
               ${visible.map(app => {
                   const checked = selectedApplications.some(selected => String(selected.application_id) === String(app.application_id));
                   return `<label class="flex cursor-pointer items-center gap-2 rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">
                        <input type="checkbox" data-application-id="${app.application_id}" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500" ${checked ? 'checked' : ''} />
                        <span>${app.application_name}</span>
                   </label>`;
               }).join('')}`
            : '<p class="px-3 py-2 text-sm text-slate-500">No applications found</p>';
        if (show) options.classList.remove('hidden');
    }

    function renderApplicationChips() {
        document.getElementById('application-chips').innerHTML = selectedApplications.map(app => `
            <span class="inline-flex items-center gap-1 rounded-full bg-blue-100 px-2.5 py-1 text-xs font-semibold text-blue-700">
                ${app.application_name}
                <button type="button" data-remove-application="${app.application_id}" class="text-blue-500 hover:text-blue-800">&times;</button>
            </span>`).join('');
        document.getElementById('application_id_input').value = selectedApplications[0]?.application_id || '';
        renderApplicationOptions(true);
    }

    function handleEmailInput(event, type) {
        if (event.key === 'Enter' || event.key === ',' || event.key === 'Tab') {
            event.preventDefault();
            addEmailFromInput(type);
        }
    }

    function addEmailFromInput(type) {
        const input = document.getElementById(type === 'from' ? 'from-email-input' : 'cc-email-input');
        const value = input.value.replace(/,/g, '').trim();
        if (!value || !validateEmail(value)) return;

        const emails = type === 'from' ? fromEmails : ccEmails;
        if (!emails.includes(value)) emails.push(value);
        renderEmailChips(type);
        input.value = '';
        input.focus();
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
        const stateOption = document.getElementById('state-select').selectedOptions[0];
        const stateLabel = stateValue === 'all' ? 'All States' : (stateOption?.dataset.stateName || stateOption?.textContent || '');

        // if user typed an email but didn't press Enter, pick it up now
        const rawFrom = document.getElementById('from-email-input').value.trim();
        if (rawFrom) {
            rawFrom.split(',').map(s => s.trim()).filter(Boolean).forEach(v => {
                if (!fromEmails.includes(v)) fromEmails.push(v);
            });
        }

        const rawCc = document.getElementById('cc-email-input').value.trim();
        if (rawCc) {
            rawCc.split(',').map(s => s.trim()).filter(Boolean).forEach(v => {
                if (!ccEmails.includes(v)) ccEmails.push(v);
            });
        }

        if (!stateValue) {
            showMailFormError('Please select a state.');
            return;
        }

        if (fromEmails.length === 0) {
            showMailFormError('Please add at least one To mail ID.');
            return;
        }
        // set hidden inputs and submit to server
        document.getElementById('state_name_input').value = stateLabel;
        document.getElementById('state_id_input').value = stateValue === 'all' ? '' : stateValue;
        document.getElementById('project_id_input').value = document.getElementById('project-select').value;
        document.querySelectorAll('input[name="application_ids[]"]').forEach(input => input.remove());
        selectedApplications.forEach(app => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'application_ids[]';
            input.value = app.application_id;
            document.getElementById('mail-configuration-form').appendChild(input);
        });
        document.getElementById('to_emails_input').value = fromEmails.join(',');
        document.getElementById('cc_emails_input').value = ccEmails.join(',');

        document.getElementById('mail-configuration-form').submit();
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

    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('mail-search');
        if (searchInput) {
            searchInput.addEventListener('input', filterMailConfigurations);
        }
        document.getElementById('state-select')?.addEventListener('change', function () {
            document.getElementById('state_id_input').value = this.value === 'all' ? '' : this.value;
            const selected = this.selectedOptions[0];
            document.getElementById('state-trigger-label').textContent = selected?.dataset.stateName || selected?.textContent || 'Select state';
            loadProjects(this.value);
        });
        renderStateOptions();
        document.getElementById('state-trigger')?.addEventListener('click', function () {
            document.getElementById('state-search').value = '';
            renderStateOptions();
            document.getElementById('state-options').classList.toggle('hidden');
        });
        document.getElementById('state-search')?.addEventListener('input', renderStateOptions);
        document.getElementById('state-options')?.addEventListener('click', function (event) {
            const option = event.target.closest('[data-state-id]');
            if (!option) return;
            const select = document.getElementById('state-select');
            select.value = option.dataset.stateId;
            select.dispatchEvent(new Event('change', { bubbles: true }));
            document.getElementById('state-options').classList.add('hidden');
        });
        document.getElementById('project-select')?.addEventListener('change', function () {
            document.getElementById('project_id_input').value = this.value;
            document.getElementById('project-trigger-label').textContent = this.selectedOptions[0]?.textContent || 'Select project';
            loadApplications(this.value);
        });
        document.getElementById('project-trigger')?.addEventListener('click', function () {
            if (this.disabled) return;
            document.getElementById('project-search').value = '';
            renderProjectOptions();
            document.getElementById('project-options').classList.toggle('hidden');
        });
        document.getElementById('project-search')?.addEventListener('input', renderProjectOptions);
        document.getElementById('project-options')?.addEventListener('click', function (event) {
            const option = event.target.closest('[data-project-id]');
            if (!option) return;
            const select = document.getElementById('project-select');
            select.value = option.dataset.projectId;
            select.dispatchEvent(new Event('change', { bubbles: true }));
            document.getElementById('project-options').classList.add('hidden');
        });
        document.getElementById('application-select')?.addEventListener('change', function () {
            document.getElementById('application_id_input').value = this.value;
        });
        document.getElementById('application-search')?.addEventListener('focus', renderApplicationOptions);
        document.getElementById('application-search')?.addEventListener('input', () => renderApplicationOptions(true));
        document.getElementById('application-options')?.addEventListener('click', function (event) {
            const selectAll = event.target.closest('[data-select-all-applications]');
            const checkbox = event.target.closest('[data-application-id]');
            const query = document.getElementById('application-search').value.trim().toLowerCase();
            const visible = applicationOptions.filter(app => String(app.application_name).toLowerCase().includes(query));

            if (selectAll) {
                const visibleIds = new Set(visible.map(app => String(app.application_id)));
                selectedApplications = selectAll.checked
                    ? [...selectedApplications, ...visible.filter(app => !selectedApplications.some(selected => String(selected.application_id) === String(app.application_id)))]
                    : selectedApplications.filter(app => !visibleIds.has(String(app.application_id)));
                renderApplicationChips();
                return;
            }

            if (checkbox) {
                const app = applicationOptions.find(row => String(row.application_id) === String(checkbox.dataset.applicationId));
                if (checkbox.checked && app && !selectedApplications.some(selected => String(selected.application_id) === String(app.application_id))) {
                    selectedApplications.push(app);
                } else if (!checkbox.checked) {
                    selectedApplications = selectedApplications.filter(selected => String(selected.application_id) !== String(checkbox.dataset.applicationId));
                }
                renderApplicationChips();
            }
        });
        document.getElementById('application-chips')?.addEventListener('click', function (event) {
            const button = event.target.closest('[data-remove-application]');
            if (!button) return;
            selectedApplications = selectedApplications.filter(app => String(app.application_id) !== String(button.dataset.removeApplication));
            renderApplicationChips();
        });
        document.addEventListener('click', function (event) {
            if (!event.target.closest('#state-picker')) {
                document.getElementById('state-options')?.classList.add('hidden');
            }
            if (!event.target.closest('#project-picker')) {
                document.getElementById('project-options')?.classList.add('hidden');
            }
            if (!event.target.closest('#application-multi-select')) {
                document.getElementById('application-options')?.classList.add('hidden');
            }
        });
    });
    </script>
</x-app-layout>