<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\PasswordResetToken;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ForgotPasswordController extends Controller
{

    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $email = trim(strtolower($request->email));
        

        $user = User::whereRaw('LOWER(email) = ?', [$email])->first();
        if (!$user) {
            
            return response()->json(['message' => 'Email not found.'], 404);
        }

        $token = rand(100000, 999999); // token instead of otp

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            ['token' => $token, 'created_at' => Carbon::now()]
        );

        try {
            Mail::raw("Your password reset token is: $token", function ($message) use ($email) {
                $message->to($email)->subject('Password Reset Token');
            });
            
        } catch (\Exception $e) {
            
            return response()->json(['message' => 'Failed to send email.'], 500);
        }

        return response()->json(['message' => 'Token sent to your email.']);
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|min:6|confirmed'
        ]);

        $record = PasswordResetToken::where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (!$record || $record->isExpired()) {
            return response()->json(['message' => 'Invalid or expired OTP.'], 400);
        }

        User::where('email', $request->email)->update([
            'password' => Hash::make($request->password),
        ]);

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json(['message' => 'Password successfully reset.']);
    }
}
