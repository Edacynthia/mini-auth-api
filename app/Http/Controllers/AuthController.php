<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    /**
     * Signup a new user
     */
    public function signup(Request $request)
    {
        // Validate input
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255',
            'password' => 'required|string|min:6'
        ]);

        // Check for duplicate email
        if (User::where('email', $request->email)->exists()) {
            return response()->json(['message' => 'Email already registered'], 400);
        }

        // Create user
        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Generate JWT token for new user
        $token = JWTAuth::fromUser($user);

        return response()->json([
            'message' => 'User created successfully',
            'user'    => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email
            ],
            'token' => $token
        ]);
    }

    /**
     * Login an existing user
     */
    public function login(Request $request)
    {
        // Validate input
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string'
        ]);

        $credentials = $request->only('email', 'password');

        // Attempt login
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        return response()->json([
            'message' => 'Login successful',
            'token'   => $token
        ]);
    }
}
