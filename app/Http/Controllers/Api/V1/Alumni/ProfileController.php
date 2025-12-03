<?php

namespace App\Http\Controllers\Api\V1\Alumni;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

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

            // Validasi JSON Privacy Settings
            'privacy_settings' => 'nullable|array',
            'privacy_settings.show_in_directory' => 'boolean',
            'privacy_settings.show_email' => 'boolean',
            'privacy_settings.allow_contact' => 'boolean',
        ]);

        /** @var User $user */
        $user = $request->user();

        // Update atau Create jika belum ada (safety)
        $user->alumniProfile()->updateOrCreate(
            ['user_id' => $user->id], // Kunci pencarian
            $validated // Data yang diupdate
        );

        return response()->json([
            'message' => 'Profile updated successfully',
            'data' => $user->load('alumniProfile')
        ]);
    }
}
