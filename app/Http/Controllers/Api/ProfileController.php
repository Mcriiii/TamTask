<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'first_name' => ["required", "regex:/^[a-zA-Z\s]+$/"],
            'last_name'  => ["required", "regex:/^[a-zA-Z\s]+$/"],
            'password'   => 'nullable|string|min:6',
        ]);

        $user->first_name = $validated['first_name'];
        $user->last_name = $validated['last_name'];
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }


        $user->save();

        return response()->json(['message' => 'Profile updated successfully']);
    }
}
