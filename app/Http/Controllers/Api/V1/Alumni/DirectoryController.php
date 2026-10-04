<?php

namespace App\Http\Controllers\Api\V1\Alumni;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DirectoryController extends Controller
{
    public function index(Request $request)
    {
        // Ambil input pencarian
        $keyword = $request->query('q');
        $year    = $request->query('year'); // Note: Frontend kirim 'year' untuk angkatan atau lulus?

        // Query Dasar: Ambil User active
        $query = User::with([
                'alumniProfile',
                'tracerStudy',
                // Pekerjaan terbaru dulu, untuk label karir di kartu directory
                'employments' => fn ($q) => $q->select('id', 'user_id', 'title', 'company_name', 'is_public', 'start_date')
                    ->latest('start_date'),
            ])
            ->where('status', 'active') // Pastikan hanya user aktif
            ->where('role', 'alumni')   // Directory khusus alumni
            ->where(function ($q) {
                // Profil public, ATAU belum punya profil (default tampil)
                $q->whereHas('alumniProfile', function ($subQ) {
                    $subQ->whereJsonContains('privacy_settings->show_in_directory', true);
                })
                ->orWhereDoesntHave('alumniProfile');
            });

        // Filter Pencarian (Nama/NIM/Email)
        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                  ->orWhere('nim', 'like', "%{$keyword}%")
                  ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        // Filter Tahun (Sesuaikan dengan kolom di DB kamu: 'angkatan' atau 'tahun_lulus')
        if ($year) {
            // Jika frontend filter Angkatan: ganti 'tahun_lulus' jadi 'angkatan'
            // Jika frontend filter Tahun Lulus: biarkan 'tahun_lulus'
            $query->where('angkatan', $year);
        }

        // Eksekusi (Pagination 12 per halaman)
        $alumni = $query->latest()->paginate(12);

        // Pekerjaan private: jangan kirim jabatan & nama perusahaan ke frontend
        $alumni->getCollection()->each(function ($user) {
            $user->employments->where('is_public', false)->each->makeHidden(['title', 'company_name']);
        });

        // Bungkus pakai Resource (Opsional tapi disarankan agar struktur konsisten)
        // return \App\Http\Resources\UserResource::collection($alumni);

        // Atau return JSON biasa (sesuai kode lamamu):
        return response()->json([
            'message' => 'Alumni Directory',
            // Perhatikan struktur ini, frontend harus ambil response.data.data.data
            // Jika pakai paginate(), data aslinya ada di dalam key 'data'
            'data'    => $alumni
        ]);
    }

    // ... (method show biarkan tetap sama, itu sudah bagus logic-nya)
    public function show($id)
    {
        $currentUser = Auth::user();

        $isAdmin = $currentUser && in_array($currentUser->role, ['admin', 'super_admin']);

        $user = User::with([
            'alumniProfile',
            'tracerStudy',
            // 'skills', // Pastikan relasi ini ada di Model User
            // 'internships' => function($query) use ($isAdmin) {
            //     if (!$isAdmin) $query->where('is_public', true);
            //     $query->latest();
            // },
            // 'employments' => function($query) use ($isAdmin) {
            //     if (!$isAdmin) $query->where('is_public', true);
            //     $query->latest();
            // }
        ])->find($id);

        if (!$user) {
            return response()->json(['message' => 'Alumni tidak ditemukan'], 404);
        }

        $privacy = $user->alumniProfile->privacy_settings ?? [];
        // Default true jika profil belum ada (untuk admin)
        $isVisible = $privacy['show_in_directory'] ?? true;

        $isOwner = $currentUser && $currentUser->id === $user->id;

        if (!$isVisible && !$isAdmin && !$isOwner) {
            return response()->json(['message' => 'Profil ini bersifat privat'], 403);
        }

        // Gunakan Resource jika ada, atau json biasa
        // return new \App\Http\Resources\UserResource($user);

        return response()->json([
            'message' => 'Detail Alumni',
            'data'    => $user
        ]);
    }
}
