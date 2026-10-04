<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AlumniProfile;
use App\Models\AlumniCandidate;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        // 1. Validasi (Sama seperti sebelumnya)
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'nim' => ['required', 'string', 'max:20', 'unique:'.User::class],
            'date_of_birth' => ['required', 'date'],
        ]);

        // 2. Cek Master Data
        $candidate = AlumniCandidate::where('nim', $request->nim)->first();

        if (!$candidate) {
            return response()->json([
                'message' => 'NIM tidak ditemukan dalam Data Alumni.',
                'errors' => ['nim' => ['NIM tidak terdaftar.']]
            ], 422);
        }

        if ($candidate->date_of_birth !== $request->date_of_birth) {
            return response()->json([
                'message' => 'Verifikasi gagal. Tanggal lahir salah.',
                'errors' => ['date_of_birth' => ['Tanggal lahir tidak cocok.']]
            ], 422);
        }

        // 🔥 LOGIC BARU: Tentukan Role & Tahun Lulus
        // Jika tahun_lulus ada isinya -> Alumni
        // Jika tahun_lulus NULL -> Mahasiswa (Ongoing)
        $determinedRole = $candidate->tahun_lulus ? 'alumni' : 'mahasiswa';

        // 3. Create User
        $user = User::create([
            'name' => $candidate->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),

            // Pakai variabel role yang sudah ditentukan
            'role' => $determinedRole,

            'status' => 'active',
            'nim' => $candidate->nim,
            'angkatan' => $candidate->angkatan,
            'tahun_lulus' => $candidate->tahun_lulus, // Simpan tahun lulus (bisa null)
        ]);

        // 4. Create Profile
        AlumniProfile::create([
            'user_id' => $user->id,
            'date_of_birth' => $candidate->date_of_birth,
            'privacy_settings' => [
                'show_in_directory' => true,
                'allow_contact' => false
            ]
        ]);

        if (config('app.bypass_email_verification')) {
            $user->markEmailAsVerified();
        } else {
            event(new Registered($user));
        }

        Auth::login($user);

        return response()->json(['status' => 'success', 'message' => 'Registrasi berhasil'], 201);
    }
}
