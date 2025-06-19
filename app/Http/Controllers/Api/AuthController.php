<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ["required", "regex:/^[a-zA-Z\s]+$/"],
            'last_name' => ["required", "regex:/^[a-zA-Z\s]+$/"],
            'email' => [
                'required',
                'email',
                'unique:users,email',
                function ($attribute, $value, $fail) use ($request) {
                    $role = $request->input('role');
                    $localPart = explode('@', $value)[0];

                    if ($role === 'student') {
                        $matches = [];
                        $digits = preg_match_all('/\d/', $localPart, $matches);
                        if ($digits !== 9) {
                            $fail('Error: Use your student number (must contain exactly 9 digits before @).');
                        }
                    } elseif (in_array($role, ['teacher', 'sfu'])) {
                        if (preg_match('/\d/', $localPart)) {
                            $fail(ucfirst($role) . ' Error: Use an official email without numbers.');
                        }
                    }
                    // security and other roles: no restriction
                }
            ],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:student,teacher,sfu,security'],
        ]);

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

    public function teacherInfo(Request $request)
{
    $user = $request->user();

    if ($user->role !== 'teacher') {
        return response()->json([
            'status' => 'error',
            'message' => 'Unauthorized access'
        ], 403);
    }

    return response()->json([
        'status' => 'success',
        'data' => [
            'name' => $user->first_name . ' ' . $user->last_name,
            'email' => $user->email,
            'role' => $user->role
        ]
    ]);
}
}
