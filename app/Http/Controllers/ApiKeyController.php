<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ApiKey;
use Illuminate\Support\Str;

class ApiKeyController extends Controller
{
    /**
     * Create a new API key
     */
    public function create(Request $request)
    {
        $request->validate([
            'service_name' => 'required|string|max:255',
            'expires_at'   => 'nullable|date'
        ]);

        $plainKey = Str::random(40);

        $apiKey = ApiKey::create([
            'service_name' => $request->service_name,           // MATCH DB
            'key'          => hash('sha256', $plainKey),       // hashed key
            'expires_at'   => $request->expires_at ?? now()->addDays(30),
            'revoked'      => false
        ]);

        return response()->json([
            'api_key'      => $plainKey,                       // show once
            'id'           => $apiKey->id,
            'expires_at'   => $apiKey->expires_at->toDateTimeString()
        ]);
    }

    /**
     * Revoke an API key
     */
    public function revoke(string $id)
    {
        $key = ApiKey::findOrFail($id);

        if ($key->revoked) {
            return response()->json(['message' => 'API key already revoked'], 400);
        }

        $key->update(['revoked' => true]);

        return response()->json(['message' => 'API Key revoked']);
    }
}
