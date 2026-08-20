<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                $hasIssueDashboardAccess = $user->menus->contains(function ($menu) {
                    return strtolower((string) ($menu->route_name ?? '')) === 'role.issue.dashboard';
                });

                return redirect()->route($hasIssueDashboardAccess ? 'role.issue.dashboard' : 'role.dashboard');
            }
        }

        return $next($request);
    }
}
