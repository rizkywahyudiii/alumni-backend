<?php

namespace App\Http\Controllers\Api\V1\Alumni;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class DirectoryController extends Controller
{
    public function index(Request $request)
    {
        // Ambil input pencarian
        $keyword = $request->query('q');
        $year    = $request->query('year');

        // Query Dasar: Ambil User yang punya profil alumni
        $query = User::with(['alumniProfile', 'tracerStudy']) // Load tracerStudy biar tau dia kerja di mana
            ->whereHas('alumniProfile', function($q) {
                // FILTER KRUSIAL: Hanya tampilkan yang setting privasinya TRUE
                // Karena kolom JSON, sintaksnya tergantung database.
                // Untuk aman/umum, kita filter di PHP atau asumsikan default visible jika null
                // TAPI best practicenya di SQL:
                $q->whereJsonContains('privacy_settings->show_in_directory', true);
            });

        // Filter Pencarian (Nama)
        if ($keyword) {
            $query->where('name', 'like', "%{$keyword}%");
        }

        // Filter Tahun Lulus
        if ($year) {
            $query->where('tahun_lulus', $year);
        }

        // Eksekusi (Pagination 12 per halaman)
        $alumni = $query->latest()->paginate(12);

        return response()->json([
            'message' => 'Alumni Directory',
            'data'    => $alumni
        ]);
    }

    public function show($id)
    {
        // Cari user, load data lengkap termasuk skills
        // Asumsi kamu punya relasi 'skills' di model User
        $user = User::with(['alumniProfile', 'tracerStudy', 'skills', 'internships'])->find($id);

        if (!$user) {
            return response()->json(['message' => 'Alumni tidak ditemukan'], 404);
        }

        // Cek Privasi: Apakah user ini mengizinkan profilnya dilihat?
        // (Opsional: sesuaikan dengan logic database kamu, misal kolom JSON)
        $privacy = $user->alumniProfile->privacy_settings ?? [];
        $isVisible = $privacy['show_in_directory'] ?? true; // Default true atau false tergantung kebijakan

        if (!$isVisible) {
            return response()->json(['message' => 'Profil ini bersifat privat'], 403);
        }

        return response()->json([
            'message' => 'Detail Alumni',
            'data'    => $user
        ]);
    }
}
