<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    /**
     * Display a listing of the user accounts.
     */
    public function accountlist()
    {
        $users = User::all(); // 🟢 Fetch all users
        return view('admin.accounts', compact('users')); // Admin account management view
    }

    /**
     * Store a newly created user account in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => ["required", "regex:/^[a-zA-Z\s]+$/"],
            'last_name' => ["required", "regex:/^[a-zA-Z\s]+$/"],
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator) // Put errors in 'addAccount' error bag
                ->withInput();
        }

        // Create new user
        User::create([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'role'       => 'user', // Default role is user
        ]);

        return redirect()->back()->with('success', 'User created successfully.');
    }

    /**
     * Update the specified user account in storage.
     */
    public function update(Request $request, $id)
    {
        $id = (int) $id;
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'first_name' => ["required", "regex:/^[a-zA-Z\s]+$/"],
            'last_name'  => ["required", "regex:/^[a-zA-Z\s]+$/"],
            'email'      => 'required|email|unique:users,email,' . $id, // Ignore current user's email
            'password'   => 'nullable|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator, 'editAccount') // Put errors in 'editAccount' error bag
                ->withInput()
                ->with('edit_user_id', $id); // To know which user was being edited
        }

        // Update user info
        $user->first_name = $request->first_name;
        $user->last_name  = $request->last_name;
        $user->email      = $request->email;
        
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        
        $user->save();

        return redirect()->back()->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user account from storage.
     */
    public function destroy($id)
    {
        $id = (int) $id;
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->back()->with('success', 'User deleted successfully.');
    }
}
