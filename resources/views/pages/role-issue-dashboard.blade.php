<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Operational Dashboard') }}
        </h2>
    </x-slot>

    <div x-data="drawerState()" x-init="init()">
        <div class="mx-auto max-w-full px-4 sm:px-6 lg:px-8">
            <div class="grid gap-4 xl:grid-cols-[minmax(460px,1fr)_minmax(700px,1fr)]">
                <div class="rounded-[18px] border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="mb-4">
                        <p class="text-sm font-semibold text-slate-900">Issue Summary</p>
                        <p class="mt-1 text-sm text-slate-500">Overview of all issues in the system.</p>
                    </div>
                    <div class="grid gap-3 md:grid-cols-2 xl:grid-cols-4">
                        @php
                            $statusCards = [
                                ['label' => 'All Issues', 'value' => (string) ($statusSummary['all'] ?? 0), 'color' => 'blue', 'icon' => 'M3 7h18M3 12h18M3 17h18', 'bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'filterStatus' => 'all'],
                                ['label' => 'In-Process', 'value' => (string) ($statusSummary['in_process'] ?? 0), 'color' => 'emerald', 'icon' => 'M5 13l4 4L19 7', 'bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'filterStatus' => 'in_process'],
                                ['label' => 'Resolved', 'value' => (string) ($statusSummary['resolved'] ?? 0), 'color' => 'amber', 'icon' => 'M9 12.75L11.25 15 15 9.75', 'bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'filterStatus' => 'resolved'],
                                ['label' => 'Closed', 'value' => (string) ($statusSummary['closed'] ?? 0), 'color' => 'violet', 'icon' => 'M6 18L18 6M6 6l12 12', 'bg' => 'bg-violet-50', 'text' => 'text-violet-700', 'filterStatus' => 'closed'],
                            ];
                        @endphp
                        @foreach($statusCards as $card)
                            @php
                                $active = ((string) ($filterValues['status_id'] ?? '') === (string) $card['filterStatus']);
                                $currentFilterValues = ['state_id' => $filterValues['state_id'] ?? '', 'project_id' => $filterValues['project_id'] ?? '', 'application_id' => $filterValues['application_id'] ?? '', 'priority_id' => $filterValues['priority_id'] ?? '', 'date_from' => $filterValues['date_from'] ?? '', 'date_to' => $filterValues['date_to'] ?? '', 'ticket_id' => $filterValues['ticket_id'] ?? '', 'search' => $filterValues['search'] ?? ''];
                            @endphp
                            <form method="GET" action="{{ route('role.issue.dashboard') }}" class="block">
                                @foreach($currentFilterValues as $field => $value)
                                    @if($value !== '')
                                        <input type="hidden" name="{{ $field }}" value="{{ $value }}">
                                    @endif
                                @endforeach
                                <input type="hidden" name="status_id" value="{{ $card['filterStatus'] }}">
                                <button type="submit" class="w-full rounded-[14px] border border-slate-200 bg-white p-3 text-left shadow-sm transition hover:-translate-y-0.5 hover:shadow-md {{ $active ? 'ring-2 ring-blue-500 ring-offset-1' : '' }}">
                                    <div class="flex items-center justify-between gap-2">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl {{ $card['bg'] }} {{ $card['text'] }}">
                                            <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $card['icon'] }}"></path></svg>
                                        </div>
                                        <span class="rounded-full bg-slate-100 px-2 py-1 text-[9px] font-semibold uppercase tracking-[0.2em] text-slate-500">{{ $active ? 'Open' : 'View' }}</span>
                                    </div>
                                    <p class="mt-3 text-[10px] uppercase tracking-[0.2em] text-slate-500">{{ $card['label'] }}</p>
                                    <p class="mt-1 text-2xl font-semibold text-slate-900">{{ $card['value'] }}</p>
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>

                <form id="roleIssueFilterForm" method="GET" action="{{ route('role.issue.dashboard') }}" class="rounded-[18px] border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="mb-4">
                        <p class="text-sm font-semibold text-slate-900">Filters</p>
                        <p class="mt-1 text-sm text-slate-500">Quick filter your ticket queue.</p>
                    </div>
                    <input type="hidden" name="status_id" value="{{ $filterValues['status_id'] ?? '' }}">
                    <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 xl:grid-cols-4">
                        <select name="state_id" onchange="this.form.submit()" class="rounded-[12px] border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All States</option>
                            @foreach($stateOptions as $state)
                                <option value="{{ $state->state_id }}" {{ (string) ($filterValues['state_id'] ?? '') === (string) $state->state_id ? 'selected' : '' }}>{{ $state->state_name }}</option>
                            @endforeach
                        </select>
                        <select name="project_id" onchange="this.form.submit()" class="rounded-[12px] border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Projects</option>
                            @foreach($projectOptions as $project)
                                <option value="{{ $project->project_id }}" {{ (string) ($filterValues['project_id'] ?? '') === (string) $project->project_id ? 'selected' : '' }}>{{ $project->project_name }}</option>
                            @endforeach
                        </select>
                        <select name="application_id" class="rounded-[12px] border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Applications</option>
                            @foreach($applicationOptions as $application)
                                <option value="{{ $application->application_id }}" {{ (string) ($filterValues['application_id'] ?? '') === (string) $application->application_id ? 'selected' : '' }}>{{ $application->application_name }}</option>
                            @endforeach
                        </select>
                        <select name="priority_id" class="rounded-[12px] border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">All Priorities</option>
                            @foreach($priorityOptions as $priority)
                                <option value="{{ $priority->priority_id }}" {{ (string) ($filterValues['priority_id'] ?? '') === (string) $priority->priority_id ? 'selected' : '' }}>{{ $priority->priority_name }}</option>
                            @endforeach
                        </select>
                        <input type="date" name="date_from" value="{{ $filterValues['date_from'] ?? '' }}" class="w-full rounded-[12px] border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 shadow-sm" placeholder="From" />
                        <input type="date" name="date_to" value="{{ $filterValues['date_to'] ?? '' }}" class="w-full rounded-[12px] border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-700 shadow-sm" placeholder="To" />
                        <input type="text" name="ticket_id" value="{{ $filterValues['ticket_id'] ?? '' }}" class="w-full rounded-[12px] border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm" placeholder="Ticket ID" />
                        <input type="text" name="search" value="{{ $filterValues['search'] ?? '' }}" class="w-full rounded-[12px] border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 shadow-sm" placeholder="Search" />
                    </div>
                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        <button type="submit" class="rounded-[12px] bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700">Apply Filters</button>
                        <a href="{{ route('role.issue.dashboard') }}" class="rounded-[12px] border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Reset</a>
                    </div>
                </form>
            </div>

            <div class="mt-4 rounded-[18px] border border-slate-200 bg-white p-4 shadow-sm">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-slate-900">Issue Queue</p>
                        <p class="mt-1 text-sm text-slate-500">Click any ticket ID to open details.</p>
                    </div>
                    <a href="#" onclick="exportIssueQueue(event)" class="text-sm font-semibold text-blue-600 hover:text-blue-700">Export</a>
                </div>

                <div class="mt-3 max-h-[230px] overflow-y-auto rounded-[12px] border border-slate-200">
                    @if(count($issues) === 0)
                        <div class="flex items-center justify-center py-16 text-slate-500">
                            <div class="text-center">
                                <svg class="mx-auto h-12 w-12 text-slate-400 mb-3" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375M12 9.75v.75m0 0v.75m0-.75h.375m-.375 0H11.25"></path>
                                </svg>
                                <p class="text-sm font-semibold text-slate-600">No records available</p>
                                <p class="mt-1 text-xs text-slate-500">Try adjusting your filters or check back later.</p>
                            </div>
                        </div>
                    @else
                        <table class="min-w-full divide-y divide-slate-200 text-left text-sm text-slate-700">
                            <thead class="sticky top-0 z-10 bg-slate-50 text-[10px] uppercase tracking-[0.2em] text-slate-500">
                                <tr>
                                    <th class="px-3 py-3">Ticket ID</th>
                                    <th class="px-3 py-3">Issue Title</th>
                                    <th class="px-3 py-3">State</th>
                                    <th class="px-3 py-3">Project</th>
                                    <th class="px-3 py-3">Application</th>
                                    <th class="px-3 py-3">Module</th>
                                    <th class="px-3 py-3">Vendors</th>
                                    <th class="px-3 py-3">Status</th>
                                    <th class="px-3 py-3">Priority</th>
                                    <th class="px-3 py-3">Updated On</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @foreach($issues as $ticket)
                                    <tr class="cursor-pointer hover:bg-slate-50" @click="const tick = @js($ticket); window.__EMRI_LOG_TICKET_BINDING?.(tick); drawerOpen = true; selectedTicket = tick; window.__EMRI_CURRENT_TICKET = tick; selectedStatus = ''; activeTab = 'details'; if (typeof window.refreshVendorOptions === 'function') { window.refreshVendorOptions(tick); }">
                                        <td class="px-3 py-2.5 font-semibold text-slate-900">
                                            <a href="#" @click.prevent="const tick = @js($ticket); window.__EMRI_LOG_TICKET_BINDING?.(tick); drawerOpen = true; selectedTicket = tick; window.__EMRI_CURRENT_TICKET = tick; selectedStatus = ''; activeTab = 'details'; if (typeof window.refreshVendorOptions === 'function') { window.refreshVendorOptions(tick); }" class="inline-block text-blue-600 hover:text-blue-800 underline decoration-blue-300 decoration-1 underline-offset-2">{{ $ticket['id'] }}</a>
                                        </td>
                                        <td class="px-3 py-2.5">{{ $ticket['title'] }}</td>
                                        <td class="px-3 py-2.5">{{ $ticket['state'] }}</td>
                                        <td class="px-3 py-2.5">{{ $ticket['project'] }}</td>
                                        <td class="px-3 py-2.5">{{ $ticket['application'] }}</td>
                                        <td class="px-3 py-2.5">{{ $ticket['module'] }}</td>
                                        <td class="px-3 py-2.5">
                                            @if(!empty($ticket['vendor_progress']['vendors']))
                                                <div class="flex flex-col gap-1">
                                                    @foreach($ticket['vendor_progress']['vendors'] as $vendor)
                                                        <div class="flex items-center gap-1">
                                                            <span class="text-[11px] font-medium text-slate-700">{{ $vendor['vendor_name'] }}</span>
                                                            @if($vendor['is_active'])
                                                                @if($vendor['is_resolved'])
                                                                    <span class="inline-flex rounded-full bg-green-100 px-1.5 py-0.5 text-[9px] font-semibold text-green-700">Resolved</span>
                                                                @else
                                                                    <span class="inline-flex rounded-full bg-yellow-100 px-1.5 py-0.5 text-[9px] font-semibold text-yellow-700">{{ $vendor['status_name'] }}</span>
                                                                @endif
                                                            @else
                                                                <span class="inline-flex rounded-full bg-red-100 px-1.5 py-0.5 text-[9px] font-semibold text-red-700">Rejected</span>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-[11px] text-slate-400">—</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2.5">
                                            @if(!empty($ticket['vendor_progress']['vendors']))
                                                <div class="flex flex-col gap-1">
                                                    @foreach($ticket['vendor_progress']['vendors'] as $vendor)
                                                        <div class="flex items-center gap-1 text-[10px]">
                                                            <span class="font-medium text-slate-600">{{ $vendor['vendor_name'] }}</span>
                                                            <span class="text-slate-400">-</span>
                                                            @if($vendor['is_active'])
                                                                @if($vendor['is_resolved'])
                                                                    <span class="inline-flex rounded-full bg-green-100 px-2 py-0.5 font-semibold text-green-700">Resolved</span>
                                                                @else
                                                                    <span class="inline-flex rounded-full bg-yellow-100 px-2 py-0.5 font-semibold text-yellow-700">{{ $vendor['status_name'] }}</span>
                                                                @endif
                                                            @else
                                                                <span class="inline-flex rounded-full bg-red-100 px-2 py-0.5 font-semibold text-red-700">Rejected</span>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="inline-flex rounded-full bg-emerald-100 px-2 py-1 text-[10px] font-semibold text-emerald-700">{{ $ticket['status'] }}</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-2.5">
                                            <span class="inline-flex rounded-full bg-red-100 px-2 py-1 text-[10px] font-semibold text-red-700">{{ $ticket['priority'] }}</span>
                                        </td>
                                        <td class="px-3 py-2.5">{{ $ticket['updated_on'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

                <div class="mt-3 flex items-center justify-between text-[12px] text-slate-500">
                    <span>Showing {{ min(count($issues), 10) }} of {{ count($issues) }} issues</span>
                </div>
            </div>
        </div>

        <script>
            window.__EMRI_VENDOR_DATA = window.__EMRI_VENDOR_DATA || {
                vendorOptions: [],
                vendorStateMappings: [],
                currentStateId: null,
                currentProjectId: null,
                selectedVendorIds: []
            };

            window.__EMRI_LOG_TICKET_BINDING = function (ticket) {
                const snapshot = ticket ? JSON.parse(JSON.stringify(ticket)) : null;
                console.log('=== TICKET BINDING LOG ===', snapshot);
                console.log('bound ticket diagnostic:', {
                    id: ticket?.id ?? null,
                    issue_id: ticket?.issue_id ?? null,
                    title: ticket?.title ?? null,
                    state_id: ticket?.state_id ?? null,
                    project_id: ticket?.project_id ?? null,
                    status: ticket?.status ?? null,
                    application: ticket?.application ?? null,
                    module: ticket?.module ?? null,
                });
                return ticket;
            };

            window.__EMRI_getAvailableVendorIds = function (stateId, projectId) {
                const mappings = window.__EMRI_VENDOR_DATA.vendorStateMappings || [];
                if (!stateId || !projectId) {
                    return [];
                }

                const ids = mappings
                    .filter((mapping) => String(mapping.state_id) === String(stateId) && String(mapping.project_id) === String(projectId))
                    .map((mapping) => String(mapping.vendor_id));

                return [...new Set(ids)];
            };

            window.__EMRI_renderVendorList = function (filter = '') {
                console.log('=== WINDOW RENDER LIST CALLED ===', filter);
                const container = document.getElementById('vendor-multi-select');
                if (!container) {
                    console.log('vendor list container not found yet');
                    return;
                }

                const list = container.querySelector('[data-multi-select-list]');
                const input = container.querySelector('[data-multi-select-input]');
                if (!list) {
                    console.log('vendor list element missing');
                    return;
                }

                const stateId = window.__EMRI_VENDOR_DATA.currentStateId;
                const projectId = window.__EMRI_VENDOR_DATA.currentProjectId;
                const availableIds = window.__EMRI_getAvailableVendorIds(stateId, projectId);
                const options = (window.__EMRI_VENDOR_DATA.vendorOptions || []).filter((vendor) => availableIds.includes(String(vendor.vendor_id)));
                const selectedIds = (window.__EMRI_VENDOR_DATA.selectedVendorIds || []).map(String);
                const query = String(filter || '').trim().toLowerCase();

                list.innerHTML = '';

                const filtered = options.filter((vendor) => {
                    const label = String(vendor.vendor_name || '').toLowerCase();
                    const isSelected = selectedIds.includes(String(vendor.vendor_id));
                    return !isSelected && (query === '' || label.includes(query));
                });

                if (!filtered.length) {
                    const empty = document.createElement('div');
                    empty.className = 'px-3 py-2 text-sm text-slate-500';
                    empty.textContent = 'No vendors mapped for this state/project.';
                    list.appendChild(empty);
                    list.classList.remove('hidden');
                    return;
                }

                filtered.forEach((vendor) => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'w-full px-3 py-2 text-left text-sm text-slate-700 hover:bg-slate-50';
                    button.dataset.vendorId = String(vendor.vendor_id);
                    button.textContent = vendor.vendor_name || '';
                    list.appendChild(button);
                });

                list.classList.remove('hidden');
                if (input) input.focus();
            };

            window.__EMRI_renderVendorChips = function () {
                const container = document.getElementById('vendor-multi-select');
                if (!container) return;

                const chipsContainer = container.querySelector('[data-multi-select-chips]');
                const hiddenContainer = container.querySelector('[data-multi-select-hidden]');
                if (!chipsContainer || !hiddenContainer) return;

                chipsContainer.innerHTML = '';
                hiddenContainer.innerHTML = '';

                const selectedIds = window.__EMRI_VENDOR_DATA.selectedVendorIds || [];
                selectedIds.forEach((vendorId) => {
                    const vendor = (window.__EMRI_VENDOR_DATA.vendorOptions || []).find((option) => String(option.vendor_id) === String(vendorId));
                    if (!vendor) return;

                    const chip = document.createElement('span');
                    chip.className = 'inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-sm text-slate-700';
                    chip.textContent = vendor.vendor_name || '';

                    const removeButton = document.createElement('button');
                    removeButton.type = 'button';
                    removeButton.className = 'rounded-full bg-slate-200 px-1 text-slate-500 hover:bg-slate-300';
                    removeButton.textContent = '×';
                    removeButton.addEventListener('click', function () {
                        window.__EMRI_VENDOR_DATA.selectedVendorIds = (window.__EMRI_VENDOR_DATA.selectedVendorIds || []).filter((id) => String(id) !== String(vendorId));
                        window.__EMRI_renderVendorChips();
                        window.__EMRI_renderVendorList('');
                    });

                    chip.appendChild(removeButton);
                    chipsContainer.appendChild(chip);

                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = 'vendor_ids[]';
                    hiddenInput.value = String(vendorId);
                    hiddenContainer.appendChild(hiddenInput);
                });
            };

            window.__EMRI_vendorMultiSelectReady = function () {
                const container = document.getElementById('vendor-multi-select');
                if (!container) {
                    return false;
                }

                return !!(container.querySelector('[data-multi-select-list]') && container.querySelector('[data-multi-select-input]') && container.querySelector('[data-multi-select-chips]'));
            };

            window.__EMRI_retryVendorRender = function (ticket, attempt = 0) {
                const maxAttempts = 25;
                const ready = window.__EMRI_vendorMultiSelectReady();

                if (ready) {
                    if (typeof window.initializeVendorMultiSelect === 'function') {
                        window.initializeVendorMultiSelect();
                    }
                    if (typeof window.__EMRI_renderVendorChips === 'function') {
                        window.__EMRI_renderVendorChips();
                    }
                    if (typeof window.__EMRI_renderVendorList === 'function') {
                        window.__EMRI_renderVendorList('');
                    }
                    return true;
                }

                if (attempt >= maxAttempts) {
                    console.warn('vendor multi-select did not render after retries');
                    return false;
                }

                console.log('vendor-multi-select not rendered yet; retrying in 50ms', { attempt: attempt + 1, maxAttempts });
                setTimeout(() => {
                    window.__EMRI_retryVendorRender(ticket, attempt + 1);
                }, 50);
                return false;
            };

            window.refreshVendorOptions = function (ticket) {
                console.log('=== REFRESH VENDOR OPTIONS CALLED ===', ticket);
                const vendorData = window.__EMRI_VENDOR_DATA || {};

                vendorData.currentStateId = (ticket && ticket.state_id !== undefined && ticket.state_id !== null) ? ticket.state_id : null;
                vendorData.currentProjectId = (ticket && ticket.project_id !== undefined && ticket.project_id !== null) ? ticket.project_id : null;

                const existingVendorRows = Array.isArray(ticket?.vendor_progress?.vendors) ? ticket.vendor_progress.vendors : [];
                const activeVendorIds = existingVendorRows
                    .filter((vendor) => vendor && vendor.is_active !== false)
                    .map((vendor) => String(vendor.vendor_id ?? ''))
                    .filter(Boolean);
                vendorData.selectedVendorIds = [...new Set(activeVendorIds)];

                console.log('global refreshVendorOptions: state_id=', vendorData.currentStateId, 'project_id=', vendorData.currentProjectId);
                console.log('global vendor data counts:', {
                    vendorOptions: (window.__EMRI_VENDOR_DATA.vendorOptions || []).length,
                    mappings: (window.__EMRI_VENDOR_DATA.vendorStateMappings || []).length
                });

                const stateId = vendorData.currentStateId;
                const projectId = vendorData.currentProjectId;
                const vendorUrl = '{{ route('role.issue.vendor.options') }}?state_id=' + encodeURIComponent(stateId ?? '') + '&project_id=' + encodeURIComponent(projectId ?? '');

                console.log('vendor query request URL:', vendorUrl);
                fetch(vendorUrl, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    credentials: 'same-origin'
                })
                .then((response) => response.json())
                .then((payload) => {
                    console.log('vendor query response payload:', payload);
                    const vendorRows = Array.isArray(payload?.data) ? payload.data : [];
                    const dedupedRows = vendorRows.filter((vendor, index, arr) => {
                        const vendorId = String(vendor.vendor_id ?? '');
                        return arr.findIndex((item) => String(item.vendor_id ?? '') === vendorId) === index;
                    });
                    window.__EMRI_VENDOR_DATA.vendorOptions = dedupedRows;
                    window.__EMRI_VENDOR_DATA.vendorStateMappings = dedupedRows.map((vendor) => ({
                        state_id: stateId,
                        project_id: projectId,
                        vendor_id: vendor.vendor_id,
                    }));
                    console.log('vendor data replaced from mapped query result:', {
                        vendorOptions: window.__EMRI_VENDOR_DATA.vendorOptions.length,
                        mappings: window.__EMRI_VENDOR_DATA.vendorStateMappings.length,
                        rows: dedupedRows,
                    });

                    if (!window.__EMRI_vendorMultiSelectReady()) {
                        window.__EMRI_retryVendorRender(ticket, 0);
                        return;
                    }

                    const multiselectContainer = document.getElementById('vendor-multi-select');
                    const list = multiselectContainer ? multiselectContainer.querySelector('[data-multi-select-list]') : null;
                    const input = multiselectContainer ? multiselectContainer.querySelector('[data-multi-select-input]') : null;
                    if (!multiselectContainer || !list || !input) {
                        console.log('vendor list/input not ready yet; retrying after render loop');
                        window.__EMRI_retryVendorRender(ticket, 0);
                        return;
                    }

                    if (typeof window.initializeVendorMultiSelect === 'function') {
                        window.initializeVendorMultiSelect();
                    }
                    window.__EMRI_renderVendorChips();
                    window.__EMRI_renderVendorList('');
                    list.classList.remove('hidden');
                    input.focus();
                })
                .catch((error) => {
                    console.error('vendor query fetch error:', error);
                });
            };

            window.refreshIssueDashboard = function refreshIssueDashboard() {
                fetch('{{ route('role.issue.dashboard') }}', {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html'
                    },
                    credentials: 'same-origin'
                })
                .then((response) => response.text())
                .then((html) => {
                    if (typeof replacePageContent === 'function') {
                        replacePageContent(html, '{{ route('role.issue.dashboard') }}');
                    } else {
                        window.location.reload();
                    }
                })
                .catch(() => {
                    window.location.reload();
                });
            };

            document.addEventListener('DOMContentLoaded', function () {
                const updateForm = document.getElementById('issueUpdateForm');
                const flashBox = updateForm ? updateForm.querySelector('#drawerUpdateMessage') : null;
                const statusOptions = @json(collect($statusOptions)->map(function ($status) {
                    return ['status_id' => (int) $status['status_id'], 'status_name' => $status['status_name']];
                })->all());

                if (!updateForm || !flashBox) {
                    return;
                }

                updateForm.addEventListener('submit', function (event) {
                    event.preventDefault();

                    const submitButton = updateForm.querySelector('button[type="submit"]');
                    if (submitButton) {
                        submitButton.disabled = true;
                    }

                    const formData = new FormData(updateForm);
                    const selectedStatusName = formData.get('status_name');
                    const selectedVendorId = formData.get('vendor_id');
                    const selectedVendorStatusId = formData.get('vendor_status_id');
                    const isVendorAssignment = String(selectedStatusName || '').trim().toLowerCase() === 'vendor assignment' || String(selectedStatusName || '').trim().toLowerCase() === 'escalate to vendor';
                    const vendorStatusId = selectedVendorStatusId || statusOptions.find((status) => String(status.status_name) === String(selectedStatusName))?.status_id;
                    const vendorUpdateUrl = updateForm.dataset.vendorStatusUrl || (window.location.origin + '/issues/' + formData.get('issue_id') + '/vendor-status');

                    if (!isVendorAssignment && selectedVendorId && vendorStatusId) {
                        formData.set('vendor_status_id', String(vendorStatusId));
                        formData.set('vendor_id', String(selectedVendorId));
                        formData.delete('status_name');
                        formData.delete('status_id');

                        fetch(vendorUpdateUrl, {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || formData.get('_token')
                            },
                            body: formData
                        })
                        .then(async (response) => {
                            const contentType = response.headers.get('content-type') || '';
                            let payload = {};

                            if (contentType.includes('application/json')) {
                                payload = await response.json();
                            }

                            if (!response.ok) {
                                throw new Error(payload.message || 'Unable to update vendor status.');
                            }

                            const message = payload.message || 'Vendor status updated successfully.';
                            flashBox.className = 'mb-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800';
                            flashBox.innerHTML = message + ' <button type="button" data-close-banner class="ml-2 rounded-full bg-white px-2 py-1 text-xs font-bold text-slate-700 hover:bg-slate-100">×</button>';
                            flashBox.classList.remove('hidden');

                            const closeBanner = flashBox.querySelector('[data-close-banner]');
                            if (closeBanner) {
                                closeBanner.addEventListener('click', function () {
                                    flashBox.classList.add('hidden');
                                    refreshIssueDashboard();
                                });
                            }

                            updateForm.reset();
                            refreshIssueDashboard();
                        })
                        .catch((error) => {
                            const message = error.message || 'Unable to update vendor status.';
                            flashBox.className = 'mb-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-800';
                            flashBox.innerHTML = message + ' <button type="button" data-close-banner class="ml-2 rounded-full bg-white px-2 py-1 text-xs font-bold text-slate-700 hover:bg-slate-100">×</button>';
                            flashBox.classList.remove('hidden');

                            const closeBanner = flashBox.querySelector('[data-close-banner]');
                            if (closeBanner) {
                                closeBanner.addEventListener('click', function () {
                                    flashBox.classList.add('hidden');
                                    refreshIssueDashboard();
                                });
                            }
                        })
                        .finally(() => {
                            if (submitButton) {
                                submitButton.disabled = false;
                            }
                        });

                        return;
                    }

                    fetch(updateForm.action, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(async (response) => {
                        const contentType = response.headers.get('content-type') || '';
                        let payload = {};

                        if (contentType.includes('application/json')) {
                            payload = await response.json();
                        }

                        if (!response.ok) {
                            throw new Error(payload.message || 'Unable to update the ticket.');
                        }

                        const message = payload.message || 'Ticket status updated successfully.';
                        flashBox.className = 'mb-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800';
                        flashBox.innerHTML = message + ' <button type="button" data-close-banner class="ml-2 rounded-full bg-white px-2 py-1 text-xs font-bold text-slate-700 hover:bg-slate-100">×</button>';
                        flashBox.classList.remove('hidden');

                        const closeBanner = flashBox.querySelector('[data-close-banner]');
                        if (closeBanner) {
                            closeBanner.addEventListener('click', function () {
                                flashBox.classList.add('hidden');
                                refreshIssueDashboard();
                            });
                        }

                        const closeButton = updateForm.closest('[x-show="showUpdate"]');
                        if (closeButton) {
                            closeButton.closest('[x-data]')?.querySelector('[x-click="showUpdate = !showUpdate"]')?.click();
                        }

                        updateForm.reset();
                    })
                    .catch((error) => {
                        const message = error.message || 'Unable to update the ticket.';
                        flashBox.className = 'mb-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-800';
                        flashBox.innerHTML = message + ' <button type="button" data-close-banner class="ml-2 rounded-full bg-white px-2 py-1 text-xs font-bold text-slate-700 hover:bg-slate-100">×</button>';
                        flashBox.classList.remove('hidden');

                        const closeBanner = flashBox.querySelector('[data-close-banner]');
                        if (closeBanner) {
                            closeBanner.addEventListener('click', function () {
                                flashBox.classList.add('hidden');
                                refreshIssueDashboard();
                            });
                        }
                    })
                    .finally(() => {
                        if (submitButton) {
                            submitButton.disabled = false;
                        }
                    });
                });
            });
        </script>

        <div class="relative min-h-0">
                    <div x-show="drawerOpen && selectedTicket" x-cloak class="fixed left-0 right-0 z-30 bg-slate-900/40 transition-opacity duration-200" style="top:var(--header-height,64px);height:calc(100% - var(--header-height,64px));"></div>
                    <aside x-show="drawerOpen && selectedTicket" x-cloak class="fixed right-0 z-40 w-full max-w-[520px] overflow-y-auto border-l border-slate-200 bg-white px-6 py-6 shadow-2xl transition duration-300 md:w-[520px]" style="top:var(--header-height,64px);height:calc(100% - var(--header-height,64px));">
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="text-xs uppercase tracking-[0.32em] text-slate-500">Ticket Details</p>
                                <h3 class="mt-2 text-xl font-semibold text-slate-900" x-text="selectedTicket ? selectedTicket.id : '—'">—</h3>
                                <div class="mt-2 flex flex-wrap items-center gap-2 text-sm">
                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-700" x-text="selectedTicket ? selectedTicket.status : '—'">—</span>
                                    <span class="rounded-full bg-red-100 px-3 py-1 text-red-700" x-text="selectedTicket ? selectedTicket.priority : '—'">—</span>
                                </div>
                            </div>
                            <button @click="drawerOpen = false; refreshIssueDashboard();" class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-100 text-slate-700 hover:bg-slate-200">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <div class="mt-6">
                            <div class="flex items-center justify-between">
                                <nav class="flex items-center gap-2 bg-slate-50 px-2 py-1">
                                    <button @click="activeTab='details'" :class="['px-3 py-2 text-sm', activeTab==='details' ? 'text-blue-600 font-semibold border-b-2 border-blue-600' : 'text-slate-500']">Details</button>
                                    <button @click="activeTab='history'" :class="['px-3 py-2 text-sm', activeTab==='history' ? 'text-blue-600 font-semibold border-b-2 border-blue-600' : 'text-slate-500']">Status History</button>
                                    <button @click="activeTab='attachments'" :class="['px-3 py-2 text-sm', activeTab==='attachments' ? 'text-blue-600 font-semibold border-b-2 border-blue-600' : 'text-slate-500']">Attachments</button>
                                    <button type="button" onclick="openTicketPreviewPopup()" class="px-3 py-2 text-sm text-slate-500 hover:text-blue-600">Preview</button>
                                </nav>
                            </div>

                            <div class="mt-4 space-y-6">
                                <div x-show="activeTab==='details'" x-cloak class="space-y-4" x-data="{ showUpdate: false }">
                                    <div class="rounded-[14px] border border-slate-200 bg-slate-50 p-4">
                                        <div class="grid gap-3 sm:grid-cols-2">
                                            <div>
                                                <p class="text-[11px] uppercase tracking-[0.24em] text-slate-500">Ticket ID</p>
                                                <p class="mt-1 text-sm font-semibold text-slate-900" x-text="selectedTicket ? selectedTicket.id : '—'">IT-xxxx</p>
                                            </div>
                                            <div>
                                                <p class="text-[11px] uppercase tracking-[0.24em] text-slate-500">State</p>
                                                <p class="mt-1 text-sm font-semibold text-slate-900" x-text="selectedTicket ? selectedTicket.state : '—'">State</p>
                                            </div>
                                            <div>
                                                <p class="text-[11px] uppercase tracking-[0.24em] text-slate-500">Project</p>
                                                <p class="mt-1 text-sm font-semibold text-slate-900" x-text="selectedTicket ? selectedTicket.project : '—'">Project</p>
                                            </div>
                                            <div>
                                                <p class="text-[11px] uppercase tracking-[0.24em] text-slate-500">Module</p>
                                                <p class="mt-1 text-sm font-semibold text-slate-900" x-text="selectedTicket ? selectedTicket.module : '—'">Module</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="rounded-[14px] border border-slate-200 bg-slate-50 p-4">
                                        <p class="text-sm font-semibold text-slate-900">Issue Description</p>
                                        <p class="mt-2 text-sm leading-6 text-slate-700" x-text="selectedTicket ? selectedTicket.description : 'No description available.'">No description available.</p>
                                    </div>

                                    <div class="flex items-center justify-between">
                                        <p class="text-sm font-semibold text-slate-900">Update Ticket</p>
                                        <button @click="showUpdate = !showUpdate" class="text-sm text-blue-600"> <span x-text="showUpdate ? 'Hide' : 'Edit'"></span></button>
                                    </div>

                                    <div x-show="showUpdate" x-cloak class="rounded-[14px] border border-slate-200 bg-white p-4">
                                        <form id="issueUpdateForm" method="POST" action="{{ route('role.issue.update') }}" enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="issue_id" x-bind:value="selectedTicket ? selectedTicket.issue_id : ''" />
                                            <input type="hidden" name="status_id" x-bind:value="selectedTicket ? selectedTicket.status_id : ''" />
                                            <input type="hidden" name="vendor_id" x-bind:value="selectedTicket ? (selectedTicket.current_vendor_id || '') : ''" />

                                            <div id="drawerUpdateMessage" class="hidden mb-3 rounded-xl border px-4 py-3 text-sm font-semibold"></div>

                                            <div class="space-y-3">
                                                <div>
                                                    <label class="block text-sm font-semibold text-slate-700">Status</label>
                                                    <select name="status_name" x-model="selectedStatus" @change="handleStatusChange()" class="mt-2 w-full rounded-[12px] border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                        <option value="">Select Status</option>
                                                        @foreach($statusOptions as $status)
                                                            <option value="{{ $status['status_name'] }}" {{ ($status['status_name'] ?? '') === ($ticketStatus ?? '') ? 'selected' : '' }}>{{ $status['status_name'] }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div x-show="shouldShowVendorPicker()" x-cloak class="space-y-2">
                                                    <label class="block text-sm font-semibold text-slate-700">
                                                        Vendor Assignment
                                                    </label>

                                                    <template x-if="isVendorAssignmentStatus()">
                                                        <div id="vendor-multi-select" class="relative mt-2">
                                                            <div class="flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2.5 shadow-sm transition focus-within:border-blue-500 focus-within:ring-2 focus-within:ring-blue-100" data-multi-select>
                                                                <div class="flex flex-wrap gap-2" data-multi-select-chips></div>
                                                                <input type="text" class="min-w-[140px] flex-1 bg-transparent text-sm text-slate-900 placeholder:text-slate-400 outline-none" placeholder="Search vendors" data-multi-select-input autocomplete="off" />
                                                            </div>
                                                            <div class="absolute left-0 right-0 z-50 mt-1 hidden max-h-60 overflow-auto rounded-xl border border-slate-200 bg-white shadow-xl" data-multi-select-list></div>
                                                            <div data-multi-select-hidden class="hidden"></div>
                                                        </div>
                                                        <p class="mt-1 text-[11px] text-slate-500">Search and select one or more vendors.</p>
                                                    </template>
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-semibold text-slate-700">Remarks</label>
                                                    <textarea name="remarks" class="mt-2 w-full rounded-[12px] border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 shadow-sm" rows="3" placeholder="Enter remarks..."></textarea>
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-semibold text-slate-700">Upload Attachment</label>
                                                    <input type="file" name="attachments[]" multiple class="mt-2 w-full text-sm text-slate-700" />
                                                </div>

                                                <div class="flex justify-end pt-2">
                                                    <button type="submit" class="rounded-[10px] bg-blue-600 px-4 py-2 text-sm font-semibold text-white">Save</button>
                                                    <button type="button" @click="showUpdate = false" class="ml-2 rounded-[10px] border border-slate-200 px-4 py-2 text-sm">Cancel</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <div x-show="activeTab==='attachments'" x-cloak class="rounded-[18px] border border-slate-200 bg-slate-50 p-4">
                                    <p class="text-sm font-semibold text-slate-900">Attachments</p>
                                    <div class="mt-3 space-y-3">
                                        <template x-if="selectedTicket && selectedTicket.attachments && selectedTicket.attachments.length">
                                            <template x-for="(attachment, index) in selectedTicket.attachments" :key="index">
                                                <div class="flex items-center justify-between rounded-[16px] border border-slate-200 bg-white px-4 py-3">
                                                    <div class="flex items-center gap-3">
                                                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-100 text-slate-700 text-xs font-bold">FILE</span>
                                                        <div>
                                                            <p class="text-sm font-semibold text-slate-900" x-text="attachment.file_name">Attachment</p>
                                                            <p class="text-xs text-slate-500" x-text="attachment.created_at">—</p>
                                                        </div>
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <a :href="attachment.view_url || '#'" target="_blank" rel="noopener noreferrer" class="text-sm font-semibold text-blue-600 hover:text-blue-700 hover:underline" x-show="attachment.view_url" x-text="'View'">View</a>
                                                        <span class="text-slate-300">|</span>
                                                        <a :href="attachment.download_url || '#'" download class="text-sm font-semibold text-blue-600 hover:text-blue-700 hover:underline" x-show="attachment.download_url" x-text="'Download'">Download</a>
                                                    </div>
                                                </div>
                                            </template>
                                        </template>
                                        <template x-if="selectedTicket && (!selectedTicket.attachments || selectedTicket.attachments.length === 0)">
                                            <div class="rounded-[14px] border border-dashed border-slate-300 bg-white px-4 py-4 text-sm text-slate-500">No attachments found.</div>
                                        </template>
                                    </div>
                                </div>

                                <div x-show="activeTab==='preview'" x-cloak id="ticketPreviewSection" class="rounded-[18px] border border-slate-200 bg-white p-6">
                                    <div class="border-b border-slate-200 pb-4 mb-6">
                                        <div class="flex items-center justify-between mb-4">
                                            <h2 class="text-2xl font-bold text-slate-800">Ticket Details Preview</h2>
                                        </div>
                                        <div class="flex flex-wrap gap-2">
                                            <button type="button" onclick="printTicketPreviewSection()" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 text-white px-4 py-2 text-sm font-semibold hover:bg-blue-700">
                                                <span>🖨</span> Print
                                            </button>
                                            <button type="button" onclick="printTicketPreviewSection()" class="inline-flex items-center gap-2 rounded-lg bg-red-600 text-white px-4 py-2 text-sm font-semibold hover:bg-red-700">
                                                <span>📄</span> Export PDF
                                            </button>
                                        </div>
                                    </div>

                                    <div class="space-y-6">
                                        <div class="border-l-4 border-blue-600 bg-blue-50 px-4 py-3 rounded">
                                            <h3 class="text-sm font-semibold text-blue-700 uppercase tracking-wide">🎫 Ticket Information</h3>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            <div class="bg-white border border-slate-200 rounded-lg p-4">
                                                <p class="text-xs font-semibold text-slate-600 uppercase tracking-widest">Ticket ID</p>
                                                <p class="mt-2 text-sm font-bold text-slate-800" x-text="selectedTicket ? selectedTicket.id : '—'">—</p>
                                            </div>
                                            <div class="bg-white border border-slate-200 rounded-lg p-4">
                                                <p class="text-xs font-semibold text-slate-600 uppercase tracking-widest">State</p>
                                                <p class="mt-2 text-sm font-semibold text-slate-800" x-text="selectedTicket ? selectedTicket.state : '—'">—</p>
                                            </div>
                                            <div class="bg-white border border-slate-200 rounded-lg p-4">
                                                <p class="text-xs font-semibold text-slate-600 uppercase tracking-widest">Priority</p>
                                                <p class="mt-2 text-sm font-semibold text-slate-800" x-text="selectedTicket ? selectedTicket.priority : '—'">—</p>
                                            </div>
                                            <div class="bg-white border border-slate-200 rounded-lg p-4">
                                                <p class="text-xs font-semibold text-slate-600 uppercase tracking-widest">Project</p>
                                                <p class="mt-2 text-sm font-semibold text-slate-800" x-text="selectedTicket ? selectedTicket.project : '—'">—</p>
                                            </div>
                                            <div class="bg-white border border-slate-200 rounded-lg p-4">
                                                <p class="text-xs font-semibold text-slate-600 uppercase tracking-widest">Application</p>
                                                <p class="mt-2 text-sm font-semibold text-slate-800" x-text="selectedTicket ? selectedTicket.application : '—'">—</p>
                                            </div>
                                            <div class="bg-white border border-slate-200 rounded-lg p-4">
                                                <p class="text-xs font-semibold text-slate-600 uppercase tracking-widest">Module</p>
                                                <p class="mt-2 text-sm font-semibold text-slate-800" x-text="selectedTicket ? selectedTicket.module : '—'">—</p>
                                            </div>
                                        </div>

                                        <div class="bg-white border border-slate-200 rounded-lg p-4">
                                            <p class="text-xs font-semibold text-slate-600 uppercase tracking-widest">Status</p>
                                            <p class="mt-2 text-sm font-semibold text-slate-800" x-text="selectedTicket ? selectedTicket.status : '—'">—</p>
                                        </div>

                                        <div class="bg-white border border-slate-200 rounded-lg p-4">
                                            <p class="text-xs font-semibold text-slate-600 uppercase tracking-widest mb-3">Description</p>
                                            <p class="whitespace-pre-line text-sm text-slate-700 leading-relaxed" x-text="selectedTicket ? selectedTicket.description : 'No description available.'">No description available.</p>
                                        </div>

                                        <div class="bg-white border border-slate-200 rounded-lg p-4">
                                            <p class="text-xs font-semibold text-slate-600 uppercase tracking-widest mb-3">Attachments</p>
                                            <template x-if="selectedTicket && selectedTicket.attachments && selectedTicket.attachments.length">
                                                <div class="space-y-2">
                                                    <template x-for="(attachment, index) in selectedTicket.attachments" :key="index">
                                                        <div class="flex items-center justify-between bg-slate-50 p-3 rounded">
                                                            <p class="text-sm text-slate-700" x-text="attachment.file_name">—</p>
                                                            <div class="flex gap-2">
                                                                <a :href="attachment.view_url || '#'" target="_blank" class="text-xs text-blue-600 hover:underline" x-show="attachment.view_url">View</a>
                                                                <a :href="attachment.download_url || '#'" download class="text-xs text-blue-600 hover:underline" x-show="attachment.download_url">Download</a>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </template>
                                            <template x-if="selectedTicket && (!selectedTicket.attachments || selectedTicket.attachments.length === 0)">
                                                <p class="text-sm text-slate-500 italic">No attachments available</p>
                                            </template>
                                        </div>

                                        <!-- UPDATE HISTORY Section in Preview Tab -->
                                        <div class="border-l-4 border-blue-600 bg-blue-50 px-4 py-3 rounded">
                                            <h3 class="text-sm font-semibold text-blue-700 uppercase tracking-wide">⏱ Update History</h3>
                                        </div>

                                        <template x-if="selectedTicket && selectedTicket.history && selectedTicket.history.length">
                                            <div class="space-y-3">
                                                <template x-for="(row, index) in selectedTicket.history" :key="index">
                                                    <div class="rounded-lg border border-slate-200 bg-slate-50 p-4">
                                                        <div class="flex items-start justify-between gap-3 mb-3">
                                                            <div>
                                                                <p class="text-sm font-bold text-blue-600" x-text="row.action">Action</p>
                                                                <p class="text-xs text-slate-600 mt-1" x-text="'by ' + (row.changed_by || 'System') + ' · ' + (row.changed_at || 'Just now')">—</p>
                                                            </div>
                                                            <span class="rounded-full bg-slate-200 px-3 py-1 text-[10px] font-semibold text-slate-700" x-text="row.status_name || row.action">Status</span>
                                                        </div>
                                                        <template x-if="row.from_status && row.to_status">
                                                            <p class="text-sm text-slate-700 mb-2" x-html="'Status: <span class=&quot;font-semibold&quot;>' + row.from_status + '</span> → <span class=&quot;font-semibold&quot;>' + row.to_status + '</span>'">—</p>
                                                        </template>
                                                        <p class="text-sm font-semibold text-slate-900" x-text="'Remarks: ' + (row.comment || row.remarks || 'No remarks')">Remarks</p>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                        <template x-if="selectedTicket && (!selectedTicket.history || selectedTicket.history.length === 0)">
                                            <div class="rounded-[14px] border border-dashed border-slate-300 bg-white px-4 py-4 text-sm text-slate-500">No update history found.</div>
                                        </template>
                                    </div>
                                </div>

                                <div x-show="activeTab==='history'" x-cloak class="space-y-3">
                                    <template x-if="selectedTicket && selectedTicket.history && selectedTicket.history.length">
                                        <template x-for="(row, index) in selectedTicket.history" :key="index">
                                            <div class="rounded-[16px] border border-slate-200 bg-white p-4">
                                                <div class="flex items-start justify-between gap-3">
                                                    <div>
                                                        <p class="text-sm font-semibold text-slate-900" x-text="row.changed_at">—</p>
                                                        <p class="mt-1 text-sm text-slate-500" x-text="row.changed_by">Update user</p>
                                                    </div>
                                                    <span class="rounded-full bg-slate-100 px-3 py-1 text-[10px] font-semibold text-slate-600" x-text="row.status_name">Open</span>
                                                </div>
                                                <p class="mt-3 text-sm text-slate-700" x-text="row.comment">Status updated</p>
                                            </div>
                                        </template>
                                    </template>
                                    <template x-if="selectedTicket && (!selectedTicket.history || selectedTicket.history.length === 0)">
                                        <div class="rounded-[14px] border border-dashed border-slate-300 bg-white px-4 py-4 text-sm text-slate-500">No status history found.</div>
                                    </template>
                                </div>

                                <div class="sticky bottom-0 left-0 z-20 mt-4 rounded-[18px] border border-slate-200 bg-white p-4 shadow-xl">
                                    <div class="flex justify-end">
                                        <button @click="drawerOpen = false" class="rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </div>

    <div id="ticket-preview-popup" class="fixed inset-0 z-[70] hidden items-center justify-center bg-slate-900/60 p-4" role="dialog" aria-modal="true" aria-labelledby="ticket-preview-popup-title">
        <div class="flex max-h-[90vh] w-full max-w-4xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
            <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                <div>
                    <p class="text-[11px] uppercase tracking-[0.24em] text-slate-500">Ticket Preview</p>
                    <h2 id="ticket-preview-popup-title" class="mt-1 text-xl font-bold text-slate-900">Ticket</h2>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" onclick="printTicketPreviewPopup(false)" class="rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-700">Print</button>
                    <button type="button" onclick="printTicketPreviewPopup(true)" class="rounded-lg bg-emerald-600 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Export PDF</button>
                    <button type="button" onclick="closeTicketPreviewPopup()" class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Close</button>
                </div>
            </div>
            <div id="ticket-preview-popup-content" class="overflow-y-auto p-6"></div>
        </div>
    </div>

    <script>
        function openTicketPreviewPopup() {
            const ticket = window.__EMRI_CURRENT_TICKET;
            const popup = document.getElementById('ticket-preview-popup');
            const content = document.getElementById('ticket-preview-popup-content');
            const title = document.getElementById('ticket-preview-popup-title');

            if (!ticket || !popup || !content || !title) return;

            const escapeHtml = (value) => String(value ?? '—')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
            const vendors = ticket.vendor_progress?.vendors || [];
            const attachments = ticket.attachments || [];
            const history = ticket.history || [];
            const isImageAttachment = (attachment) => /\.(jpe?g|png|gif|webp|bmp)$/i.test(String(attachment.file_name || ''));

            title.textContent = ticket.id || 'Ticket Preview';
            content.innerHTML = `
                <div class="rounded-xl border border-blue-200 bg-gradient-to-br from-blue-50 via-white to-indigo-50 p-5">
                    <h3 class="text-lg font-bold text-blue-900">Ticket Information</h3>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                        ${[['Ticket ID', ticket.id], ['Title / Subject', ticket.title], ['Issue Category', ticket.category], ['State', ticket.state], ['Project', ticket.project], ['Application', ticket.application], ['Module', ticket.module], ['Overall Status', ticket.status], ['Priority', ticket.priority], ['Updated On', ticket.updated_on]].map(([label, value]) => `
                        <p class="break-words text-sm leading-6"><span class="font-semibold text-slate-500">${escapeHtml(label)}:</span> <span class="font-semibold text-slate-900">${escapeHtml(value)}</span></p>`).join('')}
                    </div>
                </div>
                <div class="mt-5 rounded-xl border border-violet-200 bg-violet-50 p-5">
                    <h3 class="text-lg font-bold text-violet-900">Description</h3>
                    <p class="mt-3 whitespace-pre-wrap text-sm leading-6 text-slate-700">${escapeHtml(ticket.description || 'No description available.')}</p>
                </div>
                <div class="mt-5 rounded-xl border border-amber-200 bg-amber-50 p-5">
                    <h3 class="text-lg font-bold text-amber-900">Occurrence Details</h3>
                    <div class="mt-3 grid gap-3 sm:grid-cols-3">
                        <p class="break-words text-sm leading-6"><span class="font-semibold text-amber-800">Occurred On:</span> <span class="font-semibold text-slate-900">${escapeHtml(ticket.occurred_date || 'Not recorded')}</span></p>
                        <p class="break-words text-sm leading-6"><span class="font-semibold text-amber-800">Time:</span> <span class="font-semibold text-slate-900">${escapeHtml(ticket.occurred_time || 'Not recorded')}</span></p>
                        <p class="break-words text-sm leading-6"><span class="font-semibold text-amber-800">Affected Users:</span> <span class="font-semibold text-slate-900">${escapeHtml(ticket.affected_users || 'Not recorded')}</span></p>
                    </div>
                </div>
                <div class="mt-5 rounded-xl border border-slate-200 bg-white p-5">
                    <h3 class="text-lg font-bold text-slate-900">Vendor Status</h3>
                    <div class="mt-3 space-y-2">${vendors.length ? vendors.map((vendor) => `
                        <div class="flex items-center justify-between gap-3 rounded-lg bg-slate-50 px-3 py-2 text-sm">
                            <span class="font-semibold text-slate-800">${escapeHtml(vendor.vendor_name)}</span>
                            <span class="rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700">${escapeHtml(vendor.status_name)}</span>
                        </div>`).join('') : '<p class="text-sm text-slate-500">No vendor assignments.</p>'}</div>
                </div>
                <div class="mt-5 rounded-xl border border-slate-200 bg-white p-5">
                    <h3 class="text-lg font-bold text-slate-900">Attachments</h3>
                    <div class="mt-3 space-y-4">${attachments.length ? attachments.map((attachment) => `
                        <div class="text-sm">
                            <div class="flex items-center justify-between gap-3">
                                <span class="font-semibold text-slate-700">${escapeHtml(attachment.file_name)}</span>
                                <a href="${escapeHtml(attachment.view_url || '#')}" target="_blank" rel="noopener noreferrer" class="font-semibold text-blue-600 hover:underline">View</a>
                            </div>
                            ${isImageAttachment(attachment) && attachment.inline_url ? `<img src="${escapeHtml(attachment.inline_url)}" alt="${escapeHtml(attachment.file_name)}" class="mt-3 max-h-72 max-w-full rounded-lg border border-slate-200 object-contain" />` : ''}
                        </div>`).join('') : '<p class="text-sm text-slate-500">No attachments.</p>'}</div>
                </div>
                <div class="mt-5 rounded-xl border border-slate-200 bg-white p-5">
                    <h3 class="text-lg font-bold text-slate-900">Status History</h3>
                    <div class="mt-3 space-y-3">${history.length ? history.map((row) => `<div class="border-l-2 border-blue-200 pl-3"><p class="text-sm font-semibold text-slate-800">${escapeHtml(row.status_name || row.action)}</p><p class="text-xs text-slate-500">${escapeHtml(row.changed_at)} · ${escapeHtml(row.changed_by)}</p><p class="mt-1 text-sm text-slate-600">${escapeHtml(row.comment || row.remarks || 'No remarks')}</p></div>`).join('') : '<p class="text-sm text-slate-500">No status history.</p>'}</div>
                </div>`;

            popup.classList.remove('hidden');
            popup.classList.add('flex');
        }

        function closeTicketPreviewPopup() {
            const popup = document.getElementById('ticket-preview-popup');
            if (!popup) return;
            popup.classList.add('hidden');
            popup.classList.remove('flex');
        }

        function printTicketPreviewPopup(exportPdf = false) {
            const ticket = window.__EMRI_CURRENT_TICKET;
            const content = document.getElementById('ticket-preview-popup-content');
            if (!ticket || !content) return;

            const printWindow = window.open('', '_blank', 'width=1000,height=800');
            if (!printWindow) {
                alert('Your browser blocked the print popup. Please allow popups.');
                return;
            }

            printWindow.document.write(`<!doctype html>
                <html><head><title>${String(ticket.id || 'Ticket Preview')}</title>
                <style>
                    body { font-family: Arial, sans-serif; color: #111827; margin: 32px; }
                    h1 { font-size: 24px; margin: 0 0 20px; }
                    h3 { font-size: 16px; margin: 0 0 12px; }
                    .section { border: 1px solid #d1d5db; padding: 16px; margin-bottom: 16px; }
                    .line { margin: 5px 0; line-height: 1.5; }
                    .label { color: #6b7280; font-weight: 700; }
                    .vendor { border-bottom: 1px solid #e5e7eb; padding: 7px 0; }
                    .history { border-left: 3px solid #93c5fd; padding: 5px 0 5px 10px; margin: 8px 0; }
                    a { color: #2563eb; }
                    @media print { body { margin: 16px; } }
                </style></head><body>
                <h1>${String(ticket.id || 'Ticket Preview')}</h1>
                ${content.innerHTML}
                </body></html>`);
            printWindow.document.close();
            printWindow.focus();
            setTimeout(() => printWindow.print(), 300);
        }

        document.addEventListener('click', function (event) {
            if (event.target.id === 'ticket-preview-popup') closeTicketPreviewPopup();
        });

        function printTicketPreviewSection() {
            const previewSection = document.getElementById('ticketPreviewSection');
            if (!previewSection) return;

            const printWindow = window.open('', '_blank', 'width=1000,height=800');
            if (!printWindow) {
                alert('Your browser blocked the print popup. Please allow popups and try again.');
                return;
            }

            const printHtml = `
                <html>
                    <head>
                        <title>Ticket Preview</title>
                        <style>
                            body { font-family: Arial, sans-serif; color: #111827; margin: 24px; }
                            .wrap { max-width: 900px; margin: 0 auto; }
                            .box { border: 1px solid #e5e7eb; border-radius: 12px; padding: 16px; margin-bottom: 16px; }
                            .label { font-size: 11px; text-transform: uppercase; letter-spacing: 0.12em; color: #6b7280; }
                            .value { margin-top: 6px; font-size: 16px; font-weight: 700; }
                            .row { display: grid; grid-template-columns: repeat(2, minmax(180px, 1fr)); gap: 12px; }
                            .pill { display: inline-block; background: #f3f4f6; border-radius: 999px; padding: 6px 10px; font-size: 12px; margin-right: 8px; }
                            .muted { color: #6b7280; }
                            @media print { body { margin: 0; } }
                        </style>
                    </head>
                    <body>
                        <div class="wrap">
                            ${previewSection.innerHTML}
                        </div>
                    </body>
                </html>
            `;

            printWindow.document.write(printHtml);
            printWindow.document.close();
            printWindow.focus();
            setTimeout(() => printWindow.print(), 400);
        }

        document.addEventListener('DOMContentLoaded', function () {
            const vendorOptions = @json(collect($vendorOptions ?? [])->map(function ($vendor) {
                return ['vendor_id' => $vendor->vendor_id ?? $vendor['vendor_id'], 'vendor_name' => $vendor->vendor_name ?? $vendor['vendor_name']];
            })->all());
            const vendorStateMappings = @json(collect($vendorStateMappings ?? [])->map(function ($mapping) {
                return ['state_id' => $mapping->state_id ?? $mapping['state_id'], 'project_id' => $mapping->project_id ?? $mapping['project_id'], 'vendor_id' => $mapping->vendor_id ?? $mapping['vendor_id']];
            })->all());
            
            // Define variables that will be used by refreshVendorOptions
            let currentStateId = null;
            let currentProjectId = null;
            let selectedVendorIds = [];

            window.__EMRI_VENDOR_DATA.vendorOptions = vendorOptions;
            window.__EMRI_VENDOR_DATA.vendorStateMappings = vendorStateMappings;
            console.log('vendor data loaded into window.__EMRI_VENDOR_DATA', {
                vendorOptions: vendorOptions.length,
                vendorStateMappings: vendorStateMappings.length,
                sampleOptions: vendorOptions.slice(0, 5),
                sampleMapping: vendorStateMappings.slice(0, 5)
            });
            console.log('vendor filter state on load', {
                currentStateId: window.__EMRI_VENDOR_DATA.currentStateId,
                currentProjectId: window.__EMRI_VENDOR_DATA.currentProjectId,
                availableIdsForCurrentTicket: window.__EMRI_getAvailableVendorIds ? window.__EMRI_getAvailableVendorIds(window.__EMRI_VENDOR_DATA.currentStateId, window.__EMRI_VENDOR_DATA.currentProjectId) : []
            });

            function initializeVendorMultiSelect() {
                const multiselectContainer = document.getElementById('vendor-multi-select');
                if (!multiselectContainer) {
                    console.log('vendor-multi-select container not found on page load, will initialize when template renders');
                    return;
                }

                const input = multiselectContainer.querySelector('[data-multi-select-input]');
                const list = multiselectContainer.querySelector('[data-multi-select-list]');
                const chipsContainer = multiselectContainer.querySelector('[data-multi-select-chips]');
                const hiddenContainer = multiselectContainer.querySelector('[data-multi-select-hidden]');

                if (!input || !list || !chipsContainer || !hiddenContainer) {
                    return;
                }

                if (input.dataset.bound === 'true') {
                    return;
                }

                input.dataset.bound = 'true';

                input.addEventListener('input', function () {
                    window.__EMRI_renderVendorList(this.value);
                    list.classList.remove('hidden');
                });

                input.addEventListener('focus', function () {
                    window.__EMRI_renderVendorList(this.value);
                    list.classList.remove('hidden');
                });

                input.addEventListener('click', function () {
                    window.__EMRI_renderVendorList(this.value);
                    list.classList.remove('hidden');
                });

                list.addEventListener('click', function (event) {
                    const button = event.target.closest('button[data-vendor-id]');
                    if (!button) {
                        return;
                    }

                    const vendorId = String(button.dataset.vendorId || '');
                    if (!vendorId) {
                        return;
                    }

                    const selectedVendorIds = window.__EMRI_VENDOR_DATA.selectedVendorIds || [];
                    if (!selectedVendorIds.includes(vendorId)) {
                        window.__EMRI_VENDOR_DATA.selectedVendorIds = [...selectedVendorIds, vendorId];
                        window.__EMRI_renderVendorChips();
                        window.__EMRI_renderVendorList('');
                    }

                    input.value = '';
                    list.classList.add('hidden');
                });

                input.addEventListener('keydown', function (event) {
                    if (event.key !== 'Enter') {
                        return;
                    }

                    event.preventDefault();
                    const firstVisible = Array.from(list.querySelectorAll('button[data-vendor-id]')).find((button) => button.style.display !== 'none');
                    if (!firstVisible) {
                        return;
                    }

                    const vendorId = String(firstVisible.dataset.vendorId || '');
                    if (!vendorId) {
                        return;
                    }

                    const selectedVendorIds = window.__EMRI_VENDOR_DATA.selectedVendorIds || [];
                    if (!selectedVendorIds.includes(vendorId)) {
                        window.__EMRI_VENDOR_DATA.selectedVendorIds = [...selectedVendorIds, vendorId];
                        window.__EMRI_renderVendorChips();
                        window.__EMRI_renderVendorList('');
                    }

                    input.value = '';
                    list.classList.add('hidden');
                });

                document.addEventListener('click', function (event) {
                    if (!multiselectContainer.contains(event.target)) {
                        list.classList.add('hidden');
                    }
                });
            }

            window.initializeVendorMultiSelect = initializeVendorMultiSelect;
            initializeVendorMultiSelect();
        });

        function exportIssueQueue(event) {
            if (event) {
                event.preventDefault();
            }

            const issues = @js($issues);
            if (!issues || issues.length === 0) {
                alert('No issues to export.');
                return;
            }

            const headers = ['Ticket ID', 'Issue Title', 'State', 'Project', 'Application', 'Module', 'Status', 'Priority', 'Updated On'];
            const rows = issues.map(ticket => [
                ticket.id,
                ticket.title,
                ticket.state,
                ticket.project,
                ticket.application,
                ticket.module,
                ticket.status,
                ticket.priority,
                ticket.updated_on
            ]);

            let csv = headers.join(',') + '\n';
            rows.forEach(row => {
                csv += row.map(cell => `"${String(cell).replace(/"/g, '""')}"`).join(',') + '\n';
            });

            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement('a');
            const url = URL.createObjectURL(blob);

            link.setAttribute('href', url);
            link.setAttribute('download', 'issue_queue_' + new Date().getTime() + '.csv');
            link.style.visibility = 'hidden';

            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        window.exportIssueQueue = exportIssueQueue;
    </script>
</x-app-layout>
