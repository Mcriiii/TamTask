<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ApprovedEmail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ["required", "regex:/^[a-zA-Z\s]+$/"],
            'last_name' => ["required", "regex:/^[a-zA-Z\s]+$/"],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:student,teacher,sfu'],
        ]);

        if (in_array($validated['role'], ['teacher', 'sfu'])) {
            $approved = ApprovedEmail::where('email', $validated['email'])
                ->where('role', $validated['role'])
                ->first();
            if (!$approved) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Your email is not approved for registration.'
                ], 403);
            }
        }

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Registration successful',
            'user' => $user,
        ], 201);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json([
            /* 'status' => 'success',
            'token' => $token,
            'user' => $user, */
            'status' => 'success',
            'data' => [
                'user' => $user,
                'token' => $token
            ],
            'message' => 'Logged in successfully'
        ]);
    }
}
