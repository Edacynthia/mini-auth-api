<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Symfony\Component\HttpFoundation\Response;

class ApiOrJwt
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
         if ($request->header('X-API-KEY')) {
            $key = ApiKey::where('key', $request->header('X-API-KEY'))
                ->where('revoked', false)
                ->where(function ($q) {
                    $q->whereNull('expires_at')
                      ->orWhere('expires_at', '>', now());
                })
                ->first();

            if (!$key) return response(['error' => 'Invalid API key'], 401);

             try {
            JWTAuth::parseToken()->authenticate();
        } catch (\Exception $e) {
            return response(['error' => 'Unauthorized'], 401);
        }
        }
        return $next($request);
    }
}
