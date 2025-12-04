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
use App\Http\Resources\UserResource;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// 1. Route User (Standard Laravel/Breeze)
// Ini dipanggil Frontend setelah login untuk ambil data user
Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return new UserResource($request->user()->load('alumniProfile'));
});

// 2. Public Routes (Master Data)
Route::prefix('v1')->group(function () {
    Route::get('/industries', [MasterDataController::class, 'industries']);
    Route::get('/skills', [MasterDataController::class, 'skills']);
    Route::get('/companies', [MasterDataController::class, 'companies']);
});

// 3. Protected Routes (Butuh Login)
Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {

    // Group Alumni
    Route::prefix('alumni')->group(function () {

        // Dashboard
        Route::get('/dashboard/stats', [DashboardController::class, 'index']);

        // Employment
        Route::get('/employments', [EmploymentController::class, 'index']);
        Route::post('/employments', [EmploymentController::class, 'store']);
        Route::put('/employments/{employment}', [EmploymentController::class, 'update']);
        Route::delete('/employments/{employment}', [EmploymentController::class, 'destroy']);

        // Internship
        Route::get('/internships', [InternshipController::class, 'index']);
        Route::post('/internships', [InternshipController::class, 'store']);
        Route::put('/internships/{internship}', [InternshipController::class, 'update']);
        Route::delete('/internships/{internship}', [InternshipController::class, 'destroy']);

        // Profile (Update Data Diri)
        Route::get('/profile', [ProfileController::class, 'show']);
        Route::put('/profile', [ProfileController::class, 'update']);

        // Route Tracer Study
        Route::post('/tracer-study', [TracerStudyController::class, 'store']); // Simpan/Update
        Route::get('/tracer-study/me', [TracerStudyController::class, 'me']);  // Cek data sendiri

        // === ROUTE JOB PORTAL ===
        Route::get('/jobs', [JobController::class, 'index']);      // Lihat semua
        Route::post('/jobs', [JobController::class, 'store']);     // Posting baru
        Route::get('/jobs/{id}', [JobController::class, 'show']);  // Lihat detail
        Route::delete('/jobs/{id}', [JobController::class, 'destroy']); // Hapus

        Route::get('/directory', [DirectoryController::class, 'index']); // Direktori Alumni
        Route::get('/directory/{id}', [DirectoryController::class, 'show']); // Detail Alumni
    });

});
