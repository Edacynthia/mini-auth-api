<?php

namespace App\Http\Controllers;

use App\Models\ApiKey;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApiKeyController extends Controller
{
    public function create(Request $request)
    {
        $apiKey = ApiKey::create([
            'service_name' => $request->service_name,
            'key' => Str::random(40),
            'expires_at' => $request->expires_at,
        ]);

        return ['api_key' => $apiKey->key];
    }
}
