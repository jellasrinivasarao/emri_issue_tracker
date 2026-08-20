<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Middleware\EnsureSingleUserSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();
        $activeSessionKey = EnsureSingleUserSession::cacheKey((int) $user->getAuthIdentifier());
        $activeSession = Cache::get($activeSessionKey);

        if ($activeSession && ($activeSession['session_id'] ?? null) !== $request->session()->getId() && ! $request->boolean('force_login')) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            $request->session()->flash('single_session_conflict', true);

            throw ValidationException::withMessages([
                'login_id' => 'This user is already logged in on another device. Log out the previous session or continue with the button below.',
            ]);
        }

        Cache::put(
            $activeSessionKey,
            ['session_id' => $request->session()->getId()],
            now()->addMinutes((int) config('session.lifetime', 120))
        );

        $hasIssueDashboardAccess = $user->menus->contains(function ($menu) {
            return strtolower((string) ($menu->route_name ?? '')) === 'role.issue.dashboard';
        });
        $redirectRoute = $hasIssueDashboardAccess
            ? route('role.issue.dashboard')
            : route('role.dashboard');

        if (is_null($user->password_changed_at)) {
            // Ensure we have a sensible intended URL after password change
            $request->session()->put('url.intended', $redirectRoute);
            return redirect()->route('password.force.change');
        }

        $request->session()->forget('url.intended');
        return redirect()->to($redirectRoute);
    }

    /**
     * Destroy an authenticated session.
     * Level 3: Clear all session data and prevent caching on logout
     */
    public function destroy(Request $request): RedirectResponse
    {
        $userId = Auth::id();

        if ($userId) {
            Cache::forget(EnsureSingleUserSession::cacheKey((int) $userId));
        }

        Auth::guard('web')->logout();

        // Level 3: Clear all session variables and cookies
        $request->session()->invalidate();
        $request->session()->flush();
        $request->session()->regenerateToken();

        // Redirect to login
        $response = redirect()->route('login');
        
        // Add cache-preventing headers
        $response->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, private');
        $response->header('Pragma', 'no-cache');
        $response->header('Expires', '0');
        
        return $response;
    }
}
