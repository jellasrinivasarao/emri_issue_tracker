<?php

namespace Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class OperationalStatusController extends Controller
{
    public function generateToken(Request $request): JsonResponse
    {
        $startedAt = microtime(true);
        $username = (string) $request->input('username', 'unknown');
        $validated = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = DB::table('mst_end_user_api_user')
            ->where('username', $validated['username'])
            ->where('is_active', 1)
            ->first(['end_user_id', 'username', 'user_name', 'password']);

        if (! $user || (string) $user->password !== (string) $validated['password']) {
            $response = [
                'success' => false,
                'status_code' => 401,
                'message' => 'Invalid username or password.',
            ];
            $this->logApiCall($request, $username, ['username' => $username], $response, 401, $startedAt);

            return response()->json($response, 401);
        }

        $plainToken = Str::random(80);

        DB::table('api_access_tokens')->insert([
            'end_user_id' => $user->end_user_id,
            'token_name' => 'operational-dashboard-status',
            'token_hash' => hash('sha256', $plainToken),
            'expires_at' => null,
            'is_active' => 1,
            'created_at' => now(),
            'last_used_at' => null,
        ]);

        $response = [
            'success' => true,
            'status_code' => 200,
            'message' => 'API token generated successfully.',
            'token_type' => 'Bearer',
            'token' => $plainToken,
            'expires_at' => null,
            'username' => $user->username,
        ];
        $this->logApiCall(
            $request,
            $user->username,
            ['username' => $username],
            [...$response, 'token' => '[REDACTED]'],
            200,
            $startedAt
        );

        return response()->json($response, 200);
    }

    public function updateStatus(Request $request): JsonResponse
    {
        $startedAt = microtime(true);
        $ticketNumber = trim((string) $request->input('issue_number', ''));
        $statusName = trim((string) $request->input('status', ''));
        $remarks = trim((string) $request->input('remarks', ''));
        $updateBy = trim((string) $request->input('update_by', ''));

        $errors = [];
        if ($ticketNumber === '') {
            $errors['issue_number'] = ['The issue_number field is required.'];
        }
        if ($statusName === '') {
            $errors['status'] = ['The status field is required.'];
        }
        if ($updateBy === '') {
            $errors['update_by'] = ['The update_by field is required.'];
        }
        if (mb_strlen($remarks) > 2000) {
            $errors['remarks'] = ['The remarks field may not be greater than 2000 characters.'];
        }

        if ($errors) {
            $response = [
                'success' => false,
                'status_code' => 422,
                'ticket_number' => $ticketNumber ?: null,
                'message' => 'Validation failed.',
                'errors' => $errors,
            ];
            $this->logApiCall($request, $updateBy ?: 'unknown', $request->only(['issue_number', 'status', 'remarks', 'update_by']), $response, 422, $startedAt);

            return response()->json($response, 422);
        }

        try {
            $updatedByUser = DB::table('mst_user')
                ->where('is_active', 1)
                ->where(function ($query) use ($updateBy) {
                    $query->where('login_id', $updateBy)
                        ->orWhere('employee_code', $updateBy)
                        ->orWhereRaw('LOWER(TRIM(user_name)) = ?', [strtolower($updateBy)]);
                })
                ->first(['user_id', 'login_id', 'employee_code', 'user_name']);

            if (! $updatedByUser) {
                $response = [
                    'success' => false,
                    'status_code' => 422,
                    'ticket_number' => $ticketNumber,
                    'message' => 'Invalid update_by user.',
                    'errors' => [
                        'update_by' => ['The supplied update_by user was not found or is inactive.'],
                    ],
                ];
                $this->logApiCall($request, $updateBy ?: 'unknown', $request->only(['issue_number', 'status', 'remarks', 'update_by']), $response, 422, $startedAt);

                return response()->json($response, 422);
            }

            $issue = DB::table('txn_issue')
                ->where('issue_number', $ticketNumber)
                ->first(['issue_id', 'issue_number', 'status_id']);

            if (! $issue) {
                $response = [
                    'success' => false,
                    'status_code' => 404,
                    'ticket_number' => $ticketNumber,
                    'message' => 'Ticket not found.',
                ];
                $this->logApiCall($request, $updateBy ?: 'unknown', $request->only(['issue_number', 'status', 'remarks', 'update_by']), $response, 404, $startedAt);

                return response()->json($response, 404);
            }

            $status = DB::table('mst_issue_status')
                ->whereRaw('LOWER(TRIM(status_name)) = ?', [strtolower($statusName)])
                ->where(function ($query) {
                    $query->where('is_active', 1)->orWhereNull('is_active');
                })
                ->first(['status_id', 'status_name']);

            if (! $status) {
                $response = [
                    'success' => false,
                    'status_code' => 422,
                    'ticket_number' => $ticketNumber,
                    'message' => 'Invalid status.',
                ];
                $this->logApiCall($request, $updateBy ?: 'unknown', $request->only(['issue_number', 'status', 'remarks', 'update_by']), $response, 422, $startedAt);

                return response()->json($response, 422);
            }

            DB::transaction(function () use ($issue, $status, $remarks, $updateBy, $updatedByUser): void {
                DB::table('txn_issue')
                    ->where('issue_id', $issue->issue_id)
                    ->update([
                        'status_id' => $status->status_id,
                        'updated_at' => now(),
                    ]);

                DB::table('txn_issue_status_history')->insert([
                    'issue_id' => $issue->issue_id,
                    'old_status_id' => $issue->status_id,
                    'new_status_id' => $status->status_id,
                    'changed_by_user_id' => $updatedByUser->user_id,
                    'comment' => 'Updated by: ' . $updateBy . ($remarks !== '' ? ' - ' . $remarks : ''),
                    'changed_at' => now(),
                ]);
            });

            $response = [
                'success' => true,
                'status_code' => 200,
                'ticket_number' => $issue->issue_number,
                'update_by' => $updateBy,
                'message' => 'Ticket status updated successfully.',
            ];
            $this->logApiCall($request, $updateBy, $request->only(['issue_number', 'status', 'remarks', 'update_by']), $response, 200, $startedAt);

            return response()->json($response, 200);
        } catch (Throwable $exception) {
            Log::error('Operational status API update failed', [
                'issue_number' => $ticketNumber,
                'error' => $exception->getMessage(),
            ]);

            $response = [
                'success' => false,
                'status_code' => 500,
                'ticket_number' => $ticketNumber,
                'message' => 'Unable to update ticket status.',
            ];
            $this->logApiCall(
                $request,
                (string) ($request->user()?->login_id ?? 'unknown'),
                $request->only(['issue_number', 'status', 'remarks', 'update_by']),
                $response,
                500,
                $startedAt,
                $exception->getMessage()
            );

            return response()->json($response, 500);
        }
    }

    private function logApiCall(
        Request $request,
        string $gid,
        array $requestPayload,
        array $responsePayload,
        int $httpStatusCode,
        float $startedAt,
        ?string $errorMessage = null
    ): void {
        try {
            if (! Schema::hasTable('txn_end_user_api_call_log')) {
                return;
            }

            DB::table('txn_end_user_api_call_log')->insert([
                'gid' => $gid !== '' ? $gid : 'unknown',
                'url' => $request->fullUrl(),
                'request' => json_encode($requestPayload, JSON_UNESCAPED_SLASHES),
                'response' => json_encode($responsePayload, JSON_UNESCAPED_SLASHES),
                'response_time' => round((microtime(true) - $startedAt) * 1000, 3),
                'http_status_code' => $httpStatusCode,
                'api_status' => $httpStatusCode >= 400 ? 'error' : 'success',
                'error_message' => $errorMessage,
                'created_at' => now(),
            ]);
        } catch (Throwable) {
            // Logging must not change the API response.
        }
    }
}
