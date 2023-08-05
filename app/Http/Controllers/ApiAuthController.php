<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class ApiAuthController extends Controller
{
    public function register(Request $request)
    {
        // Validate posted fields
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'role' => ['integer', 'exists:roles,id'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Create User
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Create access token
        $token = $user->createToken('authtoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function login(Request $request)
    {
        // Validate posted fields
        $fields = $request->validate([
            'email' => 'required|string|exists:users,email',
            'password' => 'required|string'
        ]);

        // Check email
        $user = User::where('email', $fields['email'])->first();

        // Check password
        if(!Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'Password incorrect'
            ], 401);
        }

        // Create token
        $token = $user->createToken('authtoken')->plainTextToken;

        $response = [
            'user' => $user,
            'token' => $token
        ];

        return response($response, 201);
    }

    public function logout(Request $request)
    {
        // Delete current token - I'd use this for a password reset
        //auth()->user()->currentAccessToken()->delete();

        // Delete all tokens
        auth()->user()->tokens()->delete();

        return [
            'message' => 'Logged out'
        ];
    }
}
