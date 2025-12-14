<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use App\Models\User;

// --- Resources ---
use App\Http\Resources\UserResource;

// --- Controllers: Auth ---
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Api\Auth\PasswordResetController;
use App\Http\Controllers\Auth\VerifyEmailController;

// --- Controllers: Shared / Master Data ---
use App\Http\Controllers\Api\V1\Shared\MasterDataController;

// --- Controllers: Alumni & Features ---
use App\Http\Controllers\Api\V1\Alumni\DashboardController;
use App\Http\Controllers\Api\V1\Alumni\DirectoryController;
use App\Http\Controllers\Api\V1\Alumni\EmploymentController;
use App\Http\Controllers\Api\V1\Alumni\InternshipController;
use App\Http\Controllers\Api\V1\Alumni\JobController;
use App\Http\Controllers\Api\V1\Alumni\ProfileController;
use App\Http\Controllers\Api\V1\Alumni\TracerStudyController;

// --- Controllers: Admin ---
use App\Http\Controllers\Api\V1\Admin\UserController;

/*
|--------------------------------------------------------------------------
| API Routes (System Alumni)
|--------------------------------------------------------------------------
*/

// =========================================================================
// 1. PUBLIC ROUTES (GUEST)
// =========================================================================

// --- Authentication ---
Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest')
    ->name('api.login');

Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware('guest')
    ->name('api.register');

// --- Password Reset ---
Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])
    ->middleware('guest')
    ->name('password.email');

Route::post('/reset-password', [PasswordResetController::class, 'reset'])
    ->middleware('guest')
    ->name('password.update');

// ROUTE VERIFIKASI EMAIL (WAJIB DI LUAR GRUP APAPUN)
// Frontend Access: GET http://localhost:8000/api/email/verify/{id}/{hash}...
Route::get('/email/verify/{id}/{hash}', function (Request $request, $id, $hash) {

    // 1. Cari User
    $user = User::find($id);

    // 2. Validasi User & Hash
    if (! $user || sha1($user->getEmailForVerification()) !== $hash) {
        return response()->json(['message' => 'Link tidak valid atau user tidak ditemukan.'], 403);
    }

    // 3. Validasi Signature (Expired/Tampered)
    if (! $request->hasValidSignature()) {
        return response()->json(['message' => 'Link verifikasi sudah kadaluarsa atau rusak.'], 403);
    }

    // 4. Proses Verifikasi
    if (! $user->hasVerifiedEmail()) {
        $user->markEmailAsVerified();
        event(new \Illuminate\Auth\Events\Verified($user));
    }

    return response()->json(['message' => 'Email berhasil diverifikasi!']);

})->middleware(['throttle:2,1'])
  ->name('verification.verify');

// --- Email Verification (Public Resend) ---
// Fitur ini agar user yang belum login bisa minta kirim ulang email
Route::post('/resend-verification', function (Request $request) {
    $request->validate(['email' => 'required|email']);
    $user = \App\Models\User::where('email', $request->email)->first();

    if ($user && !$user->hasVerifiedEmail()) {
        $user->sendEmailVerificationNotification();
    }
    return response()->json(['message' => 'Link verifikasi telah dikirim ke email Anda.']);
})->middleware(['guest', 'throttle:2,1'])->name('verification.resend.public');

// --- Master Data (Read Only) ---
Route::prefix('v1')->group(function () {
    Route::get('/industries', [MasterDataController::class, 'industries']);
    Route::get('/skills', [MasterDataController::class, 'skills']);
    Route::get('/companies', [MasterDataController::class, 'companies']);
});


// =========================================================================
// 2. PROTECTED ROUTES (REQ: AUTH SANCTUM)
// =========================================================================

Route::middleware(['auth:sanctum'])->group(function () {

    // --- A. Global User Routes ---
    Route::get('/user', function (Request $request) {
        return new UserResource($request->user()->load('alumniProfile'));
    });

    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('api.logout');

    // Email Verification (Authenticated User)
    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return response()->json(['message' => 'Email verifikasi berhasil dikirim ulang.']);
    })->middleware(['throttle:2,1'])->name('verification.send');


    // --- B. Main Features (Prefix: v1/alumni) ---
    Route::prefix('v1/alumni')->group(function () {

        // 1. Shared Features (All Roles: Mahasiswa, Dosen, Alumni, Admin)
        // -------------------------------------------------------------
        Route::get('/dashboard/stats', [DashboardController::class, 'index']);

        // Directory & Jobs (Read Access)
        Route::get('/directory', [DirectoryController::class, 'index']);
        Route::get('/directory/{id}', [DirectoryController::class, 'show']);
        Route::get('/jobs', [JobController::class, 'index']);
        Route::get('/jobs/{id}', [JobController::class, 'show']);

        // Profile Management (Own Profile)
        Route::get('/profile', [ProfileController::class, 'show']);
        Route::put('/profile', [ProfileController::class, 'update']);


        // 2. Job Management (Create/Delete)
        // -------------------------------------------------------------
        // Permission: Alumni, Admin, Super Admin (Mahasiswa & Dosen: View Only)
        Route::middleware(['role:alumni,admin,super_admin'])->group(function () {
            Route::post('/jobs', [JobController::class, 'store']);
            Route::delete('/jobs/{id}', [JobController::class, 'destroy']);
        });


        // 3. Alumni Specific (Tracer & Career)
        // -------------------------------------------------------------
        // Permission: Alumni Only (Super Admin for debugging)
        Route::middleware(['role:alumni,super_admin'])->group(function () {
            // Career
            Route::apiResource('employments', EmploymentController::class);
            Route::apiResource('internships', InternshipController::class);

            // Tracer Study
            Route::get('/tracer-study/me', [TracerStudyController::class, 'me']);
            Route::post('/tracer-study', [TracerStudyController::class, 'store']);
        });


        // 4. Admin & Kaprodi Area
        // -------------------------------------------------------------
        // Permission: Admin & Super Admin Only
        Route::middleware(['role:admin,super_admin'])->prefix('admin')->group(function () {

            // Reporting
            Route::get('/tracer-study/export', [TracerStudyController::class, 'export']);

            // User Management
            Route::apiResource('users', UserController::class)
                ->only(['index', 'update', 'destroy']);
        });

    });
});
