<?php

namespace App\Http\Controllers\Api\V1\Alumni;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    // GET: Ambil detail profil user login
    public function show(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        // Load relasi alumniProfile & skills biar lengkap
        return response()->json(
            $user->load(['alumniProfile', 'skills', 'employments', 'internships'])
        );
    }

    // PUT: Update data profil (No HP, LinkedIn, Privacy, dll)
    public function update(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'linkedin_url' => 'nullable|url',
            'gender' => 'nullable|in:L,P',
            'date_of_birth' => 'nullable|date',

            // Validasi Foto
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Max 2MB

            'privacy_settings' => 'nullable|array',
        ]);

        /** @var User $user */
        $user = $request->user();

        // LOGIC UPLOAD FOTO (Menimpa file lama)
        if ($request->hasFile('avatar')) {
            // 1. Hapus foto lama jika ada (biar gak nyampah)
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // 2. Simpan foto baru
            $path = $request->file('avatar')->store('avatars', 'public');

            // 3. Simpan path ke database user
            $user->avatar = $path;
            $user->save();
        }

        // Update data profil lainnya (AlumniProfile)
        $user->alumniProfile()->updateOrCreate(
            ['user_id' => $user->id],
            collect($validated)->except(['avatar'])->toArray() // Exclude avatar dari update profil
        );

        return response()->json([
            'message' => 'Profile updated successfully',
            'data' => $user->load('alumniProfile')
        ]);
    }
}
