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
                            $allCardUrl = route('role.issue.dashboard');
                            $inProcessStatusId = collect($statusOptions)->first(function ($row) {
                                $name = strtolower((string) ($row['status_name'] ?? $row->status_name ?? ''));
                                return str_contains($name, 'process') || str_contains($name, 'progress');
                            })['status_id'] ?? null;
                            $closedStatusId = collect($statusOptions)->first(function ($row) {
                                $name = strtolower((string) ($row['status_name'] ?? $row->status_name ?? ''));
                                return str_contains($name, 'close') || str_contains($name, 'resolved') || str_contains($name, 'complete');
                            })['status_id'] ?? null;
                            $onHoldStatusId = collect($statusOptions)->first(function ($row) {
                                $name = strtolower((string) ($row['status_name'] ?? $row->status_name ?? ''));
                                return str_contains($name, 'hold') || str_contains($name, 'pending');
                            })['status_id'] ?? null;

                            $statusCards = [
                                ['label' => 'All Issues', 'value' => (string) ($statusSummary['all'] ?? 0), 'color' => 'blue', 'icon' => 'M3 7h18M3 12h18M3 17h18', 'bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'cardHref' => $allCardUrl, 'filterStatus' => ''],
                                ['label' => 'In-Process', 'value' => (string) ($statusSummary['in_process'] ?? 0), 'color' => 'emerald', 'icon' => 'M5 13l4 4L19 7', 'bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'cardHref' => route('role.issue.dashboard', ['status_id' => $inProcessStatusId]), 'filterStatus' => (string) ($inProcessStatusId ?? '')],
                                ['label' => 'Task Closed', 'value' => (string) ($statusSummary['closed'] ?? 0), 'color' => 'amber', 'icon' => 'M4 4h16v16H4z', 'bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'cardHref' => route('role.issue.dashboard', ['status_id' => $closedStatusId]), 'filterStatus' => (string) ($closedStatusId ?? '')],
                                ['label' => 'On-Hold', 'value' => (string) ($statusSummary['on_hold'] ?? 0), 'color' => 'violet', 'icon' => 'M12 8v8m4-4H8', 'bg' => 'bg-violet-50', 'text' => 'text-violet-700', 'cardHref' => route('role.issue.dashboard', ['status_id' => $onHoldStatusId]), 'filterStatus' => (string) ($onHoldStatusId ?? '')],
                            ];
                        @endphp
                        @foreach($statusCards as $card)
                            @php
                                $active = ((string) ($filterValues['status_id'] ?? '') === (string) $card['filterStatus']);
                            @endphp
                            <a href="{{ $card['cardHref'] }}" class="block rounded-[14px] border border-slate-200 bg-white p-3 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md {{ $active ? 'ring-2 ring-blue-500 ring-offset-1' : '' }}">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl {{ $card['bg'] }} {{ $card['text'] }}">
                                        <svg class="h-4.5 w-4.5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $card['icon'] }}"></path></svg>
                                    </div>
                                    <span class="rounded-full bg-slate-100 px-2 py-1 text-[9px] font-semibold uppercase tracking-[0.2em] text-slate-500">{{ $active ? 'Open' : 'View' }}</span>
                                </div>
                                <p class="mt-3 text-[10px] uppercase tracking-[0.2em] text-slate-500">{{ $card['label'] }}</p>
                                <p class="mt-1 text-2xl font-semibold text-slate-900">{{ $card['value'] }}</p>
                            </a>
                        @endforeach
                    </div>
                </div>

                <form id="roleIssueFilterForm" method="GET" action="{{ route('role.issue.dashboard') }}" class="rounded-[18px] border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="mb-4">
                        <p class="text-sm font-semibold text-slate-900">Filters</p>
                        <p class="mt-1 text-sm text-slate-500">Quick filter your ticket queue.</p>
                    </div>
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
                    <a href="#" class="text-sm font-semibold text-blue-600 hover:text-blue-700">Export</a>
                </div>

                <div class="mt-3 max-h-[230px] overflow-y-auto rounded-[12px] border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-left text-sm text-slate-700">
                        <thead class="sticky top-0 z-10 bg-slate-50 text-[10px] uppercase tracking-[0.2em] text-slate-500">
                            <tr>
                                <th class="px-3 py-3">Ticket ID</th>
                                <th class="px-3 py-3">Issue Title</th>
                                <th class="px-3 py-3">State</th>
                                <th class="px-3 py-3">Project</th>
                                <th class="px-3 py-3">Application</th>
                                <th class="px-3 py-3">Module</th>
                                <th class="px-3 py-3">Status</th>
                                <th class="px-3 py-3">Priority</th>
                                <th class="px-3 py-3">Updated On</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach($issues as $ticket)
                                <tr class="cursor-pointer hover:bg-slate-50" @click="drawerOpen = true; selectedTicket = @js($ticket); selectedStatus = '{{ $ticket['status'] }}'; activeTab = 'details';">
                                    <td class="px-3 py-2.5 font-semibold text-slate-900">
                                        <a href="#" @click.prevent="drawerOpen = true; selectedTicket = @js($ticket); selectedStatus = '{{ $ticket['status'] }}'; activeTab = 'details'" class="inline-block text-blue-600 hover:text-blue-800 underline decoration-blue-300 decoration-1 underline-offset-2">{{ $ticket['id'] }}</a>
                                    </td>
                                    <td class="px-3 py-2.5">{{ $ticket['title'] }}</td>
                                    <td class="px-3 py-2.5">{{ $ticket['state'] }}</td>
                                    <td class="px-3 py-2.5">{{ $ticket['project'] }}</td>
                                    <td class="px-3 py-2.5">{{ $ticket['application'] }}</td>
                                    <td class="px-3 py-2.5">{{ $ticket['module'] }}</td>
                                    <td class="px-3 py-2.5">
                                        <span class="inline-flex rounded-full bg-emerald-100 px-2 py-1 text-[10px] font-semibold text-emerald-700">{{ $ticket['status'] }}</span>
                                    </td>
                                    <td class="px-3 py-2.5">
                                        <span class="inline-flex rounded-full bg-red-100 px-2 py-1 text-[10px] font-semibold text-red-700">{{ $ticket['priority'] }}</span>
                                    </td>
                                    <td class="px-3 py-2.5">{{ $ticket['updated_on'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3 flex items-center justify-between text-[12px] text-slate-500">
                    <span>Showing {{ min(count($issues), 10) }} of {{ count($issues) }} issues</span>
                </div>
            </div>
        </div>

        <script>
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
                    <div x-show="drawerOpen" x-cloak class="fixed left-0 right-0 z-30 bg-slate-900/40 transition-opacity duration-200" style="top:var(--header-height,64px);height:calc(100% - var(--header-height,64px));"></div>
                    <aside x-show="drawerOpen" x-cloak class="fixed right-0 z-40 w-full max-w-[520px] overflow-y-auto border-l border-slate-200 bg-white px-6 py-6 shadow-2xl transition duration-300 md:w-[520px]" style="top:var(--header-height,64px);height:calc(100% - var(--header-height,64px));">
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

                                            <div id="drawerUpdateMessage" class="hidden mb-3 rounded-xl border px-4 py-3 text-sm font-semibold"></div>

                                            <div class="space-y-3">
                                                <div>
                                                    <label class="block text-sm font-semibold text-slate-700">Status</label>
                                                    <select name="status_name" x-model="selectedStatus" class="mt-2 w-full rounded-[12px] border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                        @foreach($statusOptions as $status)
                                                            <option value="{{ $status['status_name'] }}" {{ ($status['status_name'] ?? '') === ($ticketStatus ?? '') ? 'selected' : '' }}>{{ $status['status_name'] }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div x-show="selectedStatus && (selectedStatus.toLowerCase().includes('vendor'))" x-cloak>
                                                    <label class="block text-sm font-semibold text-slate-700">Vendor Assignment</label>
                                                    <select name="vendor_ids[]" multiple size="6" class="mt-2 w-full rounded-[12px] border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                                        @foreach($vendorOptions as $vendor)
                                                            <option value="{{ $vendor->vendor_id }}">{{ $vendor->vendor_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <p class="mt-1 text-[11px] text-slate-500">Hold Ctrl / Cmd to select multiple vendors.</p>
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
                                                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-100 text-slate-700">FILE</span>
                                                        <div>
                                                            <p class="text-sm font-semibold text-slate-900" x-text="attachment.file_name">Attachment</p>
                                                            <p class="text-xs text-slate-500" x-text="attachment.created_at">—</p>
                                                        </div>
                                                    </div>
                                                    <a :href="attachment.path || '#'
                                                    " class="text-sm font-semibold text-blue-600 hover:text-blue-700" x-show="attachment.path" x-text="'Download'">Download</a>
                                                </div>
                                            </template>
                                        </template>
                                        <template x-if="selectedTicket && (!selectedTicket.attachments || selectedTicket.attachments.length === 0)">
                                            <div class="rounded-[14px] border border-dashed border-slate-300 bg-white px-4 py-4 text-sm text-slate-500">No attachments found.</div>
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

</x-app-layout>
