<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class MyTicketsController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $query = $this->ownedTicketsQuery($user)
            ->leftJoin('mst_it_support_category as category', 'category.category_id', '=', 'ticket.category_id')
            ->leftJoin('mst_it_support_device as device', 'device.device_type_id', '=', 'ticket.device_type_id')
            ->leftJoin('mst_it_support_issue_type as issue_type', 'issue_type.issue_type_id', '=', 'ticket.issue_type_id')
            ->leftJoin('mst_it_support_impact as impact', 'impact.impact_id', '=', 'ticket.impact_id')
            ->leftJoin('mst_it_support_status as status', 'status.status_id', '=', 'ticket.status_id')
            ->leftJoin('mst_priority as priority', 'priority.priority_id', '=', 'ticket.priority_id')
            ->leftJoin('mst_state as state', 'state.state_id', '=', 'ticket.state_id');

        if ($request->filled('state_id')) {
            $query->where('ticket.state_id', (int) $request->input('state_id'));
        }
        if ($request->filled('status_id')) {
            $statusFilter = (string) $request->input('status_id');
            $statusIds = match ($statusFilter) {
                'in_process' => [2, 3, 7],
                'resolved' => [8],
                'closed' => [10],
                'reopened' => [9],
                default => [(int) $statusFilter],
            };
            $query->whereIn('ticket.status_id', $statusIds);
        }
        if ($request->filled('priority_id')) {
            $query->where('ticket.priority_id', (int) $request->input('priority_id'));
        }
        if ($request->filled('date_from')) {
            $query->whereDate('ticket.created_at', '>=', $request->input('date_from'));
        }
        if ($request->filled('date_to')) {
            $query->whereDate('ticket.created_at', '<=', $request->input('date_to'));
        }
        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($searchQuery) use ($search) {
                $searchQuery->where('ticket.ticket_number', 'like', "%{$search}%")
                    ->orWhere('ticket.requester_gid', 'like', "%{$search}%")
                    ->orWhere('ticket.issue_subject', 'like', "%{$search}%")
                    ->orWhere('ticket.issue_description', 'like', "%{$search}%");
            });
        }

        $tickets = $query
            ->orderByDesc('ticket.ticket_id')
            ->get([
                'ticket.ticket_id', 'ticket.ticket_number', 'ticket.requester_gid', 'ticket.state_id',
                'ticket.status_id', 'ticket.issue_subject', 'ticket.issue_description', 'ticket.priority',
                'ticket.created_at', 'category.category_name', 'device.device_name', 'issue_type.issue_type_name',
                'impact.impact_name', 'status.status_name', 'priority.priority_name', 'state.state_name',
            ]);

        $tickets->each(function ($ticket): void {
            $ticket->chat = app(TicketChatController::class)->statusForTicket((int) $ticket->ticket_id, auth()->user());
            $ticket->history = DB::table('txn_it_support_history as history')
                ->leftJoin('mst_it_support_status as from_status', 'from_status.status_id', '=', 'history.from_status_id')
                ->leftJoin('mst_it_support_status as to_status', 'to_status.status_id', '=', 'history.to_status_id')
                ->where('history.ticket_id', $ticket->ticket_id)
                ->orderBy('history.history_id')
                ->get([
                    'history.action_type', 'history.remarks', 'history.action_by_gid', 'history.action_at',
                    'from_status.status_name as from_status_name', 'to_status.status_name as to_status_name',
                ]);
            $ticket->attachments = DB::table('txn_it_support_attachment')
                ->where('ticket_id', $ticket->ticket_id)
                ->where('is_active', 1)
                ->orderByDesc('attachment_id')
                ->get(['attachment_id', 'original_file_name', 'file_path', 'file_size', 'mime_type', 'uploaded_at'])
                ->map(function ($attachment) {
                    $attachment->view_url = route('my.tickets.attachment.preview', $attachment->attachment_id);
                    $attachment->download_url = route('my.tickets.attachment.download', $attachment->attachment_id);
                    return $attachment;
                });
        });

        $ownedQuery = $this->ownedTicketsQuery($user);
        $statusCounts = [
            'total' => (clone $ownedQuery)->count(),
            'in_progress' => (clone $ownedQuery)->whereIn('status_id', [2, 3, 7])->count(),
            'resolved' => (clone $ownedQuery)->where('status_id', 8)->count(),
            'closed' => (clone $ownedQuery)->where('status_id', 10)->count(),
            'reopened' => (clone $ownedQuery)->where('status_id', 9)->count(),
        ];

        return view('pages.my-tickets', [
            'tickets' => $tickets,
            'statusCounts' => $statusCounts,
            'stateOptions' => DB::table('mst_state')->where('is_active', 1)->orderBy('state_name')->get(),
            'statusOptions' => DB::table('mst_it_support_status')->where('is_active', 1)->orderBy('display_order')->get(),
            'priorityOptions' => DB::table('mst_priority')->where('is_active', 1)->orderBy('display_order')->get(),
            'filterValues' => $request->only(['state_id', 'status_id', 'priority_id', 'date_from', 'date_to', 'search']),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'ticket_id' => ['required', 'integer', 'exists:txn_it_support_ticket,ticket_id'],
            'status_id' => ['required', 'integer', 'exists:mst_it_support_status,status_id'],
            'remarks' => ['nullable', 'string', 'max:2000'],
        ]);

        $user = $request->user();
        $ticket = $this->ownedTicketsQuery($user)
            ->where('ticket.ticket_id', $validated['ticket_id'])
            ->first(['ticket.*']);

        abort_unless($ticket, 404);

        DB::transaction(function () use ($validated, $ticket, $user): void {
            DB::table('txn_it_support_ticket')
                ->where('ticket_id', $ticket->ticket_id)
                ->update([
                    'status_id' => $validated['status_id'],
                    'updated_by' => $user?->getAuthIdentifier(),
                    'updated_at' => now(),
                    'resolved_at' => (int) $validated['status_id'] === 8 ? now() : $ticket->resolved_at,
                ]);

            DB::table('txn_it_support_history')->insert([
                'ticket_id' => $ticket->ticket_id,
                'action_type' => 'STATUS_UPDATED',
                'from_status_id' => $ticket->status_id,
                'to_status_id' => $validated['status_id'],
                'action_by_user_id' => $user?->getAuthIdentifier(),
                'action_by_gid' => $user?->login_id ?: $user?->user_name,
                'remarks' => $validated['remarks'] ?: 'Ticket status updated.',
                'action_at' => now(),
            ]);
        });

        if ((int) $validated['status_id'] === 10) {
            app(TicketChatController::class)->endForClosedTicket((int) $ticket->ticket_id);
        }

        return redirect()->route('my.tickets')->with('success', 'Ticket status updated.');
    }

    public function previewAttachment(Request $request, int $id)
    {
        $attachment = $this->ownedAttachment($request->user(), $id);
        abort_unless($attachment, 404);
        $filePath = Storage::disk('public')->path($attachment->file_path);
        abort_unless(is_file($filePath), 404, 'Attachment file not found.');

        return response()->file($filePath, ['Content-Type' => $attachment->mime_type ?: 'application/octet-stream']);
    }

    public function downloadAttachment(Request $request, int $id)
    {
        $attachment = $this->ownedAttachment($request->user(), $id);
        abort_unless($attachment, 404);
        $filePath = Storage::disk('public')->path($attachment->file_path);
        abort_unless(is_file($filePath), 404, 'Attachment file not found.');

        return response()->download($filePath, $attachment->original_file_name);
    }

    private function ownedTicketsQuery($user)
    {
        return DB::table('txn_it_support_ticket as ticket')
            ->where(function ($query) use ($user) {
                $query->where('ticket.created_by', $user->user_id)
                    ->orWhere('ticket.requester_gid', (string) $user->login_id);
            });
    }

    private function ownedAttachment($user, int $id): ?object
    {
        return DB::table('txn_it_support_attachment as attachment')
            ->join('txn_it_support_ticket as ticket', 'ticket.ticket_id', '=', 'attachment.ticket_id')
            ->where('attachment.attachment_id', $id)
            ->where('attachment.is_active', 1)
            ->where(function ($query) use ($user) {
                $query->where('ticket.created_by', $user->user_id)
                    ->orWhere('ticket.requester_gid', (string) $user->login_id);
            })
            ->first(['attachment.*']);
    }
}
