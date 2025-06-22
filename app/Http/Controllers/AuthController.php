<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login()
    {
        return view("auth.login");
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(route("login"))->with("success", "Logged out successfully");
    }

    public function loginPost(Request $request)
    {
        $request->validate([
            "email" => "required|email",
            "password" => "required",
        ]);

        $credentials = $request->only("email", "password");

        if (Auth::attempt($credentials)) {
            $user = Auth::user(); // Get the authenticated user

            if (!in_array($user->role, ['admin', 'user'])) {
            Auth::logout();
            return back()->with("error", "This role is not allowed to access the web portal.");
        }

            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard'); // Admin dashboard
            } else {
                return redirect()->route('dashboard'); // User dashboard
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }


    public function register()
    {
        return view("auth.register");
    }

    public function registerPost(Request $request)
    {
        $request->validate([
            "first_name" => ["required", "regex:/^[a-zA-Z\s]+$/"],
            "last_name" => ["required", "regex:/^[a-zA-Z\s]+$/"],
            "email" => "required|email|unique:users,email",
            "password" => "required|confirmed|min:6",
        ]);

        $user = new User();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role = 'user'; // Default role
        if ($user->save()) {
            return redirect(route("login"))
                ->with("success", "User created successfully. Please login.");
        }

        return redirect(route("register"))
            ->with("error", "Failed to create account");
    }
}
