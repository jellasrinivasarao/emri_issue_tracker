<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class EnsureSingleUserSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $activeSession = Cache::get($this->cacheKey((int) Auth::id()));

            if (! $activeSession || ($activeSession['session_id'] ?? null) !== $request->session()->getId()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }
        }

        return $next($request);
    }

    public static function cacheKey(int $userId): string
    {
        return 'auth.active_session.' . $userId;
    }
}
