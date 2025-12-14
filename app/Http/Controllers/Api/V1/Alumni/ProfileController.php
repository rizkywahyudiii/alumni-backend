<?php

namespace App\Http\Controllers\Api\V1\Alumni;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * GET: Ambil detail profil user login
     * Mengambil data user beserta relasi ke profil alumni, skills, dll.
     */
    public function show(Request $request)
    {
        // Jangan pakai User::find(1) atau semacamnya.
        $user = $request->user();

        // Load data relasi
        $user->load(['alumniProfile']);

        return response()->json([
            'status' => 'success',
            'data' => $user
        ]);
    }

    /**
     * PUT: Update data profil LENGKAP
     * Menangani: Akun (Nama/Pass), Profile (Alamat/HP), dan Avatar
     */
    public function update(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        // 1. VALIDASI DATA
        $validated = $request->validate([
            // A. Validasi Akun Utama (Table Users)
            'name'  => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],

            // Validasi Password (Opsional)
            'current_password' => 'nullable|required_with:new_password',
            'new_password'     => 'nullable|min:8|confirmed',

            // B. Validasi Data Alumni (Table AlumniProfiles)
            'phone'         => 'nullable|string|max:20',
            'address'       => 'nullable|string|max:500',
            'linkedin_url'  => 'nullable|url',
            'gender'        => 'nullable|in:L,P',
            'date_of_birth' => 'nullable|date',
            'privacy_settings' => 'nullable|array',

            // C. Validasi Foto
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Max 2MB
        ]);

        // 2. CEK PASSWORD (Jika user ingin ganti password)
        if ($request->filled('current_password') && $request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'message' => 'Password lama tidak sesuai.',
                    'errors'  => ['current_password' => ['Password lama salah']]
                ], 422);
            }
        }

        // Gunakan Transaksi Database agar aman (Kalau satu gagal, semua batal)
        try {
            DB::beginTransaction();

            // 3. UPDATE DATA AKUN (USERS)
            $user->name  = $request->name;
            $user->email = $request->email;

            if ($request->filled('new_password')) {
                $user->password = Hash::make($request->new_password);
            }

            // 4. HANDLE UPLOAD AVATAR
            if ($request->hasFile('avatar')) {
                // Hapus foto lama fisik jika ada & bukan default
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }

                // Simpan foto baru
                $path = $request->file('avatar')->store('avatars', 'public');
                $user->avatar = $path;
            }

            $user->save(); // Simpan perubahan tabel users

            // 5. UPDATE DATA ALUMNI (ALUMNI_PROFILES)
            // Ambil hanya field yang relevan untuk tabel alumni_profiles
            $alumniData = $request->only([
                'phone',
                'address',
                'linkedin_url',
                'gender',
                'date_of_birth',
                'privacy_settings'
            ]);

            $user->alumniProfile()->updateOrCreate(
                ['user_id' => $user->id],
                $alumniData
            );

            DB::commit(); // Simpan permanen

            return response()->json([
                'message' => 'Profil berhasil diperbarui!',
                'data'    => $user->load('alumniProfile')
            ]);

        } catch (\Exception $e) {
            DB::rollBack(); // Batalkan jika error
            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan data.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
