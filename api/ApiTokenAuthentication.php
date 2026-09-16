<?php

namespace Api;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ApiTokenAuthentication
{
    public function handle(Request $request, Closure $next): Response
    {
        $plainToken = $request->bearerToken();

        if (! $plainToken) {
            return response()->json([
                'success' => false,
                'status_code' => 401,
                'message' => 'Bearer token is required.',
            ], 401);
        }

        $token = DB::table('api_access_tokens as token')
            ->join('mst_end_user_api_user as user', 'user.end_user_id', '=', 'token.end_user_id')
            ->where('token.token_hash', hash('sha256', $plainToken))
            ->where('token.is_active', 1)
            ->where('user.is_active', 1)
            ->where(function ($query) {
                $query->whereNull('token.expires_at')
                    ->orWhere('token.expires_at', '>', now());
            })
            ->first([
                'token.token_id',
                'token.end_user_id',
                'user.username',
                'user.user_name',
            ]);

        if (! $token) {
            return response()->json([
                'success' => false,
                'status_code' => 401,
                'message' => 'Invalid or expired bearer token.',
            ], 401);
        }

        DB::table('api_access_tokens')
            ->where('token_id', $token->token_id)
            ->update(['last_used_at' => now()]);

        $request->setUserResolver(fn () => (object) [
            'user_id' => null,
            'end_user_id' => $token->end_user_id,
            'login_id' => $token->username,
            'user_name' => $token->user_name,
        ]);

        return $next($request);
    }
}
