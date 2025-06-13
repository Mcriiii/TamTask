<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\LostFoundController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\ReferralController;
use App\Http\Controllers\ViolationController;
use App\Http\Controllers\CertificateController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Shared Routes 
Route::middleware(['auth', 'nocache'])->group(function () {
    // User Dashboard
    Route::get('/dashboard', [AnalyticsController::class, 'lostFoundReport'])->name('dashboard');

    // Lost and Found (User URL)
    Route::get('/lost-found', [LostFoundController::class, 'showLostandfound'])->name('lost-found.index');
    Route::post('/lost-found', [LostFoundController::class, 'store'])->name('lost-found.store');
    Route::get('/lost-found/{id}/edit', [LostFoundController::class, 'edit'])->name('lost-found.edit');
    Route::put('/lost-found/{id}', [LostFoundController::class, 'update'])->name('lost-found.update');
    Route::delete('/lost-found/{id}', [LostFoundController::class, 'destroy'])->name('lost-found.destroy');
    Route::post('/lost-found/{id}/claim', [LostFoundController::class, 'markAsClaimed'])->name('lost-found.claim');

    // Complaint Routes
    Route::get('/complaints', [ComplaintController::class, 'index'])->name('complaints.index');
    Route::post('/complaints', [ComplaintController::class, 'store'])->name('complaints.store');
    Route::get('/complaints/{id}/edit', [ComplaintController::class, 'edit'])->name('complaints.edit');
    Route::put('/complaints/{id}', [ComplaintController::class, 'update'])->name('complaints.update');
    Route::delete('/complaints/{id}', [ComplaintController::class, 'destroy'])->name('complaints.destroy');

    // Incident Routes
    Route::get('/incidents', [IncidentController::class, 'index'])->name('incidents.index');
    Route::post('/incidents/store', [IncidentController::class, 'store'])->name('incidents.store');
    Route::get('/incidents/edit/{id}', [IncidentController::class, 'edit'])->name('incidents.edit');
    Route::put('/incidents/update/{id}', [IncidentController::class, 'update'])->name('incidents.update');
    Route::delete('/incidents/delete/{id}', [IncidentController::class, 'destroy'])->name('incidents.destroy');

    // Violation Routes
    Route::get('/violations', [ViolationController::class, 'index'])->name('violations.index');
    Route::post('/violations/store', [ViolationController::class, 'store'])->name('violations.store');
    Route::get('/violations/edit/{id}', [ViolationController::class, 'edit'])->name('violations.edit');
    Route::put('/violations/update/{id}', [ViolationController::class, 'update'])->name('violations.update');
    Route::delete('/violations/delete/{id}', [ViolationController::class, 'destroy'])->name('violations.destroy');


    // Referral Routes
    Route::get('/referrals', [ReferralController::class, 'index'])->name('referrals.index');
    Route::post('/referrals/store', [ReferralController::class, 'store'])->name('referrals.store');
    Route::get('/referrals/edit/{id}', [ReferralController::class, 'edit'])->name('referrals.edit');
    Route::put('/referrals/update/{id}', [ReferralController::class, 'update'])->name('referrals.update');
    Route::delete('/referrals/delete/{id}', [ReferralController::class, 'destroy'])->name('referrals.destroy');

    // Certificate Routes
    Route::get('/certificates', [CertificateController::class, 'index'])->name('certificates.index');
    Route::post('/certificates/store', [CertificateController::class, 'store'])->name('certificates.store');
    Route::get('/certificates/view/{id}', [CertificateController::class, 'view'])->name('certificates.view');
    Route::delete('/certificates/delete/{id}', [CertificateController::class, 'destroy'])->name('certificates.destroy');
    Route::get('/certificates/edit/{id}', [CertificateController::class, 'edit'])->name('certificates.edit');
    Route::put('/certificates/update/{id}', [CertificateController::class, 'update'])->name('certificates.update');




    // Export Analytics for User (optional)
    Route::get('/dashboard/export', [AnalyticsController::class, 'exportToPdf'])->name('pdf.export');
});

// Admin Routes
Route::prefix('admin')->middleware(['auth', 'admin', 'nocache'])->group(function () {
    // Admin Dashboard
    Route::get('/dashboard', [AnalyticsController::class, 'lostFoundReport'])->name('admin.dashboard');

    // Lost and Found (Admin URL)
    Route::get('/lost-found', [LostFoundController::class, 'showLostandfound'])->name('admin.lost-found.index');
    Route::post('/lost-found', [LostFoundController::class, 'store'])->name('admin.lost-found.store');
    Route::get('/lost-found/{id}/edit', [LostFoundController::class, 'edit'])->name('admin.lost-found.edit');
    Route::put('/lost-found/{id}', [LostFoundController::class, 'update'])->name('admin.lost-found.update');
    Route::delete('/lost-found/{id}', [LostFoundController::class, 'destroy'])->name('admin.lost-found.destroy');
    Route::post('/lost-found/{id}/claim', [LostFoundController::class, 'markAsClaimed'])->name('admin.lost-found.claim');

    // Complaint Routes
    Route::get('/complaints', [ComplaintController::class, 'index'])->name('admin.complaints.index');
    Route::post('/complaints', [ComplaintController::class, 'store'])->name('admin.complaints.store');
    Route::get('/complaints/{id}/edit', [ComplaintController::class, 'edit'])->name('admin.complaints.edit');
    Route::put('/complaints/{id}', [ComplaintController::class, 'update'])->name('admin.complaints.update');
    Route::delete('/complaints/{id}', [ComplaintController::class, 'destroy'])->name('admin.complaints.destroy');

    // Incident Routes
    Route::get('/incidents', [IncidentController::class, 'index'])->name('admin.incidents.index');
    Route::post('/incidents/store', [IncidentController::class, 'store'])->name('admin.incidents.store');
    Route::get('/incidents/edit/{id}', [IncidentController::class, 'edit'])->name('admin.incidents.edit');
    Route::put('/incidents/update/{id}', [IncidentController::class, 'update'])->name('admin.incidents.update');
    Route::delete('/incidents/delete/{id}', [IncidentController::class, 'destroy'])->name('admin.incidents.destroy');

    // Violation Routes
    Route::get('/violations', [ViolationController::class, 'index'])->name('admin.violations.index');
    Route::post('/violations/store', [ViolationController::class, 'store'])->name('admin.violations.store');
    Route::get('/violations/edit/{id}', [ViolationController::class, 'edit'])->name('admin.violations.edit');
    Route::put('/violations/update/{id}', [ViolationController::class, 'update'])->name('admin.violations.update');
    Route::delete('/violations/delete/{id}', [ViolationController::class, 'destroy'])->name('admin.violations.destroy');


    // Referral Routes
    Route::get('/referrals', [ReferralController::class, 'index'])->name('admin.referrals.index');
    Route::post('/referrals/store', [ReferralController::class, 'store'])->name('admin.referrals.store');
    Route::get('/referrals/edit/{id}', [ReferralController::class, 'edit'])->name('admin.referrals.edit');
    Route::put('/referrals/update/{id}', [ReferralController::class, 'update'])->name('admin.referrals.update');
    Route::delete('/referrals/delete/{id}', [ReferralController::class, 'destroy'])->name('admin.referrals.destroy');

    // Certificate Routes
    Route::get('/certificates', [CertificateController::class, 'index'])->name('admin.certificates.index');
    Route::post('/certificates/store', [CertificateController::class, 'store'])->name('admin.certificates.store');
    Route::get('/certificates/view/{id}', [CertificateController::class, 'view'])->name('admin.certificates.view');
    Route::delete('/certificates/delete/{id}', [CertificateController::class, 'destroy'])->name('admin.certificates.destroy');
    Route::get('/certificates/edit/{id}', [CertificateController::class, 'edit'])->name('admin.certificates.edit');
    Route::put('/certificates/update/{id}', [CertificateController::class, 'update'])->name('admin.certificates.update');


    // Export Analytics for Admin
    Route::get('/dashboard/export', [AnalyticsController::class, 'exportToPdf'])->name('admin.pdf.export');

    // Manage Accounts
    Route::get('/accounts', [AccountController::class, 'accountlist'])->name('admin.accounts');
    Route::post('/accounts', [AccountController::class, 'store'])->name('admin.accounts.store');
    Route::put('/accounts/{id}', [AccountController::class, 'update'])->name('admin.accounts.update');
    Route::delete('/accounts/{id}', [AccountController::class, 'destroy'])->name('admin.accounts.destroy');


    // Activity Logs
    Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('admin.activity.logs');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'loginPost'])->name('login.post');
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/register', [AuthController::class, 'registerPost'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
