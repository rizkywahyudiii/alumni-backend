<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class PasswordResetController extends Controller
{
    /**
     * 1. Kirim Link Reset ke Email (Forgot Password)
     */
    public function sendResetLink(Request $request)
    {
        // ✅ UPDATE VALIDASI DISINI
        // Tambahkan 'exists:users,email' untuk memastikan email terdaftar
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'Maaf, email tidak terdaftar di sistem kami. Cek kembali email Anda'
        ]);

        // Kirim link reset password
        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json(['status' => 'success', 'message' => __($status)]);
        }

        // Fallback error (jarang terjadi jika validasi exists sudah lolos)
        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    }

    /**
     * 2. Proses Reset Password Baru
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email|exists:users,email', // Validasi juga di sini
            'password' => 'required|min:8|confirmed',
        ]);

        // Attempt to reset the password
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['status' => 'success', 'message' => __($status)]);
        }

        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    }
}
