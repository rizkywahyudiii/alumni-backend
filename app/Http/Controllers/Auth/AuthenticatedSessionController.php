<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */

    public function store(Request $request): JsonResponse
    {
        // 1. Validasi Input Biasa
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 2. Cek Credential Manual (Tanpa Session/Cookie)
        // Auth::attempt akan mengecek email & password ke database
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Email atau password salah.'
            ], 401);
        }

        // 3. Ambil User
        $user = User::where('email', $request->email)->firstOrFail();

        // User lama yang belum verifikasi ikut di-bypass
        if (config('app.bypass_email_verification') && ! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
        }

        // 4. Hapus Token Lama (Opsional: Agar 1 user cuma punya 1 token aktif)
        // $user->tokens()->delete();

        // 5. Buat Token Baru
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login success',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): JsonResponse
    {
        // Hapus token user saat ini (current access token)
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout success'
        ]);
    }
}
