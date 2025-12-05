<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Alumni\DashboardController;

/*
|--------------------------------------------------------------------------
| RBAC Route Examples
|--------------------------------------------------------------------------
|
| File ini berisi contoh route grouping berdasarkan role.
| Copy-paste route yang sesuai ke routes/api.php
|
*/

// // ========== ROUTE UNTUK ALUMNI ==========
// Route::middleware(['auth:sanctum', 'role:alumni'])->prefix('v1/alumni')->group(function () {
//     // Route yang hanya bisa diakses Alumni
//     Route::get('/dashboard/stats', [DashboardController::class, 'index']);
//     Route::post('/jobs', [JobController::class, 'store']); // Alumni bisa posting job
//     // ... route lainnya untuk alumni
// });

// // ========== ROUTE UNTUK MAHASISWA (ON-GOING) ==========
// Route::middleware(['auth:sanctum', 'role:mahasiswa'])->prefix('v1/mahasiswa')->group(function () {
//     // Route yang hanya bisa diakses Mahasiswa
//     Route::get('/directory', [DirectoryController::class, 'index']); // Bisa lihat directory
//     Route::get('/jobs', [JobController::class, 'index']); // Bisa lihat jobs, tapi tidak bisa posting
//     // ... route lainnya untuk mahasiswa
// });

// // ========== ROUTE UNTUK DOSEN ==========
// Route::middleware(['auth:sanctum', 'role:dosen,admin'])->prefix('v1/dosen')->group(function () {
//     // Route yang bisa diakses Dosen dan Admin
//     Route::get('/analytics', [AnalyticsController::class, 'index']);
//     Route::get('/alumni-list', [AlumniController::class, 'index']);
//     Route::get('/tracer-study-stats', [TracerStudyController::class, 'stats']);
//     // ... route lainnya untuk dosen
// });

// // ========== ROUTE UNTUK KAPRODI/ADMIN ==========
// Route::middleware(['auth:sanctum', 'role:admin,super_admin'])->prefix('v1/admin')->group(function () {
//     // Route yang hanya bisa diakses Admin/Kaprodi
//     Route::get('/dashboard', [AdminDashboardController::class, 'index']);
//     Route::get('/export-alumni', [ExportController::class, 'alumni']);
//     Route::get('/reports', [ReportController::class, 'index']);
//     Route::post('/users', [UserController::class, 'store']); // Create user
//     Route::put('/users/{id}', [UserController::class, 'update']); // Update user
//     Route::delete('/users/{id}', [UserController::class, 'destroy']); // Delete user
//     // ... route lainnya untuk admin
// });

// // ========== ROUTE UNTUK MULTIPLE ROLES ==========
// Route::middleware(['auth:sanctum', 'role:alumni,mahasiswa'])->prefix('v1')->group(function () {
//     // Route yang bisa diakses Alumni dan Mahasiswa
//     Route::get('/directory', [DirectoryController::class, 'index']);
//     Route::get('/jobs', [JobController::class, 'index']);
// });

// // ========== ROUTE UNTUK SEMUA YANG SUDAH LOGIN ==========
// Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
//     // Route yang bisa diakses semua role yang sudah login
//     Route::get('/profile', [ProfileController::class, 'show']);
//     Route::put('/profile', [ProfileController::class, 'update']);
// });
