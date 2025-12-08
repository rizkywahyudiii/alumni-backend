<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Shared\MasterDataController;
use App\Http\Controllers\Api\V1\Alumni\EmploymentController;
use App\Http\Controllers\Api\V1\Alumni\InternshipController;
use App\Http\Controllers\Api\V1\Alumni\ProfileController;
use App\Http\Controllers\Api\V1\Alumni\TracerStudyController;
use App\Http\Controllers\Api\V1\Alumni\JobController;
use App\Http\Controllers\Api\V1\Alumni\DashboardController;
use App\Http\Controllers\Api\V1\Alumni\DirectoryController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Resources\UserResource;

/*
|--------------------------------------------------------------------------
| API Routes (RBAC Implemented)
|--------------------------------------------------------------------------
*/

// --- 0. PUBLIC ROUTES (No Auth Required) ---
Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest')
    ->name('api.login');

Route::prefix('v1')->group(function () {
    Route::get('/industries', [MasterDataController::class, 'industries']);
    Route::get('/skills', [MasterDataController::class, 'skills']);
    Route::get('/companies', [MasterDataController::class, 'companies']);
});

// --- PROTECTED ROUTES (Require Valid Token) ---
Route::middleware(['auth:sanctum'])->group(function () {

    // 1. GLOBAL AUTH ROUTES (Accessible by ALL Roles)
    Route::get('/user', function (Request $request) {
        return new UserResource($request->user()->load('alumniProfile'));
    });
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('api.logout');


    // --- V1 API GROUP ---
    Route::prefix('v1/alumni')->group(function () {

        // A. SHARED FEATURES (Alumni, Dosen, Admin, Mahasiswa)
        // ---------------------------------------------------
        // Semua user login bisa lihat direktori dan detail job
        Route::get('/directory', [DirectoryController::class, 'index']);
        Route::get('/directory/{id}', [DirectoryController::class, 'show']);

        Route::get('/jobs', [JobController::class, 'index']);      // List Lowongan
        Route::get('/jobs/{id}', [JobController::class, 'show']);  // Detail Lowongan

        Route::get('/dashboard/stats', [DashboardController::class, 'index']); // Dashboard Stats (Logic filter ada di Controller)

        // Profile Sendiri (Semua user punya profile dasar)
        Route::get('/profile', [ProfileController::class, 'show']);
        Route::put('/profile', [ProfileController::class, 'update']);


        // B. ALUMNI SPECIFIC FEATURES (Role: ALUMNI Only)
        // ---------------------------------------------------
        Route::middleware(['role:alumni,super_admin'])->group(function () {
            // ^ Hapus ',super_admin' pada baris di atas jika ingin testing strict mode alumni

            // Career Management
            Route::apiResource('employments', EmploymentController::class);
            Route::apiResource('internships', InternshipController::class);

            // Tracer Study
            Route::post('/tracer-study', [TracerStudyController::class, 'store']);
            Route::get('/tracer-study/me', [TracerStudyController::class, 'me']);
        });


        // C. JOB MANAGEMENT (Role: ALUMNI, ADMIN, SUPER_ADMIN)
        // ---------------------------------------------------
        // Mahasiswa & Dosen tidak bisa posting/hapus lowongan
        Route::middleware(['role:alumni,admin,super_admin'])->group(function () {
            Route::post('/jobs', [JobController::class, 'store']);
            Route::delete('/jobs/{id}', [JobController::class, 'destroy']);
            // Note: Update job mungkin perlu ditambahkan logic kepemilikan di controller
        });

        // --- D. ADMIN/KAPRODI AREA ---
        // Pastikan user biasa tidak bisa akses ini!
        Route::middleware(['role:admin,super_admin'])->prefix('admin')->group(function () {

            // 1. Export Excel Tracer Study
            Route::get('/tracer-study/export', [TracerStudyController::class, 'export']);

            // 2. User Management (CRUD)
            // Endpoint: GET /users, PUT /users/{id}, DELETE /users/{id}
            Route::apiResource('users', \App\Http\Controllers\Api\V1\Admin\UserController::class)
                ->only(['index', 'update', 'destroy']);

        });
    });
});
