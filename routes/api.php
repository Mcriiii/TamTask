<?php


use App\Models\Incident;
use App\Models\Referral;
use App\Models\Complaint;
use App\Models\LostFound;
use App\Models\Violation;
use App\Models\Certificate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\IncidentController;
use App\Http\Controllers\Api\ReferralController;
use App\Http\Controllers\Api\ComplaintController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\LostFoundController;
use App\Http\Controllers\Api\ViolationController;
use App\Http\Controllers\Api\CertificateController;
use App\Http\Controllers\Api\ForgotPasswordController;



// ✅ Add this to define API Rate Limiter
RateLimiter::for('api', function (Illuminate\Http\Request $request) {
  return Limit::perMinute(60)->by($request->ip());
});

Route::post('/send-otp', [AuthController::class, 'sendOtp']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password/send-otp', [ForgotPasswordController::class, 'sendOtp']);
Route::post('/reset-password/reset', [ForgotPasswordController::class, 'resetPassword']);

Route::middleware("auth:sanctum")->group(function () {
  Route::get('/teacher', [AuthController::class, 'teacherInfo']);
  Route::get('/security-info', [AuthController::class, 'securityInfo']);
  Route::get('/student', [AuthController::class, 'studentInfo']);
  Route::get('/sfu-info', [AuthController::class, 'sfuInfo']);
  Route::post('/lost-found', [LostFoundController::class, 'store']);
  Route::post('/violation', [ViolationController::class, 'store']);
  Route::post('/complaints', [ComplaintController::class, 'store']);
  Route::post('/incident', [IncidentController::class, 'store']);
  Route::get('/report-counts', [ReportController::class, 'getReportCounts']);
  Route::get('/dashboard-counts', [DashboardController::class, 'getCounts']);
  Route::put('/profile', [ProfileController::class, 'update']);
  Route::post('/certificates', [CertificateController::class, 'store']);
  Route::post('/certificates/{id}/upload', [CertificateController::class, 'uploadReceipt']);
  Route::get('/certificates/mine', [CertificateController::class, 'myCertificates']);
  Route::get('/violations/mine', [ViolationController::class, 'myViolations']);
  Route::get('/lost-found/mine', [LostFoundController::class, 'myReports']);
  Route::get('/complaints/mine', [ComplaintController::class, 'myComplaints']);
  Route::post('/referral', [ReferralController::class, 'store']);
  Route::get('/referrals/mine', [ReferralController::class, 'myReferrals']);
  Route::get('/incidents/mine', [IncidentController::class, 'myIncidents']);
  Route::get('/notifications', [DashboardController::class, 'getNotifications']);

});


