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
    /**
     * Tombol "Cek Data": cocokkan NIM + tanggal lahir, kembalikan nama untuk auto-fill.
     */
    public function check(Request $request): JsonResponse
    {
        $request->validate([
            'nim' => ['required', 'string', 'max:20'],
            'date_of_birth' => ['required', 'date'],
        ]);

        $candidate = $this->verifyCandidate($request);

        if (User::where('nim', $candidate->nim)->exists()) {
            return response()->json(['message' => 'NIM ini sudah terdaftar. Silakan login.'], 422);
        }

        return response()->json(['name' => $candidate->name]);
    }

    // Pesan sengaja disamakan agar tidak bocor mana yang salah (NIM atau tanggal lahir)
    private function verifyCandidate(Request $request): AlumniCandidate
    {
        $candidate = AlumniCandidate::where('nim', $request->nim)->first();

        if (! $candidate || $candidate->date_of_birth !== $request->date_of_birth) {
            abort(response()->json([
                'message' => 'NIM atau tanggal lahir tidak sesuai dengan data alumni.',
            ], 422));
        }

        return $candidate;
    }

    public function store(Request $request): JsonResponse
    {
        // 1. Validasi (Sama seperti sebelumnya)
        $request->validate([
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'nim' => ['required', 'string', 'max:20', 'unique:'.User::class],
            'date_of_birth' => ['required', 'date'],
        ]);

        // 2. Cek Master Data (nama diambil dari data kandidat, bukan input user)
        $candidate = $this->verifyCandidate($request);

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

        return response()->json([
            'status' => 'success',
            'message' => 'Registrasi berhasil',
            'email_verified' => $user->hasVerifiedEmail(),
        ], 201);
    }
}
