<?php

namespace App\Http\Controllers\Api;

use App\Models\Referral;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReferralController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_name' => ['required', 'regex:/^[\pL\s]+$/u'],
            'date_reported' => 'required|date',
            'level' => 'required',
            'date_to_see' => 'required|date',
        ]);

        do {
            $referralNo = 'REF-' . rand(1000, 9999);
        } while (Referral::where('referral_no', $referralNo)->exists());

        $referral = Referral::create([
            'referral_no'   => $referralNo,
            'student_name'  => $validated['student_name'],
            'date_reported' => $validated['date_reported'],
            'level'         => $validated['level'],
            'date_to_see'   => $validated['date_to_see'],
            'status'        => 'Pending', // Optional: or remove if not in DB
            'user_id'       => $request->user()->id,
        ]);

        return response()->json([
            'message' => 'Referral created successfully',
            'referral' => $referral
        ], 201);
    }


    public function myReferrals(Request $request)
    {
        $user = $request->user();
        $referrals = Referral::where('user_id', $user->id)->orderBy('date_reported', 'desc')->get();
        return response()->json($referrals);
    }
}
