<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">IT Support Desk</h2>
    </x-slot>

    <div x-data="{ selectedTicket: null, tab: 'details', previewPopup: false, editing: false }" @ticket-chat-updated.window="if (selectedTicket?.ticket_id == $event.detail.ticketId) selectedTicket.chat = $event.detail.data" class="h-[calc(100vh-76px)] overflow-hidden bg-slate-50 px-4 py-8 sm:px-6 lg:px-8">
            <div class="mx-auto flex h-full max-w-[1700px] flex-col space-y-4 overflow-hidden">
                <div class="grid gap-4 xl:grid-cols-[minmax(460px,1fr)_minmax(700px,1fr)]">
                    <div class="rounded-[18px] border border-slate-200 bg-white p-4 shadow-sm">
                        <div class="flex items-center justify-between gap-3"><p class="text-sm font-semibold text-slate-900">Support Ticket Summary</p><label class="flex items-center gap-2 text-xs font-semibold text-slate-500">Auto refresh time <input data-auto-refresh-time type="text" value="5 seconds" readonly class="w-24 rounded-lg border border-slate-200 bg-slate-50 px-2 py-1 text-center text-xs text-slate-700" /><button type="button" data-auto-refresh-toggle class="rounded-lg bg-slate-900 px-2.5 py-1 text-xs font-semibold text-white">Pause</button></label></div>
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

                <div class="flex min-h-0 flex-1 overflow-hidden rounded-[18px] border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex min-h-0 flex-1 flex-col">
                        <div class="mb-3 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-semibold text-slate-900">Support Ticket Queue</p>
                                <p class="mt-1 text-sm text-slate-500">Select a ticket to view its details, history, and attachments.</p>
                            </div>
                            <span class="text-xs text-slate-500">Showing {{ $tickets->count() }} tickets</span>
                        </div>
                        <div class="min-h-0 flex-1 overflow-auto rounded-[12px] border border-slate-200">
                            <table class="min-w-full divide-y divide-slate-200 text-left text-sm text-slate-700">
                            <thead class="sticky top-0 z-10 bg-gradient-to-r from-blue-600 via-indigo-600 to-sky-500 text-[10px] uppercase tracking-[0.2em] text-white">
                                <tr>
                                    <th class="px-3 py-3">Ticket</th><th class="px-3 py-3">Subject</th><th class="px-3 py-3">Requester</th><th class="px-3 py-3">State</th><th class="px-3 py-3">Category</th><th class="px-3 py-3">Priority</th><th class="px-3 py-3">Status</th><th class="px-3 py-3">Chat</th><th class="px-3 py-3">Created</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @forelse($tickets as $ticket)
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-3 py-3 font-semibold text-blue-600">
                                            <a href="#" @click.prevent="selectedTicket = @js($ticket); tab = 'details'; editing = false" class="inline-block text-blue-600 hover:text-blue-800 underline decoration-blue-300 decoration-1 underline-offset-2">{{ $ticket->ticket_number }}</a>
                                        </td>
                                        <td class="max-w-xs truncate px-3 py-3">{{ $ticket->issue_subject }}</td>
                                        <td class="px-3 py-3">{{ $ticket->requester_gid }}</td>
                                        <td class="px-3 py-3">{{ $ticket->state_name ?: '-' }}</td>
                                        <td class="px-3 py-3">{{ $ticket->category_name ?: '-' }}</td>
                                        <td class="px-3 py-3 font-semibold">{{ $ticket->priority_name ?: $ticket->priority ?: '-' }}</td>
                                        <td class="px-3 py-3">{{ $ticket->status_name ?: '-' }}</td>
                                        <td class="px-3 py-3"><button type="button" title="Open ticket chat" data-chat-ticket-id="{{ $ticket->ticket_id }}" data-chat-ticket-number="{{ $ticket->ticket_number }}" data-chat-count="{{ $ticket->chat['unread_count'] ?? 0 }}" @click.stop="$nextTick(() => openSupportChat({{ $ticket->ticket_id }}))" class="relative inline-flex h-8 w-8 items-center justify-center rounded-full {{ ($ticket->chat['status'] ?? '') === 'active' ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}" aria-label="Chat"><svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 18.5 4 21v-5.5A8 8 0 0 1 12 7h1a8 8 0 0 1 8 8v.5a8 8 0 0 1-8 8H8.5L7 18.5Z"></path></svg><span data-chat-badge class="absolute -right-1 -top-1 {{ ($ticket->chat['unread_count'] ?? 0) > 0 ? '' : 'hidden' }} min-w-4 rounded-full bg-red-500 px-1 text-[9px] font-bold leading-4 text-white">{{ ($ticket->chat['unread_count'] ?? 0) > 99 ? '99+' : ($ticket->chat['unread_count'] ?? 0) }}</span></button><span class="ml-2 rounded-full px-2 py-1 text-[10px] font-semibold {{ ($ticket->chat['status'] ?? 'not_initiated') === 'active' ? 'bg-emerald-100 text-emerald-700' : (($ticket->chat['status'] ?? '') === 'ended' ? 'bg-slate-100 text-slate-600' : 'bg-amber-100 text-amber-700') }}">{{ ($ticket->chat['status'] ?? 'not_initiated') === 'active' ? 'Active' : (($ticket->chat['status'] ?? '') === 'ended' ? 'Ended' : 'Not Initiated') }}</span></td>
                                        <td class="whitespace-nowrap px-3 py-3">{{ $ticket->created_at }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="9" class="px-5 py-12 text-center text-slate-500">No support tickets found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div id="support-chat-panel" data-chat-panel class="hidden flex h-[500px] max-h-[75vh] w-[min(390px,calc(100vw-2rem))] flex-col overflow-hidden rounded-[18px] border border-slate-200 bg-white shadow-[0_18px_40px_rgba(15,23,42,0.14)]" style="position: fixed !important; right: 1rem !important; bottom: 1rem !important; left: auto !important; top: auto !important; z-index: 60 !important;"><div class="flex items-center justify-between gap-3 bg-gradient-to-r from-blue-600 via-blue-600 to-sky-500 px-4 py-3 text-white"><div class="flex items-center gap-3"><span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-white/15 ring-1 ring-white/30"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M7 18.5 4 21v-5.5A8 8 0 0 1 12 7h1a8 8 0 0 1 8 8v.5a8 8 0 0 1-8 8H8.5L7 18.5Z"></path></svg></span><p class="text-[15px] font-semibold">Ticket Chat</p></div><button type="button" data-chat-close class="text-xl font-light leading-none hover:text-slate-200">×</button></div><div class="bg-slate-100 px-4 py-3 text-center text-[12px] text-slate-600"><span data-chat-status>Chat initiated by IT Support Desk</span></div><button type="button" data-chat-initiate class="mt-3 hidden w-full bg-blue-600 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-700">Initiate Chat</button><div data-chat-messages class="min-h-0 flex-1 space-y-3 overflow-y-auto bg-white p-3 pr-2 [scrollbar-width:thin]"></div><div class="border-t border-slate-200 bg-white p-3"><form data-chat-form class="flex items-center gap-2 rounded-[14px] border border-sky-600 bg-white p-1.5"><input data-chat-input type="text" maxlength="2000" required placeholder="Type your message..." class="min-w-0 flex-1 rounded-[10px] border-0 bg-transparent px-3 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none" /><button type="submit" class="inline-flex h-11 w-11 items-center justify-center rounded-full bg-blue-600 text-white shadow-sm hover:bg-blue-700" aria-label="Send"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 11.5 20 4l-4.5 16-3.7-7.3L3 11.5Z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M20 4 9.8 14.2"></path></svg></button></form></div></div>
            <style>
                #support-chat-panel { height: 500px !important; max-height: 500px !important; }
                #support-chat-panel [data-chat-messages] { height: 300px !important; min-height: 0 !important; max-height: 300px !important; flex: 0 0 300px !important; overflow-y: auto !important; }
            </style>

            <div x-show="selectedTicket" x-cloak class="fixed inset-0 z-50 bg-slate-900/40" @click.self="selectedTicket = null">
                <aside data-ticket-drawer class="absolute right-0 top-0 h-full w-full max-w-xl overflow-y-auto bg-white px-5 py-6 shadow-2xl">
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
                        <div class="rounded-[14px] border border-slate-200 bg-slate-50 p-4"><p class="text-sm font-semibold text-slate-900">Issue Description</p><p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700" x-text="selectedTicket?.issue_description || 'No description available.'"></p></div>
                        <div class="flex items-center justify-between gap-3">
                            <h4 class="font-semibold text-slate-900">Update Ticket</h4>
                            <button type="button" class="font-semibold text-blue-600 hover:text-blue-700" @click="editing = !editing" x-text="editing ? 'Cancel' : 'Edit'"></button>
                        </div>
                        <form x-show="editing" x-cloak method="POST" action="{{ route('it.support.dashboard.update') }}" class="rounded-2xl border border-slate-200 bg-white p-4">
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
                    <div class="sticky bottom-0 left-0 z-20 mt-4 rounded-[18px] border border-slate-200 bg-white p-4 shadow-xl"><div class="flex justify-end"><button type="button" @click="selectedTicket = null" class="rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Close</button></div></div>
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

<script>
    (function () {
        let chatTimer = null;
        let activeChatTicket = null;
        let markNextChatRead = false;
        const panel = document.getElementById('support-chat-panel');
        const panelTitle = panel?.querySelector('p');
        const messagesBox = panel?.querySelector('[data-chat-messages]');
        const form = panel?.querySelector('[data-chat-form]');
        const input = panel?.querySelector('[data-chat-input]');
        const statusLabel = panel?.querySelector('[data-chat-status]');
        const initiateButton = panel?.querySelector('[data-chat-initiate]');
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
        const messagesUrl = @json(route('it.support.chat.messages', ['ticket' => '__TICKET__']));
        const sendUrl = @json(route('it.support.chat.send', ['ticket' => '__TICKET__']));
        const initiateUrl = @json(route('it.support.chat.initiate', ['ticket' => '__TICKET__']));

        const urlFor = (template, ticketId) => template.replace('__TICKET__', encodeURIComponent(ticketId));
        const escapeText = (value) => String(value ?? '').replace(/[&<>'"]/g, (character) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;' }[character]));
        const chatTrigger = (ticketId) => document.querySelector(`[data-chat-ticket-id="${ticketId}"]`);
        const updateChatBadge = (ticketId, count) => {
            const trigger = chatTrigger(ticketId);
            const badge = trigger?.querySelector('[data-chat-badge]');
            if (!badge) return;
            trigger.classList.add('h-10', 'w-10');
            badge.classList.add('z-20', 'inline-flex', 'h-6', 'min-w-6', 'items-center', 'justify-center', '-right-2', '-top-2', 'rounded-full', 'bg-red-600', 'text-xs', 'text-white', 'ring-2', 'ring-white');
            badge.style.right = '-8px';
            badge.style.top = '-10px';
            badge.textContent = count > 99 ? '99+' : String(count);
            badge.style.display = count > 0 ? 'inline-flex' : 'none';
            badge.classList.toggle('hidden', count < 1);
        };
        const refreshTicketBadge = async (trigger) => {
            if (String(trigger.dataset.chatTicketId) === String(activeChatTicket)) return;
            try {
                const response = await fetch(urlFor(messagesUrl, trigger.dataset.chatTicketId), { headers: { Accept: 'application/json' } });
                if (response.ok) updateChatBadge(trigger.dataset.chatTicketId, (await response.json()).unread_count || 0);
            } catch (error) {
                return;
            }
        };
        document.querySelectorAll('[data-chat-ticket-id]').forEach(refreshTicketBadge);
        setInterval(() => document.querySelectorAll('[data-chat-ticket-id]').forEach(refreshTicketBadge), 1000);

        function updateChatStatus(status) {
            const active = status === 'active';
            const ended = status === 'ended';
            if (statusLabel) statusLabel.textContent = active ? 'Chat Status: Active until ticket closure' : (ended ? 'Chat Status: Ended' : 'Chat Status: Not Initiated');
            if (initiateButton) initiateButton.classList.toggle('hidden', active || ended);
            if (input) input.disabled = !active;
        }

        function renderMessages(messages, stickToBottom = false) {
            if (!messagesBox) return;
            const previousScrollTop = messagesBox.scrollTop;
            const wasAtBottom = messagesBox.scrollHeight - messagesBox.scrollTop - messagesBox.clientHeight < 24;
            messagesBox.innerHTML = messages.length
                ? messages.map((message) => {
                    const isSupport = message.sender_role === 'IT Support Desk';
                    const avatar = `<span class="mt-1 inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-600"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm0 2c-4.42 0-8 2.24-8 5v1h16v-1c0-2.76-3.58-5-8-5Z"/></svg></span>`;
                    const content = `<div class="min-w-0 max-w-[78%]"><p class="mb-1 text-[10px] font-semibold ${isSupport ? 'text-left' : 'text-right'} text-slate-500">${escapeText(message.sender_name)}</p><div class="rounded-[14px] border ${isSupport ? 'border-slate-200 bg-slate-50' : 'border-blue-100 bg-blue-50'} px-3 py-2"><p class="overflow-hidden text-ellipsis whitespace-nowrap text-[13px] leading-5 text-slate-700" title="${escapeText(message.message)}">${escapeText(message.message)}</p></div><p class="mt-1 text-[9px] leading-3 text-slate-400 ${isSupport ? 'text-left' : 'text-right'}">${escapeText(message.sent_at)}</p></div>`;
                    return `<div class="flex ${isSupport ? 'justify-start' : 'justify-end'} items-start gap-2">${isSupport ? avatar + content : content + avatar}</div>`;
                }).join('')
                : '<p class="text-sm text-slate-500">No messages yet.</p>';
            messagesBox.scrollTop = stickToBottom || wasAtBottom ? messagesBox.scrollHeight : previousScrollTop;
        }

        async function pollChat() {
            if (!activeChatTicket) return;
            const messageUrl = urlFor(messagesUrl, activeChatTicket) + (markNextChatRead ? '?mark_read=1' : '');
            const response = await fetch(messageUrl, { headers: { Accept: 'application/json' } });
            if (!response.ok) return;
            const data = await response.json();
            markNextChatRead = false;
            updateChatStatus(data.status);
            updateChatBadge(activeChatTicket, data.unread_count || 0);
            renderMessages(data.messages || []);
            if (data.status !== 'active') {
                clearInterval(chatTimer);
                chatTimer = null;
            }
        }

        window.openSupportChat = async function (ticketId) {
            if (!ticketId || !panel) return;
            activeChatTicket = ticketId;
            markNextChatRead = true;
            const trigger = chatTrigger(ticketId);
            if (panelTitle) panelTitle.textContent = `Ticket Chat - ${trigger?.dataset.chatTicketNumber || ticketId}`;
            panel.classList.remove('hidden');
            await pollChat();
            clearInterval(chatTimer);
            chatTimer = setInterval(() => { markNextChatRead = true; pollChat(); }, 1000);
        };

        window.initiateSupportChat = async function (ticketId) {
            if (!ticketId) return;
            const response = await fetch(urlFor(initiateUrl, ticketId), { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, Accept: 'application/json' } });
            if (!response.ok) return;
            const data = await response.json();
            window.dispatchEvent(new CustomEvent('ticket-chat-updated', { detail: { ticketId, data } }));
            window.openSupportChat(ticketId);
        };

        initiateButton?.addEventListener('click', function () {
            if (activeChatTicket) window.initiateSupportChat(activeChatTicket);
        });

        form?.addEventListener('submit', async function (event) {
            event.preventDefault();
            if (!activeChatTicket || !input.value.trim()) return;
            const message = input.value.trim();
            input.value = '';
            const response = await fetch(urlFor(sendUrl, activeChatTicket), { method: 'POST', headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json', Accept: 'application/json' }, body: JSON.stringify({ message }) });
            if (response.ok) renderMessages((await response.json()).messages || [], true);
        });

        panel?.querySelector('[data-chat-close]')?.addEventListener('click', function () {
            panel.classList.add('hidden');
            activeChatTicket = null;
            clearInterval(chatTimer);
            chatTimer = null;
        });
    }());
</script>

<script>
    (function () {
        const panel = document.getElementById('support-chat-panel');

        function isVisible(element) {
            return Boolean(element && element.getClientRects().length && getComputedStyle(element).display !== 'none');
        }

        window.positionSupportChatPanel = function () {
            if (!panel) return;
            const drawer = document.querySelector('[data-ticket-drawer]');
            const drawerOverlay = drawer?.closest('[x-show]');

            if (isVisible(drawer) && isVisible(drawerOverlay)) {
                const drawerLeft = drawer.getBoundingClientRect().left;
                const panelWidth = panel.getBoundingClientRect().width || 390;
                const gap = 12;
                const rightOffset = Math.max(16, window.innerWidth - drawerLeft + gap);
                const maxRightOffset = Math.max(16, window.innerWidth - panelWidth - 16);
                const nextRight = `${Math.min(rightOffset, maxRightOffset)}px`;
                if (panel.style.right !== nextRight) panel.style.right = nextRight;
            } else {
                if (panel.style.right !== '1rem') panel.style.right = '1rem';
            }
        };

        window.addEventListener('resize', window.positionSupportChatPanel);
        new MutationObserver(window.positionSupportChatPanel).observe(document.body, { attributes: true, subtree: true, attributeFilter: ['style', 'class'] });
        window.positionSupportChatPanel();
    }());
</script>

<script>
    (function () {
        const idleDelay = 5000;
        let lastActivity = Date.now();
        let pageReady = document.readyState === 'complete';
        let manuallyPaused = false;
        const refreshFields = document.querySelectorAll('[data-auto-refresh-time]');
        const refreshToggles = document.querySelectorAll('[data-auto-refresh-toggle]');

        refreshToggles.forEach(function (toggle) {
            toggle.addEventListener('click', function () {
                manuallyPaused = !manuallyPaused;
                toggle.textContent = manuallyPaused ? 'Start' : 'Pause';
                lastActivity = Date.now();
            });
        });

        window.addEventListener('load', function () {
            pageReady = true;
            lastActivity = Date.now();
        }, { once: true });

        const markActivity = function () {
            lastActivity = Date.now();
        };

        ['pointerdown', 'keydown', 'input', 'change', 'focusin', 'scroll', 'wheel', 'submit'].forEach(function (eventName) {
            document.addEventListener(eventName, markActivity, { passive: eventName !== 'submit' });
        });

        const isVisible = function (element) {
            return Boolean(element && element.getClientRects().length && getComputedStyle(element).display !== 'none' && getComputedStyle(element).visibility !== 'hidden');
        };

        window.setInterval(function () {
            const drawerOpen = Array.from(document.querySelectorAll('[data-ticket-drawer]')).some(isVisible);
            const previewOpen = isVisible(document.querySelector('#support-ticket-popup-preview')?.closest('.fixed'));
            const chatOpen = isVisible(document.querySelector('[data-chat-panel]'));
            const paused = !pageReady || manuallyPaused || document.hidden || drawerOpen || previewOpen || chatOpen;
            refreshFields.forEach(function (field) {
                field.value = !pageReady ? 'Loading...' : paused ? 'Paused' : Math.max(0, Math.ceil((idleDelay - (Date.now() - lastActivity)) / 1000)) + ' seconds';
            });

            if (paused || Date.now() - lastActivity < idleDelay) {
                return;
            }

            window.location.reload();
        }, 1000);
    }());
</script>
