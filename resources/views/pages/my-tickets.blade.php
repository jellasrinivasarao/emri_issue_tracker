<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 leading-tight">My Tickets</h2></x-slot>

    <div x-data="{ selectedTicket: null, tab: 'details', previewPopup: false, editing: false }" @ticket-chat-updated.window="if (selectedTicket?.ticket_id == $event.detail.ticketId) selectedTicket.chat = $event.detail.data" class="min-h-screen bg-slate-50 px-4 py-8 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-[1700px] space-y-4">
            <div class="grid gap-4 xl:grid-cols-[minmax(460px,1fr)_minmax(700px,1fr)]">
                <div class="rounded-[18px] border border-slate-200 bg-white p-4 shadow-sm">
                    <div class="flex items-center justify-between gap-3"><p class="text-sm font-semibold text-slate-900">My Ticket Summary</p><label class="flex items-center gap-2 text-xs font-semibold text-slate-500">Auto refresh time <input data-auto-refresh-time type="text" value="5 seconds" readonly class="w-24 rounded-lg border border-slate-200 bg-slate-50 px-2 py-1 text-center text-xs text-slate-700" /><button type="button" data-auto-refresh-toggle class="rounded-lg bg-slate-900 px-2.5 py-1 text-xs font-semibold text-white">Pause</button></label></div>
                    <p class="mt-1 text-sm text-slate-500">Overview of tickets raised by your End User account.</p>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2 xl:grid-cols-5">
                @foreach([
                    ['label' => 'All Tickets', 'value' => $statusCounts['total'], 'filter' => '', 'panel' => 'bg-blue-100 border-blue-200', 'labelText' => 'text-blue-800', 'valueText' => 'text-blue-900'],
                    ['label' => 'In Process', 'value' => $statusCounts['in_progress'], 'filter' => 'in_process', 'panel' => 'bg-amber-100 border-amber-200', 'labelText' => 'text-amber-800', 'valueText' => 'text-amber-900'],
                    ['label' => 'Resolved', 'value' => $statusCounts['resolved'], 'filter' => 'resolved', 'panel' => 'bg-emerald-100 border-emerald-200', 'labelText' => 'text-emerald-800', 'valueText' => 'text-emerald-900'],
                    ['label' => 'Closed', 'value' => $statusCounts['closed'], 'filter' => 'closed', 'panel' => 'bg-violet-100 border-violet-200', 'labelText' => 'text-violet-800', 'valueText' => 'text-violet-900'],
                    ['label' => 'Reopened', 'value' => $statusCounts['reopened'], 'filter' => 'reopened', 'panel' => 'bg-blue-500 border-blue-600', 'labelText' => 'text-white', 'valueText' => 'text-white'],
                ] as $card)
                    <form method="GET" action="{{ route('my.tickets') }}">
                        <input type="hidden" name="state_id" value="{{ $filterValues['state_id'] ?? '' }}" />
                        <input type="hidden" name="priority_id" value="{{ $filterValues['priority_id'] ?? '' }}" />
                        <input type="hidden" name="date_from" value="{{ $filterValues['date_from'] ?? '' }}" />
                        <input type="hidden" name="date_to" value="{{ $filterValues['date_to'] ?? '' }}" />
                        <input type="hidden" name="search" value="{{ $filterValues['search'] ?? '' }}" />
                        <input type="hidden" name="status_id" value="{{ $card['filter'] }}" />
                        <button type="submit" class="flex h-[150px] w-full flex-col items-center justify-center rounded-[14px] border {{ $card['panel'] }} p-3 text-center shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
                            <p class="text-[11px] font-bold uppercase tracking-[0.16em] {{ $card['labelText'] }}">{{ $card['label'] }}</p>
                            <p class="mt-auto text-3xl font-bold {{ $card['valueText'] }}">{{ $card['value'] }}</p>
                        </button>
                    </form>
                @endforeach
                    </div>
                </div>

                <form method="GET" action="{{ route('my.tickets') }}" class="rounded-[18px] border border-slate-200 bg-white p-4 shadow-sm">
                    <p class="text-sm font-semibold text-slate-900">Filters</p>
                    <p class="mt-1 text-sm text-slate-500">Filter your internal IT ticket history.</p>
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
                        <input type="search" name="search" value="{{ $filterValues['search'] ?? '' }}" placeholder="Search ticket, subject" class="rounded-[12px] border border-slate-200 px-3 py-2.5 text-sm text-slate-700" />
                    </div>
                    <div class="mt-4 flex gap-2">
                        <button type="submit" class="rounded-[12px] bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">Apply Filters</button>
                        <a href="{{ route('my.tickets') }}" class="rounded-[12px] border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50">Reset</a>
                    </div>
                </form>
            </div>

            <div class="overflow-hidden rounded-[18px] border border-slate-200 bg-white p-4 shadow-sm">
                <div class="mb-3 flex items-center justify-between">
                    <div><p class="text-sm font-semibold text-slate-900">My Internal IT Tickets</p><p class="mt-1 text-sm text-slate-500">Only tickets raised by your End User account are shown.</p></div>
                    <span class="text-xs text-slate-500">Showing {{ $tickets->count() }} tickets</span>
                </div>
                <div class="max-h-[520px] overflow-auto rounded-[12px] border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-left text-sm text-slate-700">
                        <thead class="sticky top-0 z-10 bg-gradient-to-r from-blue-600 via-indigo-600 to-sky-500 text-[10px] uppercase tracking-[0.2em] text-white"><tr><th class="px-3 py-3">Ticket</th><th class="px-3 py-3">Subject</th><th class="px-3 py-3">Requester</th><th class="px-3 py-3">State</th><th class="px-3 py-3">Category</th><th class="px-3 py-3">Priority</th><th class="px-3 py-3">Status</th><th class="px-3 py-3">Chat</th><th class="px-3 py-3">Created</th></tr></thead>
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
                                    <td class="px-3 py-3"><button type="button" title="{{ ($ticket->chat['status'] ?? '') === 'active' ? 'Open chat' : 'Chat not available' }}" data-chat-ticket-id="{{ $ticket->ticket_id }}" data-chat-ticket-number="{{ $ticket->ticket_number }}" data-chat-count="{{ $ticket->chat['unread_count'] ?? 0 }}" @click.stop="if ('{{ $ticket->chat['status'] ?? '' }}' === 'active') { $nextTick(() => openEndUserChat({{ $ticket->ticket_id }})) }" class="relative inline-flex h-8 w-8 items-center justify-center rounded-full {{ ($ticket->chat['status'] ?? '') === 'active' ? 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' : 'cursor-not-allowed bg-slate-100 text-slate-400' }}" {{ ($ticket->chat['status'] ?? '') === 'active' ? '' : 'disabled' }} aria-label="Chat"><svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M7 18.5 4 21v-5.5A8 8 0 0 1 12 7h1a8 8 0 0 1 8 8v.5a8 8 0 0 1-8 8H8.5L7 18.5Z"></path></svg><span data-chat-badge class="absolute -right-1 -top-1 {{ ($ticket->chat['unread_count'] ?? 0) > 0 ? '' : 'hidden' }} min-w-4 rounded-full bg-red-500 px-1 text-[9px] font-bold leading-4 text-white">{{ ($ticket->chat['unread_count'] ?? 0) > 99 ? '99+' : ($ticket->chat['unread_count'] ?? 0) }}</span></button><span class="ml-2 rounded-full px-2 py-1 text-[10px] font-semibold {{ ($ticket->chat['status'] ?? 'not_initiated') === 'active' ? 'bg-emerald-100 text-emerald-700' : (($ticket->chat['status'] ?? '') === 'ended' ? 'bg-slate-100 text-slate-600' : 'bg-amber-100 text-amber-700') }}">{{ ($ticket->chat['status'] ?? 'not_initiated') === 'active' ? 'Available' : (($ticket->chat['status'] ?? '') === 'ended' ? 'Ended' : 'Disabled') }}</span></td>
                                    <td class="whitespace-nowrap px-3 py-3">{{ $ticket->created_at }}</td>
                                </tr></tr>
                            @empty
                                <tr><td colspan="9" class="px-5 py-12 text-center text-slate-500">You have not raised any internal IT tickets.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div id="end-user-chat-panel" data-chat-panel class="hidden flex h-[500px] max-h-[75vh] w-[min(390px,calc(100vw-2rem))] flex-col overflow-hidden rounded-[18px] border border-slate-200 bg-white shadow-[0_18px_40px_rgba(15,23,42,0.14)]" style="position: fixed !important; right: 1rem !important; bottom: 1rem !important; left: auto !important; top: auto !important; z-index: 60 !important;"><div class="flex items-center justify-between gap-3 bg-gradient-to-r from-blue-600 via-blue-600 to-sky-500 px-4 py-3 text-white"><div class="flex items-center gap-3"><span class="inline-flex h-7 w-7 items-center justify-center rounded-full bg-white/15 ring-1 ring-white/30"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M7 18.5 4 21v-5.5A8 8 0 0 1 12 7h1a8 8 0 0 1 8 8v.5a8 8 0 0 1-8 8H8.5L7 18.5Z"></path></svg></span><p class="text-[15px] font-semibold">Ticket Chat</p></div><button type="button" data-chat-close class="text-xl font-light leading-none hover:text-slate-200">×</button></div><div class="bg-slate-100 px-4 py-3 text-center text-[12px] text-slate-600"><span>Chat initiated by IT Support Desk</span></div><div data-chat-messages class="min-h-0 flex-1 space-y-3 overflow-y-auto bg-white p-3 pr-2 [scrollbar-width:thin]"></div><div class="border-t border-slate-200 bg-white p-3"><form data-chat-form class="flex items-center gap-2 rounded-[14px] border border-sky-600 bg-white p-1.5"><input data-chat-input type="text" maxlength="2000" required placeholder="Type your message..." class="min-w-0 flex-1 rounded-[10px] border-0 bg-transparent px-3 py-2 text-sm text-slate-700 placeholder:text-slate-400 focus:outline-none" /><button type="submit" class="inline-flex h-11 w-11 items-center justify-center rounded-full bg-blue-600 text-white shadow-sm hover:bg-blue-700" aria-label="Send"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 11.5 20 4l-4.5 16-3.7-7.3L3 11.5Z"></path><path stroke-linecap="round" stroke-linejoin="round" d="M20 4 9.8 14.2"></path></svg></button></form></div></div>
        <style>
            #end-user-chat-panel { height: 500px !important; max-height: 500px !important; }
            #end-user-chat-panel [data-chat-messages] { height: 300px !important; min-height: 0 !important; max-height: 300px !important; flex: 0 0 300px !important; overflow-y: auto !important; }
        </style>

        <div x-show="selectedTicket" x-cloak class="fixed inset-0 z-50 bg-slate-900/40" @click.self="selectedTicket = null">
            <aside data-ticket-drawer class="absolute right-0 top-0 h-full w-full max-w-xl overflow-y-auto bg-white px-5 py-6 shadow-2xl">
                <div class="flex items-start justify-between gap-4"><div><p class="text-[11px] uppercase tracking-[0.32em] text-slate-500">Ticket Details</p><h3 class="mt-2 text-xl font-semibold text-slate-900" x-text="selectedTicket?.ticket_number"></h3><div class="mt-2 flex flex-wrap gap-2 text-sm"><span class="rounded-full bg-slate-100 px-3 py-1 text-slate-700" x-text="selectedTicket?.status_name || '-'">-</span><span class="rounded-full bg-red-100 px-3 py-1 text-red-700" x-text="selectedTicket?.priority_name || selectedTicket?.priority || '-'">-</span></div></div><button type="button" aria-label="Close ticket details" class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-100 text-2xl text-slate-600 hover:bg-slate-200" @click="selectedTicket = null">&times;</button></div>
                <div class="mt-6 flex items-center gap-1 bg-slate-50 px-2"><button type="button" class="border-b-2 px-3 py-3 text-sm" :class="tab === 'details' ? 'border-blue-600 font-semibold text-blue-600' : 'border-transparent text-slate-500'" @click="tab = 'details'">Details</button><button type="button" class="border-b-2 px-3 py-3 text-sm" :class="tab === 'history' ? 'border-blue-600 font-semibold text-blue-600' : 'border-transparent text-slate-500'" @click="tab = 'history'">Status History</button><button type="button" class="border-b-2 px-3 py-3 text-sm" :class="tab === 'attachments' ? 'border-blue-600 font-semibold text-blue-600' : 'border-transparent text-slate-500'" @click="tab = 'attachments'">Attachments</button><button type="button" class="border-b-2 border-transparent px-3 py-3 text-sm text-slate-500 hover:text-blue-600" @click="previewPopup = true; window.__myTicketsPreviewOpen = true">Preview</button></div>
                <div x-show="tab === 'details'" class="mt-5 space-y-4"><div class="grid grid-cols-2 gap-3 rounded-2xl bg-slate-50 p-4 text-sm"><div><span class="text-slate-500">Requester</span><p class="font-semibold" x-text="selectedTicket?.requester_gid || '-' "></p></div><div><span class="text-slate-500">State</span><p class="font-semibold" x-text="selectedTicket?.state_name || '-' "></p></div><div><span class="text-slate-500">Category</span><p class="font-semibold" x-text="selectedTicket?.category_name || '-' "></p></div><div><span class="text-slate-500">Device Type</span><p class="font-semibold" x-text="selectedTicket?.device_name || '-' "></p></div><div><span class="text-slate-500">Issue Type</span><p class="font-semibold" x-text="selectedTicket?.issue_type_name || '-' "></p></div><div><span class="text-slate-500">Impact</span><p class="font-semibold" x-text="selectedTicket?.impact_name || '-' "></p></div><div><span class="text-slate-500">Status</span><p class="font-semibold" x-text="selectedTicket?.status_name || '-' "></p></div></div><div class="rounded-[14px] border border-slate-200 bg-slate-50 p-4"><p class="text-sm font-semibold text-slate-900">Issue Description</p><p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-700" x-text="selectedTicket?.issue_description || 'No description available.'"></p></div><div class="flex items-center justify-between gap-3"><p class="font-semibold text-slate-900">Update Ticket</p><button type="button" class="text-sm font-semibold text-blue-600" @click="editing = !editing" x-text="editing ? 'Hide' : 'Edit'"></button></div><form x-show="editing" x-cloak method="POST" action="{{ route('my.tickets.update') }}" class="rounded-2xl border border-slate-200 bg-white p-4">@csrf<input type="hidden" name="ticket_id" x-bind:value="selectedTicket?.ticket_id || ''" /><label class="text-sm font-semibold text-slate-700">Status</label><select name="status_id" class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" x-bind:value="selectedTicket?.status_id || ''">@foreach($statusOptions as $status)<option value="{{ $status->status_id }}">{{ $status->status_name }}</option>@endforeach</select><label class="mt-3 block text-sm font-semibold text-slate-700">Remarks</label><textarea name="remarks" rows="3" class="mt-2 w-full rounded-xl border border-slate-200 px-3 py-2 text-sm" placeholder="Remarks"></textarea><button type="submit" class="mt-3 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Save Status</button></form></div>
                <div x-show="tab === 'history'" class="mt-5 space-y-3"><template x-for="entry in (selectedTicket?.history || [])" :key="entry.action_at + entry.action_type"><div class="rounded-xl border border-slate-200 p-3 text-sm"><p class="font-semibold text-slate-900" x-text="entry.to_status_name || entry.action_type"></p><p class="mt-1 text-slate-600" x-text="entry.remarks || '-' "></p><p class="mt-1 text-xs text-slate-500" x-text="(entry.action_by_gid || '-') + ' | ' + entry.action_at"></p></div></template><p x-show="!(selectedTicket?.history || []).length" class="text-sm text-slate-500">No history found.</p></div>
                <div x-show="tab === 'attachments'" class="mt-5 space-y-3"><template x-for="file in (selectedTicket?.attachments || [])" :key="file.attachment_id"><div class="flex items-center justify-between gap-4 rounded-2xl border border-slate-200 bg-white px-3 py-3 shadow-sm"><div class="min-w-0"><p class="truncate text-sm font-semibold text-slate-900" x-text="file.original_file_name"></p><p class="mt-1 text-xs text-slate-500" x-text="file.uploaded_at"></p></div><div class="flex shrink-0 items-center gap-2 text-sm"><a :href="file.view_url || '#'" target="_blank" rel="noopener noreferrer" class="font-semibold text-blue-600 hover:underline">View</a><span class="text-slate-300">|</span><a :href="file.download_url || '#'" download class="font-semibold text-blue-600 hover:underline">Download</a></div></div></template><p x-show="!(selectedTicket?.attachments || []).length" class="text-sm text-slate-500">No attachments found.</p></div>
                <div class="sticky bottom-0 left-0 z-20 mt-4 rounded-[18px] border border-slate-200 bg-white p-4 shadow-xl"><div class="flex justify-end"><button type="button" @click="selectedTicket = null" class="rounded-[10px] border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">Close</button></div></div>
            </aside>
        </div>

        <div x-show="previewPopup && selectedTicket" x-cloak class="fixed inset-0 z-[70] flex items-center justify-center bg-slate-900/60 p-4" @click.self="previewPopup = false; window.__myTicketsPreviewOpen = false">
            <div class="flex max-h-[90vh] w-full max-w-4xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl">
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-4"><div><p class="text-[11px] uppercase tracking-[0.24em] text-slate-500">Ticket Preview</p><h2 class="mt-1 text-xl font-bold text-slate-900" x-text="selectedTicket?.ticket_number"></h2></div><div class="flex items-center gap-2"><button type="button" onclick="printMyTicketPreview()" class="rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-700">Print</button><button type="button" onclick="printMyTicketPreview()" class="rounded-lg bg-emerald-600 px-3 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Export PDF</button><button type="button" class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50" @click="previewPopup = false; window.__myTicketsPreviewOpen = false">Close</button></div></div>
                <div id="my-ticket-popup-preview" class="overflow-y-auto p-6"><div class="border-l-4 border-blue-600 bg-blue-50 px-4 py-3"><h3 class="text-sm font-semibold uppercase tracking-wide text-blue-700">Ticket Information</h3></div><div class="mt-4 grid grid-cols-2 gap-4 rounded-xl bg-slate-50 p-4 text-sm sm:grid-cols-3"><div><span class="text-xs uppercase tracking-widest text-slate-500">Ticket ID</span><p class="mt-1 font-bold" x-text="selectedTicket?.ticket_number"></p></div><div><span class="text-xs uppercase tracking-widest text-slate-500">Requester</span><p class="mt-1 font-semibold" x-text="selectedTicket?.requester_gid"></p></div><div><span class="text-xs uppercase tracking-widest text-slate-500">State</span><p class="mt-1 font-semibold" x-text="selectedTicket?.state_name || '-' "></p></div><div><span class="text-xs uppercase tracking-widest text-slate-500">Category</span><p class="mt-1 font-semibold" x-text="selectedTicket?.category_name || '-' "></p></div><div><span class="text-xs uppercase tracking-widest text-slate-500">Device Type</span><p class="mt-1 font-semibold" x-text="selectedTicket?.device_name || '-' "></p></div><div><span class="text-xs uppercase tracking-widest text-slate-500">Issue Type</span><p class="mt-1 font-semibold" x-text="selectedTicket?.issue_type_name || '-' "></p></div><div><span class="text-xs uppercase tracking-widest text-slate-500">Impact</span><p class="mt-1 font-semibold" x-text="selectedTicket?.impact_name || '-' "></p></div><div><span class="text-xs uppercase tracking-widest text-slate-500">Priority</span><p class="mt-1 font-semibold" x-text="selectedTicket?.priority_name || selectedTicket?.priority || '-' "></p></div><div><span class="text-xs uppercase tracking-widest text-slate-500">Status</span><p class="mt-1 font-semibold" x-text="selectedTicket?.status_name || '-' "></p></div></div><div class="mt-5 rounded-xl border border-violet-200 bg-violet-50 p-5"><h3 class="text-sm font-semibold uppercase tracking-wide text-violet-700">Description</h3><p class="mt-3 whitespace-pre-line text-sm leading-6 text-slate-700" x-text="selectedTicket?.issue_description || 'No description available.'"></p></div><div class="mt-5 rounded-xl border border-slate-200 p-5"><h3 class="text-sm font-semibold uppercase tracking-wide text-slate-700">Update History</h3><div class="mt-3 space-y-3"><template x-for="entry in (selectedTicket?.history || [])" :key="'preview-' + entry.action_at + entry.action_type"><div class="rounded-lg border border-slate-200 bg-slate-50 p-4"><p class="text-sm font-bold text-blue-600" x-text="entry.action_type"></p><p class="mt-1 text-xs text-slate-600" x-text="'by ' + (entry.action_by_gid || 'System') + ' | ' + entry.action_at"></p><p class="mt-2 text-sm font-semibold text-slate-900" x-text="'Remarks: ' + (entry.remarks || 'No remarks')"></p></div></template><p x-show="!(selectedTicket?.history || []).length" class="text-sm text-slate-500">No update history found.</p></div></div></div>
            </div>
        </div>
    </div>
</x-app-layout>

<script>
    function printMyTicketPreview() {
        const preview = document.getElementById('my-ticket-popup-preview');
        if (!preview) return;
        const printWindow = window.open('', '_blank', 'width=900,height=700');
        printWindow.document.write('<html><head><title>My Ticket Preview</title><style>body{font-family:Arial,sans-serif;padding:24px;color:#0f172a}</style></head><body>' + preview.innerHTML + '</body></html>');
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
        const panel = document.getElementById('end-user-chat-panel');
        const panelTitle = panel?.querySelector('p');
        const messagesBox = panel?.querySelector('[data-chat-messages]');
        const form = panel?.querySelector('[data-chat-form]');
        const input = panel?.querySelector('[data-chat-input]');
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
        const messagesUrl = @json(route('my.tickets.chat.messages', ['ticket' => '__TICKET__']));
        const sendUrl = @json(route('my.tickets.chat.send', ['ticket' => '__TICKET__']));

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

        function renderMessages(messages, stickToBottom = false) {
            if (!messagesBox) return;
            const previousScrollTop = messagesBox.scrollTop;
            const wasAtBottom = messagesBox.scrollHeight - messagesBox.scrollTop - messagesBox.clientHeight < 24;
            messagesBox.innerHTML = messages.length
                ? messages.map((message) => {
                    const isUser = message.sender_role === 'End User';
                    const avatar = `<span class="mt-1 inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-blue-100 text-blue-600"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm0 2c-4.42 0-8 2.24-8 5v1h16v-1c0-2.76-3.58-5-8-5Z"/></svg></span>`;
                    const content = `<div class="min-w-0 max-w-[78%]"><p class="mb-1 text-[10px] font-semibold ${isUser ? 'text-right' : 'text-left'} text-slate-500">${escapeText(message.sender_name)}</p><div class="rounded-[14px] border ${isUser ? 'border-blue-100 bg-blue-50' : 'border-slate-200 bg-slate-50'} px-3 py-2"><p class="overflow-hidden text-ellipsis whitespace-nowrap text-[13px] leading-5 text-slate-700" title="${escapeText(message.message)}">${escapeText(message.message)}</p></div><p class="mt-1 text-[9px] leading-3 text-slate-400 ${isUser ? 'text-right' : 'text-left'}">${escapeText(message.sent_at)}</p></div>`;
                    return `<div class="flex ${isUser ? 'justify-end' : 'justify-start'} items-start gap-2">${isUser ? content + avatar : avatar + content}</div>`;
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
            updateChatBadge(activeChatTicket, data.unread_count || 0);
            renderMessages(data.messages || []);
            if (data.status !== 'active') {
                clearInterval(chatTimer);
                chatTimer = null;
            }
        }

        window.openEndUserChat = async function (ticketId) {
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
        const panel = document.getElementById('end-user-chat-panel');

        function isVisible(element) {
            return Boolean(element && element.getClientRects().length && getComputedStyle(element).display !== 'none');
        }

        window.positionEndUserChatPanel = function () {
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

        window.addEventListener('resize', window.positionEndUserChatPanel);
        new MutationObserver(window.positionEndUserChatPanel).observe(document.body, { attributes: true, subtree: true, attributeFilter: ['style', 'class'] });
        window.positionEndUserChatPanel();
    }());
</script>

<script>
    (function () {
        const idleDelay = 5000;
        window.__myTicketsPreviewOpen = false;
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
            const previewOpen = window.__myTicketsPreviewOpen || isVisible(document.querySelector('#my-ticket-popup-preview')?.closest('.fixed'));
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
