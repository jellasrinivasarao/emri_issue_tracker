<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">IT Support Desk</h2>
    </x-slot>

    <div x-data="{ selectedTicket: null, tab: 'details', previewPopup: false }" class="min-h-screen bg-slate-50 px-4 py-8 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-[1700px] space-y-4">
                <div class="grid gap-4 xl:grid-cols-[minmax(460px,1fr)_minmax(700px,1fr)]">
                    <div class="rounded-[18px] border border-slate-200 bg-white p-4 shadow-sm">
                        <p class="text-sm font-semibold text-slate-900">Support Ticket Summary</p>
                        <p class="mt-1 text-sm text-slate-500">Overview of tickets assigned to the IT Support Desk.</p>
                        <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
                            @foreach ([
                                ['label' => 'All Tickets', 'value' => $ticketCounts['total'], 'filter' => '', 'panel' => 'bg-blue-100 border-blue-200', 'labelText' => 'text-blue-800', 'valueText' => 'text-blue-900'],
                                ['label' => 'In Process', 'value' => $ticketCounts['in_progress'], 'filter' => 'in_process', 'panel' => 'bg-amber-100 border-amber-200', 'labelText' => 'text-amber-800', 'valueText' => 'text-amber-900'],
                                ['label' => 'Resolved', 'value' => $ticketCounts['resolved'], 'filter' => 'resolved', 'panel' => 'bg-emerald-100 border-emerald-200', 'labelText' => 'text-emerald-800', 'valueText' => 'text-emerald-900'],
                                ['label' => 'Closed', 'value' => $ticketCounts['closed'], 'filter' => 'closed', 'panel' => 'bg-violet-100 border-violet-200', 'labelText' => 'text-violet-800', 'valueText' => 'text-violet-900'],
                                ['label' => 'Reopened', 'value' => $ticketCounts['reopened'], 'filter' => 'reopened', 'panel' => 'bg-blue-500 border-blue-600', 'labelText' => 'text-white', 'valueText' => 'text-white'],
                            ] as $summary)
                                <form method="GET" action="{{ route('it.support.dashboard') }}">
                                    <input type="hidden" name="state_id" value="{{ $filterValues['state_id'] ?? '' }}" />
                                    <input type="hidden" name="priority_id" value="{{ $filterValues['priority_id'] ?? '' }}" />
                                    <input type="hidden" name="date_from" value="{{ $filterValues['date_from'] ?? '' }}" />
                                    <input type="hidden" name="date_to" value="{{ $filterValues['date_to'] ?? '' }}" />
                                    <input type="hidden" name="search" value="{{ $filterValues['search'] ?? '' }}" />
                                    <input type="hidden" name="status_id" value="{{ $summary['filter'] }}" />
                                    <button type="submit" class="flex h-[150px] w-full flex-col items-center justify-center rounded-[14px] border {{ $summary['panel'] }} p-3 text-center shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                                        <p class="text-[11px] font-bold uppercase tracking-[0.16em] {{ $summary['labelText'] }}">{{ $summary['label'] }}</p>
                                        <p class="mt-auto text-3xl font-bold {{ $summary['valueText'] }}">{{ $summary['value'] }}</p>
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    </div>

                    <form method="GET" action="{{ route('it.support.dashboard') }}" class="rounded-[18px] border border-slate-200 bg-white p-4 shadow-sm">
                        <p class="text-sm font-semibold text-slate-900">Filters</p>
                        <p class="mt-1 text-sm text-slate-500">Filter the support ticket queue.</p>
                        <div class="mt-4 grid grid-cols-1 gap-2 sm:grid-cols-2 xl:grid-cols-3">
                            <select name="state_id" class="rounded-[12px] border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700">
                                <option value="">All States</option>
                                @foreach($stateOptions as $state)
                                    <option value="{{ $state->state_id }}" {{ (string) ($filterValues['state_id'] ?? '') === (string) $state->state_id ? 'selected' : '' }}>{{ $state->state_name }}</option>
                                @endforeach
                            </select>
                            <select name="status_id" class="rounded-[12px] border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700">
                                <option value="">All Statuses</option>
                                @foreach($statusOptions as $status)
                                    <option value="{{ $status->status_id }}" {{ (string) ($filterValues['status_id'] ?? '') === (string) $status->status_id ? 'selected' : '' }}>{{ $status->status_name }}</option>
                                @endforeach
                            </select>
                            <select name="priority_id" class="rounded-[12px] border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700">
                                <option value="">All Priorities</option>
                                @foreach($priorityOptions as $priority)
                                    <option value="{{ $priority->priority_id }}" {{ (string) ($filterValues['priority_id'] ?? '') === (string) $priority->priority_id ? 'selected' : '' }}>{{ $priority->priority_name }}</option>
                                @endforeach
                            </select>
                            <input type="date" name="date_from" value="{{ $filterValues['date_from'] ?? '' }}" class="rounded-[12px] border border-slate-200 px-3 py-2.5 text-sm text-slate-700" />
                            <input type="date" name="date_to" value="{{ $filterValues['date_to'] ?? '' }}" class="rounded-[12px] border border-slate-200 px-3 py-2.5 text-sm text-slate-700" />
                            <input type="search" name="search" value="{{ $filterValues['search'] ?? '' }}" placeholder="Search ticket, GID, subject" class="rounded-[12px] border border-slate-200 px-3 py-2.5 text-sm text-slate-700" />
                        </div>
                        <div class="mt-4 flex gap-2">
                            <button type="submit" class="rounded-[12px] bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">Apply Filters</button>
                            <a href="{{ route('it.support.dashboard') }}" class="rounded-[12px] border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Reset</a>
                        </div>
                    </form>
                </div>

                <div class="overflow-hidden rounded-[18px] border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="mb-3 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-slate-900">Support Ticket Queue</p>
                            <p class="mt-1 text-sm text-slate-500">Select a ticket to view its details, history, and attachments.</p>
                        </div>
                        <span class="text-xs text-slate-500">Showing {{ $tickets->count() }} tickets</span>
                    </div>
                    <div class="max-h-[520px] overflow-auto rounded-[12px] border border-slate-200">
                        <table class="min-w-full divide-y divide-slate-200 text-left text-sm text-slate-700">
                            <thead class="sticky top-0 z-10 bg-gradient-to-r from-blue-600 via-indigo-600 to-sky-500 text-[10px] uppercase tracking-[0.2em] text-white">
                                <tr>
                                    <th class="px-3 py-3">Ticket</th><th class="px-3 py-3">Subject</th><th class="px-3 py-3">Requester</th><th class="px-3 py-3">State</th><th class="px-3 py-3">Category</th><th class="px-3 py-3">Priority</th><th class="px-3 py-3">Status</th><th class="px-3 py-3">Created</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @forelse($tickets as $ticket)
                                    <tr class="cursor-pointer hover:bg-slate-50" @click="selectedTicket = @js($ticket); tab = 'details'">
                                        <td class="px-3 py-3 font-semibold text-blue-600">{{ $ticket->ticket_number }}</td>
                                        <td class="max-w-xs truncate px-3 py-3">{{ $ticket->issue_subject }}</td>
                                        <td class="px-3 py-3">{{ $ticket->requester_gid }}</td>
                                        <td class="px-3 py-3">{{ $ticket->state_name ?: '-' }}</td>
                                        <td class="px-3 py-3">{{ $ticket->category_name ?: '-' }}</td>
                                        <td class="px-3 py-3 font-semibold">{{ $ticket->priority_name ?: $ticket->priority ?: '-' }}</td>
                                        <td class="px-3 py-3">{{ $ticket->status_name ?: '-' }}</td>
                                        <td class="whitespace-nowrap px-3 py-3">{{ $ticket->created_at }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="8" class="px-5 py-12 text-center text-slate-500">No support tickets found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div x-show="selectedTicket" x-cloak class="fixed inset-0 z-50 bg-slate-900/40" @click.self="selectedTicket = null">
                <aside class="absolute right-0 top-0 h-full w-full max-w-xl overflow-y-auto bg-white px-5 py-6 shadow-2xl">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-[11px] uppercase tracking-[0.32em] text-slate-500">Ticket Details</p>
                            <h3 class="mt-2 text-xl font-semibold text-slate-900" x-text="selectedTicket?.ticket_number"></h3>
                            <div class="mt-2 flex flex-wrap items-center gap-2 text-sm">
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-slate-700" x-text="selectedTicket?.status_name || '-'">-</span>
                                <span class="rounded-full bg-red-100 px-3 py-1 text-red-700" x-text="selectedTicket?.priority_name || selectedTicket?.priority || '-'">-</span>
                            </div>
                        </div>
                        <button type="button" aria-label="Close ticket details" class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-100 text-2xl font-light leading-none text-slate-600 hover:bg-slate-200" @click="selectedTicket = null">&times;</button>
                    </div>
                    <div class="mt-6 flex items-center gap-1 bg-slate-50 px-2">
                        <button type="button" class="border-b-2 px-3 py-3 text-sm" :class="tab === 'details' ? 'border-blue-600 font-semibold text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-700'" @click="tab = 'details'">Details</button>
                        <button type="button" class="border-b-2 px-3 py-3 text-sm" :class="tab === 'history' ? 'border-blue-600 font-semibold text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-700'" @click="tab = 'history'">Status History</button>
                        <button type="button" class="border-b-2 px-3 py-3 text-sm" :class="tab === 'attachments' ? 'border-blue-600 font-semibold text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-700'" @click="tab = 'attachments'">Attachments</button>
                        <button type="button" class="border-b-2 px-3 py-3 text-sm border-transparent text-slate-500 hover:text-blue-600" @click="previewPopup = true">Preview</button>
                    </div>
                    <div x-show="tab === 'details'" class="mt-5 space-y-4">
                        <div class="grid grid-cols-2 gap-3 rounded-2xl bg-slate-50 p-4 text-sm"><div><span class="text-slate-500">Requester</span><p class="font-semibold" x-text="selectedTicket?.requester_gid"></p></div><div><span class="text-slate-500">State</span><p class="font-semibold" x-text="selectedTicket?.state_name || '-' "></p></div><div><span class="text-slate-500">Category</span><p class="font-semibold" x-text="selectedTicket?.category_name || '-' "></p></div><div><span class="text-slate-500">Device Type</span><p class="font-semibold" x-text="selectedTicket?.device_name || '-' "></p></div><div><span class="text-slate-500">Issue Type</span><p class="font-semibold" x-text="selectedTicket?.issue_type_name || '-' "></p></div><div><span class="text-slate-500">Impact</span><p class="font-semibold" x-text="selectedTicket?.impact_name || '-' "></p></div><div><span class="text-slate-500">Status</span><p class="font-semibold" x-text="selectedTicket?.status_name || '-' "></p></div></div>
                        <div><h4 class="font-semibold text-slate-900" x-text="selectedTicket?.issue_subject"></h4><p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700" x-text="selectedTicket?.issue_description"></p></div>
                        <form method="POST" action="{{ route('it.support.dashboard.update') }}" class="rounded-2xl border border-slate-200 bg-white p-4">
                            @csrf
                            <input type="hidden" name="ticket_id" x-bind:value="selectedTicket?.ticket_id || ''" />
                            <label class="text-sm font-semibold text-slate-700">Update Status</label>
                            <select name="status_id" class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" x-bind:value="selectedTicket?.status_id || ''">
                                @foreach($statusOptions as $status)
                                    <option value="{{ $status->status_id }}">{{ $status->status_name }}</option>
                                @endforeach
                            </select>
                            <textarea name="remarks" rows="3" class="mt-3 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="Remarks"></textarea>
                            <button type="submit" class="mt-3 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Save Status</button>
                        </form>
                    </div>
                    <div x-show="tab === 'history'" class="mt-5 space-y-3"><template x-for="entry in (selectedTicket?.history || [])" :key="entry.action_at + entry.action_type"><div class="rounded-xl border border-slate-200 p-3 text-sm"><p class="font-semibold text-slate-900" x-text="entry.action_type"></p><p class="mt-1 text-slate-600" x-text="entry.remarks || '-' "></p><p class="mt-1 text-xs text-slate-500" x-text="(entry.action_by_gid || '-') + ' | ' + entry.action_at"></p></div></template><p x-show="!(selectedTicket?.history || []).length" class="text-sm text-slate-500">No history found.</p></div>
                    <div x-show="tab === 'attachments'" class="mt-5 space-y-3">
                        <template x-for="file in (selectedTicket?.attachments || [])" :key="file.file_path">
                            <div class="flex items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-white px-3 py-3 shadow-sm">
                                <div class="flex min-w-0 items-center gap-3">
                                    <span class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-100 text-[10px] font-bold text-slate-700">FILE</span>
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-semibold text-slate-900" x-text="file.original_file_name"></p>
                                        <p class="mt-1 text-xs text-slate-500" x-text="file.uploaded_at"></p>
                                    </div>
                                </div>
                                <div class="flex shrink-0 items-center gap-2 text-sm">
                                    <a :href="file.view_url || file.url || '#'" target="_blank" rel="noopener noreferrer" class="font-semibold text-blue-600 hover:underline">View</a>
                                    <span class="text-slate-300">|</span>
                                    <a :href="file.download_url || '#'" download class="font-semibold text-blue-600 hover:underline">Download</a>
                                </div>
                            </div>
                        </template>
                        <p x-show="!(selectedTicket?.attachments || []).length" class="text-sm text-slate-500">No attachments found.</p>
                    </div>
                    <div x-show="tab === 'preview'" id="support-ticket-preview" class="mt-5 space-y-5 rounded-2xl border border-slate-200 bg-white p-5">
                        <div class="flex items-center justify-between gap-3 border-b border-slate-200 pb-4">
                            <div><p class="text-[11px] uppercase tracking-[0.24em] text-slate-500">Ticket Preview</p><h4 class="mt-1 text-xl font-bold text-slate-900" x-text="selectedTicket?.ticket_number"></h4></div>
                            <button type="button" onclick="printSupportTicketPreview()" class="rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-700">Print</button>
                        </div>
                        <div class="border-l-4 border-blue-600 bg-blue-50 px-4 py-3"><h3 class="text-sm font-semibold uppercase tracking-wide text-blue-700">Ticket Information</h3></div>
                        <dl class="grid grid-cols-2 gap-4 rounded-xl bg-slate-50 p-4 text-sm sm:grid-cols-3">
                            <div><dt class="text-xs uppercase tracking-widest text-slate-500">Ticket ID</dt><dd class="mt-1 font-bold" x-text="selectedTicket?.ticket_number"></dd></div>
                            <div><dt class="text-xs uppercase tracking-widest text-slate-500">Requester</dt><dd class="mt-1 font-semibold" x-text="selectedTicket?.requester_gid"></dd></div>
                            <div><dt class="text-xs uppercase tracking-widest text-slate-500">State</dt><dd class="mt-1 font-semibold" x-text="selectedTicket?.state_name || '-' "></dd></div>
                            <div><dt class="text-xs uppercase tracking-widest text-slate-500">Category</dt><dd class="mt-1 font-semibold" x-text="selectedTicket?.category_name || '-' "></dd></div>
                            <div><dt class="text-xs uppercase tracking-widest text-slate-500">Priority</dt><dd class="mt-1 font-semibold" x-text="selectedTicket?.priority_name || selectedTicket?.priority || '-' "></dd></div>
                            <div><dt class="text-xs uppercase tracking-widest text-slate-500">Status</dt><dd class="mt-1 font-semibold" x-text="selectedTicket?.status_name || '-' "></dd></div>
                        </dl>
                        <div class="rounded-xl border border-violet-200 bg-violet-50 p-4"><h3 class="text-sm font-semibold uppercase tracking-wide text-violet-700">Description</h3><p class="mt-3 whitespace-pre-line text-sm leading-6 text-slate-700" x-text="selectedTicket?.issue_description || 'No description available.'"></p></div>
                        <div class="rounded-xl border border-slate-200 p-4"><h3 class="text-sm font-semibold uppercase tracking-wide text-slate-700">Attachments</h3><div class="mt-3 space-y-2"><template x-for="file in (selectedTicket?.attachments || [])" :key="'preview-' + file.file_path"><div class="flex items-center justify-between rounded-lg bg-slate-50 p-3 text-sm"><span class="font-semibold text-slate-700" x-text="file.original_file_name"></span><span class="flex gap-3"><a :href="file.view_url || file.url || '#'" target="_blank" class="font-semibold text-blue-600 hover:underline">View</a><a :href="file.download_url || '#'" download class="font-semibold text-blue-600 hover:underline">Download</a></span></div></template><p x-show="!(selectedTicket?.attachments || []).length" class="text-sm italic text-slate-500">No attachments available.</p></div></div>
                        <div class="border-l-4 border-blue-600 bg-blue-50 px-4 py-3"><h3 class="text-sm font-semibold uppercase tracking-wide text-blue-700">Update History</h3></div>
                        <div class="space-y-3"><template x-for="entry in (selectedTicket?.history || [])" :key="'preview-history-' + entry.action_at + entry.action_type"><div class="rounded-lg border border-slate-200 bg-slate-50 p-4"><div class="flex items-start justify-between gap-3"><div><p class="text-sm font-bold text-blue-600" x-text="entry.action_type"></p><p class="mt-1 text-xs text-slate-600" x-text="'by ' + (entry.action_by_gid || 'System') + ' | ' + entry.action_at"></p></div><span class="rounded-full bg-slate-200 px-3 py-1 text-[10px] font-semibold text-slate-700" x-text="entry.to_status_name || entry.action_type"></span></div><p class="mt-2 text-sm font-semibold text-slate-900" x-text="'Remarks: ' + (entry.remarks || 'No remarks')"></p></div></template><p x-show="!(selectedTicket?.history || []).length" class="rounded-xl border border-dashed border-slate-300 p-4 text-sm text-slate-500">No update history found.</p></div>
                    </div>
                </aside>
            </div>

            <div x-show="previewPopup && selectedTicket" x-cloak class="fixed inset-0 z-[70] flex items-center justify-center bg-slate-900/60 p-4" @click.self="previewPopup = false">
                <div class="flex max-h-[90vh] w-full max-w-4xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
                    <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4">
                        <div><p class="text-[11px] uppercase tracking-[0.24em] text-slate-500">Ticket Preview</p><h2 class="mt-1 text-xl font-bold text-slate-900" x-text="selectedTicket?.ticket_number"></h2></div>
                        <div class="flex items-center gap-2"><button type="button" onclick="printSupportTicketPreview(false)" class="rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-700">Print</button><button type="button" onclick="printSupportTicketPreview(true)" class="rounded-lg bg-emerald-600 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Export PDF</button><button type="button" class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50" @click="previewPopup = false">Close</button></div>
                    </div>
                    <div id="support-ticket-popup-preview" class="overflow-y-auto p-6">
                        <div class="border-l-4 border-blue-600 bg-blue-50 px-4 py-3"><h3 class="text-sm font-semibold uppercase tracking-wide text-blue-700">Ticket Information</h3></div>
                        <div class="mt-4 grid grid-cols-2 gap-4 rounded-xl bg-slate-50 p-4 text-sm sm:grid-cols-3"><div><span class="text-xs uppercase tracking-widest text-slate-500">Ticket ID</span><p class="mt-1 font-bold" x-text="selectedTicket?.ticket_number"></p></div><div><span class="text-xs uppercase tracking-widest text-slate-500">Requester</span><p class="mt-1 font-semibold" x-text="selectedTicket?.requester_gid"></p></div><div><span class="text-xs uppercase tracking-widest text-slate-500">State</span><p class="mt-1 font-semibold" x-text="selectedTicket?.state_name || '-' "></p></div><div><span class="text-xs uppercase tracking-widest text-slate-500">Category</span><p class="mt-1 font-semibold" x-text="selectedTicket?.category_name || '-' "></p></div><div><span class="text-xs uppercase tracking-widest text-slate-500">Device Type</span><p class="mt-1 font-semibold" x-text="selectedTicket?.device_name || '-' "></p></div><div><span class="text-xs uppercase tracking-widest text-slate-500">Issue Type</span><p class="mt-1 font-semibold" x-text="selectedTicket?.issue_type_name || '-' "></p></div><div><span class="text-xs uppercase tracking-widest text-slate-500">Impact</span><p class="mt-1 font-semibold" x-text="selectedTicket?.impact_name || '-' "></p></div><div><span class="text-xs uppercase tracking-widest text-slate-500">Priority</span><p class="mt-1 font-semibold" x-text="selectedTicket?.priority_name || selectedTicket?.priority || '-' "></p></div><div><span class="text-xs uppercase tracking-widest text-slate-500">Status</span><p class="mt-1 font-semibold" x-text="selectedTicket?.status_name || '-' "></p></div></div>
                        <div class="mt-5 rounded-xl border border-violet-200 bg-violet-50 p-5"><h3 class="text-sm font-semibold uppercase tracking-wide text-violet-700">Description</h3><p class="mt-3 whitespace-pre-line text-sm leading-6 text-slate-700" x-text="selectedTicket?.issue_description || 'No description available.'"></p></div>
                        <div class="mt-5 rounded-xl border border-slate-200 p-5"><h3 class="text-sm font-semibold uppercase tracking-wide text-slate-700">Attachments</h3><div class="mt-3 space-y-2"><template x-for="file in (selectedTicket?.attachments || [])" :key="'popup-' + file.file_path"><div class="flex items-center justify-between rounded-lg bg-slate-50 p-3 text-sm"><span class="font-semibold text-slate-700" x-text="file.original_file_name"></span><span class="flex gap-3"><a :href="file.view_url || file.url || '#'" target="_blank" class="font-semibold text-blue-600 hover:underline">View</a><a :href="file.download_url || '#'" download class="font-semibold text-blue-600 hover:underline">Download</a></span></div></template><p x-show="!(selectedTicket?.attachments || []).length" class="text-sm italic text-slate-500">No attachments available.</p></div></div>
                        <div class="mt-5 border-l-4 border-blue-600 bg-blue-50 px-4 py-3"><h3 class="text-sm font-semibold uppercase tracking-wide text-blue-700">Update History</h3></div>
                        <div class="mt-3 space-y-3"><template x-for="entry in (selectedTicket?.history || [])" :key="'popup-history-' + entry.action_at + entry.action_type"><div class="rounded-lg border border-slate-200 bg-slate-50 p-4"><p class="text-sm font-bold text-blue-600" x-text="entry.action_type"></p><p class="mt-1 text-xs text-slate-600" x-text="'by ' + (entry.action_by_gid || 'System') + ' | ' + entry.action_at"></p><p class="mt-2 text-sm font-semibold text-slate-900" x-text="'Remarks: ' + (entry.remarks || 'No remarks')"></p></div></template><p x-show="!(selectedTicket?.history || []).length" class="text-sm text-slate-500">No update history found.</p></div>
                    </div>
                </div>
            </div>
</x-app-layout>

<script>
    function printSupportTicketPreview(exportPdf = false) {
        const preview = document.getElementById('support-ticket-popup-preview') || document.getElementById('support-ticket-preview');
        if (!preview) return;
        const printWindow = window.open('', '_blank', 'width=900,height=700');
        printWindow.document.write('<html><head><title>Support Ticket Preview</title><style>body{font-family:Arial,sans-serif;padding:24px;color:#0f172a}a{color:#2563eb} .no-print{display:none!important}</style></head><body>' + preview.innerHTML + '</body></html>');
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
    }
</script>
