<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Middleware\EnsureSingleUserSession;
use App\Jobs\SendEndUserOtpMail;
use App\Models\MailLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class EndUserAccessController extends Controller
{
    public function showGidVerification(): View
    {
        return view('auth.end-user-gid');
    }

    public function verifyGid(Request $request): RedirectResponse
    {
        $validated = $request->validate(['gid' => ['required', 'string', 'max:100']]);
        $gid = trim($validated['gid']);

        $user = User::query()
            ->where(function ($query) use ($gid) {
                $query->where('login_id', $gid)
                    ->orWhere('user_name', $gid)
                    ->orWhere('official_email', $gid);
            })
            ->first();

        if ($user && ! empty($user->login_id)) {
            $gid = (string) $user->login_id;
        }

        $apiConfig = $this->apiConfig();
        if ($apiConfig && (int) $apiConfig->is_api_required === 1) {
            $statusResponse = $this->callEmployeeApi($apiConfig, $apiConfig->status_endpoint, $gid);

            $statusResponseGid = (string) data_get($statusResponse, 'data.gid', data_get($statusResponse, 'gid', ''));
            if (! $statusResponse || strtolower((string) data_get($statusResponse, 'status')) !== 'success' || ($statusResponseGid !== '' && strcasecmp($statusResponseGid, $gid) !== 0)) {
                return back()->withInput()->with('error', 'Invalid GID.');
            }

            $externalStatus = data_get($statusResponse, 'data.employee_status');
            $externalActive = data_get($statusResponse, 'data.active', data_get($statusResponse, 'data.is_active'));
            if (in_array(strtolower((string) $externalStatus), ['inactive', 'disabled', 'terminated'], true) || $externalActive === false || $externalActive === 0 || $externalActive === '0') {
                return back()->withInput()->with('error', 'Employee/GID is inactive.');
            }
        }

        if (! $user) {
            if (! $apiConfig || (int) $apiConfig->is_api_required !== 1) {
                return back()->withInput()->with('error', 'Invalid GID.');
            }

            $detailsResponse = $this->callEmployeeApi($apiConfig, $apiConfig->details_endpoint, $gid);
            $detailsResponseGid = (string) data_get($detailsResponse, 'data.gid', data_get($detailsResponse, 'gid', ''));
            if (! $detailsResponse || strtolower((string) data_get($detailsResponse, 'status')) !== 'success' || ($detailsResponseGid !== '' && strcasecmp($detailsResponseGid, $gid) !== 0)) {
                return back()->withInput()->with('error', 'Invalid GID.');
            }

            $user = $this->storeExternalEmployee($gid, (array) data_get($detailsResponse, 'data', []));
            if (! $user) {
                return back()->withInput()->with('error', 'Employee details could not be stored.');
            }
        }

        if (! $this->isActive($user)) {
            return back()->withInput()->with('error', 'Employee/GID is inactive.');
        }

        if (! $this->ensureEndUserRole($user)) {
            return back()->withInput()->with('error', 'End User role is not configured.');
        }

        $user->password_reset_otp = null;
        $user->password_reset_otp_expires_at = null;
        $user->save();

        $otp = random_int(100000, 999999);
        $user->password_reset_otp = Hash::make((string) $otp);
        $user->password_reset_otp_expires_at = now()->addMinutes(15);
        $user->save();

        $mailResult = $this->sendOtp($user, $otp);
        if (! $mailResult['success']) {
            return back()->withInput()->with('error', 'Unable to send OTP to the registered email address. Please try again.');
        }

        $request->session()->put([
            'end_user_gid' => $user->login_id,
            'end_user_id' => $user->user_id,
        ]);

        return redirect()->route('end.user.otp');
    }

    private function apiConfig(): ?object
    {
        if (! Schema::hasTable('mst_end_user_api_configuration')) {
            return null;
        }

        return DB::table('mst_end_user_api_configuration')
            ->where('config_code', 'EMPLOYEE_STATUS_DETAILS')
            ->where('is_active', 1)
            ->first();
    }

    private function callEmployeeApi(object $config, ?string $endpoint, string $gid): ?array
    {
        if (! $endpoint || ! $config->base_url) {
            return null;
        }

        $url = rtrim($config->base_url, '/') . '/' . ltrim($endpoint, '/');
        $requestPayload = ['gid' => $gid];
        $startedAt = microtime(true);

        try {
            $timeoutSeconds = max(1, min((int) ($config->timeout_seconds ?: 5), 10));
            $response = Http::connectTimeout(min(3, $timeoutSeconds))
                ->timeout($timeoutSeconds)
                ->acceptJson()
                ->post($url, $requestPayload);

            $responseBody = $response->body();
            $responseData = $response->json();
            $this->logApiCall(
                $gid,
                $url,
                $requestPayload,
                $responseBody,
                microtime(true) - $startedAt,
                $response->status(),
                $response->successful() ? 'success' : 'error'
            );

            return $response->successful() && is_array($responseData) ? $responseData : null;
        } catch (\Throwable $exception) {
            $this->logApiCall(
                $gid,
                $url,
                $requestPayload,
                null,
                microtime(true) - $startedAt,
                null,
                'error',
                $exception->getMessage()
            );

            return null;
        }
    }

    private function logApiCall(
        string $gid,
        string $url,
        array $requestPayload,
        ?string $responseBody,
        float $elapsedSeconds,
        ?int $httpStatusCode,
        string $apiStatus,
        ?string $errorMessage = null
    ): void {
        try {
            if (! Schema::hasTable('txn_end_user_api_call_log')) {
                return;
            }

            DB::table('txn_end_user_api_call_log')->insert([
                'gid' => $gid,
                'url' => $url,
                'request' => json_encode($requestPayload, JSON_UNESCAPED_SLASHES),
                'response' => $responseBody,
                'response_time' => round($elapsedSeconds * 1000, 3),
                'http_status_code' => $httpStatusCode,
                'api_status' => $apiStatus,
                'error_message' => $errorMessage,
                'created_at' => now(),
            ]);
        } catch (\Throwable) {
            // API logging must never prevent the employee validation flow.
        }
    }

    private function storeExternalEmployee(string $gid, array $employeeData): ?User
    {
        $payload = [
            'employee_code' => data_get($employeeData, 'gid', $gid),
            'login_id' => data_get($employeeData, 'gid', $gid),
            'user_name' => data_get($employeeData, 'name', $gid),
            'official_email' => data_get($employeeData, 'mail_id', data_get($employeeData, 'email')),
            'department' => data_get($employeeData, 'department'),
            'role_id' => 9,
            'user_status' => strtoupper((string) data_get($employeeData, 'employee_status', 'ACTIVE')),
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $payload = array_filter($payload, fn ($value, $column) => Schema::hasColumn('mst_user', $column), ARRAY_FILTER_USE_BOTH);
        $userId = DB::table('mst_user')->insertGetId($payload);

        return User::find($userId);
    }

        public function showOtp(Request $request): View|RedirectResponse
        {
            if (! $request->session()->has('end_user_id') && ! $request->session()->has('end_user_gid')) {
                return redirect()->route('end.user.gid');
            }

            return view('auth.end-user-otp', [
                'gid' => $request->session()->get('end_user_gid'),
            ]);
        }

        public function verifyOtp(Request $request): RedirectResponse
        {
            $validated = $request->validate(['otp' => ['required', 'digits:6']]);
            $enteredOtp = trim((string) $validated['otp']);
            $gid = $request->session()->get('end_user_gid');
            $userId = $request->session()->get('end_user_id');
            $user = $userId ? User::find($userId) : null;

            if (! $user && $gid) {
                $user = User::query()
                    ->where(function ($query) use ($gid) {
                        $query->where('login_id', $gid)
                            ->orWhere('user_name', $gid)
                            ->orWhere('official_email', $gid);
                    })
                    ->first();
            }

            if (! $user) {
                $request->session()->forget(['end_user_gid', 'end_user_id']);
                return redirect()->route('end.user.gid')->with('error', 'Invalid GID.');
            }

            if (! $user->password_reset_otp || ! $user->password_reset_otp_expires_at) {
                return back()->withInput()->with('error', 'Invalid or expired OTP.');
            }

            if ($user->password_reset_otp_expires_at->isPast()) {
                return back()->withInput()->with('error', 'Invalid or expired OTP.');
            }

            if (! Hash::check($enteredOtp, $user->password_reset_otp)) {
                return back()->withInput()->with('error', 'Invalid or expired OTP.');
            }

            if (! $this->isActive($user)) {
                return back()->withInput()->with('error', 'Employee/GID is inactive.');
            }

            $user->password_reset_otp = null;
            $user->password_reset_otp_expires_at = null;
            $user->last_login_at = now();
            $user->save();

            auth()->login($user);
            $request->session()->forget(['end_user_gid', 'end_user_id']);
            $request->session()->regenerate();

            Cache::put(
                EnsureSingleUserSession::cacheKey((int) $user->getAuthIdentifier()),
                ['session_id' => $request->session()->getId()],
                now()->addMinutes((int) config('session.lifetime', 120))
            );

            return redirect()->route('role.dashboard');
        }

        private function sendOtp(User $user, int $otp): array
        {
            if (! $user->official_email) {
                return ['success' => false];
            }

            $mailLog = new MailLog();
            $mailLog->user_id = $user->user_id;
            $mailLog->to_address = $user->official_email;
            $mailLog->subject = 'Your EMRI Issue Tracker login OTP';
            $mailLog->body = "Your login OTP is: {$otp}. Please use it to verify your login.";
            $mailLog->status = 'pending';
            $mailLog->mailer = 'smtp';
            $mailLog->save();

            $job = new SendEndUserOtpMail($user->user_id, $mailLog->mail_log_id, $otp);

            try {
                $forceSyncProcessing = config('queue.default') === 'sync'
                    || ! Schema::hasTable('jobs')
                    || filter_var(env('DISABLE_QUEUED_EXTERNAL_CALLS', true), FILTER_VALIDATE_BOOLEAN);

                if ($forceSyncProcessing) {
                    $job->handle();
                    $mailLog->body = "Your login OTP is: {$otp}. Please use it to verify your login.";
                    $mailLog->status = 'sent';
                    $mailLog->sent_at = now();
                    $mailLog->save();
                } else {
                    dispatch($job);
                    $mailLog->body = "Your login OTP is: {$otp}. Please use it to verify your login.";
                    $mailLog->save();
                }

                return ['success' => true];
            } catch (\Throwable $exception) {
                $mailLog->status = 'failed';
                $mailLog->error_message = $exception->getMessage();
                $mailLog->save();

                return ['success' => false];
            }
        }

    private function isActive(User $user): bool
    {
        return (int) ($user->is_active ?? 0) === 1 && strtoupper((string) ($user->user_status ?? 'ACTIVE')) === 'ACTIVE';
    }

    private function hasEndUserRole(User $user): bool
    {
        return $user->hasRoleId(9)
            || $user->roles()->whereRaw('LOWER(role_name) = ?', ['end user'])->exists();
    }

    private function ensureEndUserRole(User $user): bool
    {
        $roleId = DB::table('mst_role')->where('role_id', 9)->whereRaw('LOWER(role_name) = ?', ['end user'])->value('role_id');
        if (! $roleId) {
            return false;
        }

        $mappingExists = DB::table('map_user_role')
            ->where('user_id', $user->user_id)
            ->where('role_id', $roleId)
            ->exists();

        if (! $mappingExists) {
            DB::table('map_user_role')->insert([
                'user_id' => $user->user_id,
                'role_id' => $roleId,
                'is_active' => 1,
                'created_at' => now(),
            ]);
        } else {
            DB::table('map_user_role')
                ->where('user_id', $user->user_id)
                ->where('role_id', $roleId)
                ->update(['is_active' => 1]);
        }

        return true;
    }
}
