<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckMenuAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $routeName
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $routeName)
    {
        $user = $request->user();

        if (! $user || ! method_exists($user, 'hasMenuAccess')) {
            abort(403);
        }

        if (! $user->hasMenuAccess($routeName)) {
            abort(403);
        }

        return $next($request);
    }
}
