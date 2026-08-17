<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

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

        // Keep the first authenticated page stable and role-dashboard driven.
        $user = $request->user();
        $redirectRoute = route('role.dashboard');

        if (is_null($user->password_changed_at)) {
            // Ensure we have a sensible intended URL after password change
            $request->session()->put('url.intended', $redirectRoute);
            return redirect()->route('password.force.change');
        }

        $request->session()->put('url.intended', $redirectRoute);
        return redirect()->intended($redirectRoute);
    }

    /**
     * Destroy an authenticated session.
     * Level 3: Clear all session data and prevent caching on logout
     */
    public function destroy(Request $request): RedirectResponse
    {
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
