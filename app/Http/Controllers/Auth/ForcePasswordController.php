<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class ForcePasswordController extends Controller
{
    /**
     * Show the forced change-password form for first-time users.
     */
    public function show()
    {
        return view('auth.force-change-password', ['withoutSidebar' => true]);
    }

    /**
     * Update the user's password without requiring the current password.
     */
    public function update(Request $request)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = $request->user();
        $user->password_hash = Hash::make($request->password);
        $user->password_changed_at = now();
        $user->save();

        // Pull intended URL (set during login) so we can redirect after showing message.
        // The login controller stores the role-dashboard target as the stable first-page route.
        $redirectTo = session()->pull('url.intended', route('role.dashboard'));

        return view('auth.force-change-password', ['withoutSidebar' => true, 'redirectTo' => $redirectTo])
            ->with('status', 'Password changed successfully');
    }
}
