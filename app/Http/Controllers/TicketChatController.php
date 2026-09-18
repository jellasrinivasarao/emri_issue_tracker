<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TicketChatController extends Controller
{
    private const TTL_MINUTES = 120;

    public function initiate(Request $request, int $ticketId): JsonResponse
    {
        $ticket = $this->ticketForSupport($request, $ticketId);
        abort_unless($ticket, 404);

        $chat = $this->chatForTicket($ticketId);
        $chat['status'] = 'active';
        $chat['ticket_id'] = $ticketId;
        $chat['initiated_by'] = (int) $request->user()->getAuthIdentifier();
        $chat['initiated_by_name'] = $request->user()->user_name ?: $request->user()->login_id;
        $chat['initiated_at'] = $chat['initiated_at'] ?? now()->toIso8601String();
        $chat['messages'] = array_values($chat['messages'] ?? []);

        Cache::put($this->cacheKey($ticketId), $chat, now()->addMinutes(self::TTL_MINUTES));

        return response()->json($this->statusPayload($chat));
    }

    public function status(Request $request, int $ticketId): JsonResponse
    {
        abort_unless($this->canAccessTicket($request, $ticketId), 404);

        return response()->json($this->statusPayload($this->chatForTicket($ticketId), $request->user()));
    }

    public function messages(Request $request, int $ticketId): JsonResponse
    {
        abort_unless($this->canAccessTicket($request, $ticketId), 404);
        $chat = $this->chatForTicket($ticketId);
        if ($request->boolean('mark_read')) {
            $this->markAsRead($chat, $request->user());
            Cache::put($this->cacheKey($ticketId), $chat, now()->addMinutes(self::TTL_MINUTES));
        }

        return response()->json($this->statusPayload($chat, $request->user()));
    }

    public function send(Request $request, int $ticketId): JsonResponse
    {
        abort_unless($this->canAccessTicket($request, $ticketId), 404);

        $validated = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);
        $chat = $this->chatForTicket($ticketId);
        abort_unless(($chat['status'] ?? null) === 'active', 409, 'Chat is not active.');

        $user = $request->user();
        $chat['messages'] = array_values($chat['messages'] ?? []);
        $chat['messages'][] = [
            'id' => (string) Str::uuid(),
            'sender_id' => (int) $user->getAuthIdentifier(),
            'sender_name' => $user->user_name ?: $user->login_id,
            'sender_role' => $user->hasRole('IT Support Desk') ? 'IT Support Desk' : 'End User',
            'message' => trim($validated['message']),
            'sent_at' => now()->toIso8601String(),
        ];

        Cache::put($this->cacheKey($ticketId), $chat, now()->addMinutes(self::TTL_MINUTES));

        return response()->json($this->statusPayload($chat, $user));
    }

    public function endForClosedTicket(int $ticketId): void
    {
        $chat = Cache::get($this->cacheKey($ticketId), []);
        if (($chat['status'] ?? null) !== 'active') {
            return;
        }

        $chat['status'] = 'ended';
        $chat['ended_at'] = now()->toIso8601String();
        Cache::put($this->cacheKey($ticketId), $chat, now()->addMinutes(self::TTL_MINUTES));
    }

    public function statusForTicket(int $ticketId, $user = null): array
    {
        return $this->statusPayload($this->chatForTicket($ticketId), $user ?: auth()->user());
    }

    public function clearForUser($user): void
    {
        if (! $user) {
            return;
        }

        $ticketIds = $user->hasRole('IT Support Desk')
            ? DB::table('txn_it_support_ticket')->where('assigned_desk', 10)->pluck('ticket_id')
            : DB::table('txn_it_support_ticket')
                ->where(function ($query) use ($user) {
                    $query->where('created_by', $user->user_id)
                        ->orWhere('requester_gid', (string) $user->login_id);
                })
                ->pluck('ticket_id');

        foreach ($ticketIds as $ticketId) {
            Cache::forget($this->cacheKey((int) $ticketId));
        }
    }

    public function clearAll(): void
    {
        DB::table('txn_it_support_ticket')
            ->pluck('ticket_id')
            ->each(fn ($ticketId) => Cache::forget($this->cacheKey((int) $ticketId)));
    }

    private function ticketForSupport(Request $request, int $ticketId): ?object
    {
        if (! $request->user()?->hasRole('IT Support Desk')) {
            return null;
        }

        return DB::table('txn_it_support_ticket')
            ->where('ticket_id', $ticketId)
            ->where('assigned_desk', 10)
            ->first();
    }

    private function canAccessTicket(Request $request, int $ticketId): bool
    {
        if ($this->ticketForSupport($request, $ticketId)) {
            return true;
        }

        $user = $request->user();
        if (! $user?->hasRole('End User')) {
            return false;
        }

        return DB::table('txn_it_support_ticket')
            ->where('ticket_id', $ticketId)
            ->where(function ($query) use ($user) {
                $query->where('created_by', $user->user_id)
                    ->orWhere('requester_gid', (string) $user->login_id);
            })
            ->exists();
    }

    private function statusPayload(array $chat, $user = null): array
    {
        $status = $chat['status'] ?? 'not_initiated';
        $messages = array_values($chat['messages'] ?? []);
        $userRole = $user?->hasRole('IT Support Desk') ? 'IT Support Desk' : 'End User';
        $readAt = $user ? ($chat['read_at'][(string) $user->getAuthIdentifier()] ?? null) : null;
        $unreadCount = collect($messages)->filter(function (array $message) use ($userRole, $readAt): bool {
            return ($message['sender_role'] ?? null) !== $userRole
                && (! $readAt || ($message['sent_at'] ?? '') > $readAt);
        })->count();

        return [
            'status' => $status,
            'active' => $status === 'active',
            'initiated_at' => $chat['initiated_at'] ?? null,
            'ended_at' => $chat['ended_at'] ?? null,
            'messages' => $messages,
            'unread_count' => $unreadCount,
        ];
    }

    private function markAsRead(array &$chat, $user): void
    {
        if (! $user) {
            return;
        }

        $chat['read_at'] = $chat['read_at'] ?? [];
        $chat['read_at'][(string) $user->getAuthIdentifier()] = now()->toIso8601String();
    }

    private function chatForTicket(int $ticketId): array
    {
        $chat = Cache::get($this->cacheKey($ticketId), []);
        $ticketStatus = DB::table('txn_it_support_ticket')
            ->where('ticket_id', $ticketId)
            ->value('status_id');

        if ((int) $ticketStatus !== 10 && ($chat['status'] ?? null) === 'ended') {
            $chat['status'] = 'active';
            unset($chat['ended_at']);
            Cache::put($this->cacheKey($ticketId), $chat, now()->addMinutes(self::TTL_MINUTES));
        }

        return $chat;
    }

    private function cacheKey(int $ticketId): string
    {
        return 'it-support-chat:ticket:' . $ticketId;
    }
}
