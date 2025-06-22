<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\UserOtp;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;


class AuthController extends Controller
{
    public function sendOtp(Request $request)
    {

        UserOtp::where('expires_at', '<', now())->delete();

        $request->validate(['email' => 'required|email']);

        $otp = rand(100000, 999999);

        UserOtp::updateOrCreate(
            ['email' => $request->email],
            ['otp' => $otp, 'expires_at' => now()->addMinutes(5)]
        );

        Mail::raw("Your OTP is: $otp", function ($message) use ($request) {
            $message->to($request->email)->subject("Your OTP Code");
        });

        return response()->json(['message' => 'OTP sent to your email.']);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required'
        ]);

        $otpRecord = UserOtp::where('email', $request->email)
            ->where('otp', $request->otp)
            ->where('expires_at', '>', now())
            ->first();

        if ($otpRecord) {
            return response()->json(['verified' => true]);
        }

        return response()->json(['verified' => false, 'message' => 'Invalid or expired OTP.'], 400);
    }

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
                        //Student: exactly 9 digits
                        if (!preg_match('/^\d{9}$/', $localPart)) {
                            $fail('Student email must contain exactly 9 digits and nothing else.');
                        }
                    } elseif (in_array($role, ['teacher', 'sfu'])) {
                        //Teacher/SFU: only letters and periods
                        if (!preg_match('/^[a-zA-Z._-]+$/', $localPart)) {
                            $fail(ucfirst($role) . ' email must contain only letters and periods (no numbers).');
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
            'user' => $user->only(['id', 'first_name', 'last_name', 'email', 'role']),
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
