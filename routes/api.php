<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Shared\MasterDataController;
use App\Http\Controllers\Api\V1\Alumni\EmploymentController;
use App\Http\Controllers\Api\V1\Alumni\InternshipController;
use App\Http\Controllers\Api\V1\Alumni\ProfileController;

use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;


Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

// Public Routes (Bisa diakses tanpa login - opsional, tapi biasanya master data public)
Route::prefix('v1')->group(function () {
    Route::get('/industries', [MasterDataController::class, 'industries']);
    Route::get('/skills', [MasterDataController::class, 'skills']);
    Route::get('/companies', [MasterDataController::class, 'companies']);
});

// User Route (yang sudah ada dari Breeze/Sanctum)
Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    // Kita ganti return default Laravel dengan UserResource buatan kita
    return new \App\Http\Resources\UserResource($request->user()->load('alumniProfile'));
});

// Routes yang butuh Login
Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {

    // User Profile
    Route::get('/user', function (Request $request) {
        return new \App\Http\Resources\UserResource($request->user()->load('alumniProfile'));
    });

    // Alumni Features
    // Group ini bisa diakses user login.
    // Best practice: Tambahkan middleware check role 'alumni' jika perlu.
    Route::prefix('alumni')->group(function () {
        //route employment
        Route::get('/employments', [EmploymentController::class, 'index']);
        Route::post('/employments', [EmploymentController::class, 'store']);
        Route::delete('/employments/{employment}', [EmploymentController::class, 'destroy']);

        // Route Internship
        Route::get('/internships', [InternshipController::class, 'index']);
        Route::post('/internships', [InternshipController::class, 'store']);
        Route::delete('/internships/{internship}', [InternshipController::class, 'destroy']);

        // Route Profile
        Route::get('/profile', [ProfileController::class, 'show']);
        Route::put('/profile', [ProfileController::class, 'update']);
        });
});

// ===============================================

// Route KHUSUS buat testing di Postman biar gampang
Route::post('/login-token', function (\Illuminate\Http\Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    $user = \App\Models\User::where('email', $request->email)->first();

    if (! $user || ! Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['Kredensial salah.'],
        ]);
    }

    // Buat token baru
    $token = $user->createToken('testing-token')->plainTextToken;

    return response()->json(['token' => $token]);
});
