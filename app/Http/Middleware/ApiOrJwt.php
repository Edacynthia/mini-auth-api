<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response;

class ApiOrJwt
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
{
    // JWT check
    if ($request->bearerToken()) {
        try {
            JWTAuth::parseToken()->authenticate();
            $request->merge(['auth_type' => 'user']);
            return $next($request);
        } catch (\Exception $e) {
            return response()->json(['error'=>'Invalid JWT'], 401);
        }
    }

    // API Key check
    $apiKeyHeader = $request->header('X-API-KEY');

    if ($apiKeyHeader) {
        $hashed = hash('sha256', $apiKeyHeader);
        $key = ApiKey::where('key', $hashed)->first();

        if (!$key) {
            return response()->json(['error'=>'Invalid API Key'], 401);
        }

        // Revocation check (highest priority)
        if ($key->revoked) {
            return response()->json([
                'error' => 'API key is revoked',
                'service_name' => $key->service_name,
                'revoked' => true,
                'expires_at' => $key->expires_at
            ], 401);
        }

        // Expiration check
        if ($key->expires_at && now()->greaterThan($key->expires_at)) {
            return response()->json([
                'error' => 'API key has expired',
                'service_name' => $key->service_name,
                'revoked' => false,
                'expires_at' => $key->expires_at
            ], 401);
        }

        // Key is valid
        $request->merge(['auth_type' => 'service', 'service_name' => $key->service_name]);
        return $next($request);
    }

    // No auth
    return response()->json(['error'=>'Unauthorized','reason'=>'No JWT or API key provided'], 401);
}
}