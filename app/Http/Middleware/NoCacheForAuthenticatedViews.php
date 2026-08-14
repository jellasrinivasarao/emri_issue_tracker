<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class NoCacheForAuthenticatedViews
{
    /**
     * Level 1: Prevent caching of protected pages
     * Put these headers on every protected PHP page to prevent browser cache restore
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (method_exists($response, 'header')) {
            // Comprehensive cache prevention
            $response->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0, private');
            $response->header('Cache-Control', 'post-check=0, pre-check=0', false);
            $response->header('Pragma', 'no-cache');
            $response->header('Expires', '0');
        }

        return $response;
    }
}
