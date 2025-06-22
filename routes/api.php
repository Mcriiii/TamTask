<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\LostFoundController;
use App\Http\Controllers\Api\ViolationController;


// ✅ Add this to define API Rate Limiter
RateLimiter::for('api', function (Illuminate\Http\Request $request) {
    return Limit::perMinute(60)->by($request->ip());
});

Route::post('/send-otp', [AuthController::class, 'sendOtp']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware("auth:sanctum")->group(function(){
  //  Route::post('/list',function(){
   //     return \App\Models\User::all();
   // });
   Route::post('/teacher', [AuthController::class, 'teacherInfo']);
   Route::post('/lost-found', [LostFoundController::class, 'store']);
   Route::post('/violation', [ViolationController::class, 'store']);
});