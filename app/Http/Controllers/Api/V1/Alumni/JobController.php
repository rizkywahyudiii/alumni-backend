<?php

namespace App\Http\Controllers\Api\V1\Alumni;

use App\Http\Controllers\Controller;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class JobController extends Controller
{
    /**
     * Tampilkan semua lowongan kerja (Terbaru dlu)
     */
    public function index()
    {
        // Ambil job yang aktif saja, urutkan dari yang terbaru
        // with('user') gunanya biar kita tau siapa yang posting (Eager Loading)
        $jobs = Job::with('user:id,name,email')
                    ->where('is_active', true)
                    ->latest()
                    ->get();

        return response()->json([
            'message' => 'List lowongan kerja',
            'data'    => $jobs
        ]);
    }

    /**
     * Simpan lowongan baru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'       => 'required|string|max:255',
            'company'     => 'required|string|max:255',
            'location'    => 'required|string|max:255',
            'description' => 'required|string',
            'job_type'    => 'required|string', // Full-time, dll
            'application_url' => 'nullable|string|max:255',
            'closing_date' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Simpan dengan mengaitkan ke user yang sedang login
        $job = Job::create([
            'user_id'     => Auth::id(), // Kita isi manual ID user yang login
            'title'       => $request->title,
            'company'     => $request->company,
            'location'    => $request->location,
            'salary_range'=> $request->salary_range,
            'job_type'    => $request->job_type,
            'description' => $request->description,
            'application_url' => $request->application_url,
            'closing_date'=> $request->closing_date,
            'is_active'   => true,
        ]);

        return response()->json([
            'message' => 'Lowongan berhasil diposting!',
            'data'    => $job
        ], 201);
    }

    /**
     * Tampilkan detail 1 lowongan
     */
    public function show($id)
    {
        $job = Job::with('user:id,name')->find($id);

        if (!$job) {
            return response()->json(['message' => 'Lowongan tidak ditemukan'], 404);
        }

        return response()->json(['data' => $job]);
    }

    /**
     * Hapus Lowongan (Hanya pemilik yang boleh hapus)
     */
    public function destroy($id)
    {
        $job = Job::find($id);

        if (!$job) {
            return response()->json(['message' => 'Lowongan tidak ditemukan'], 404);
        }

        // Cek Authorization: Apakah yang login adalah pemilik postingan ini?
        if ($job->user_id !== Auth::id()) {
            return response()->json(['message' => 'Anda tidak berhak menghapus postingan ini'], 403);
        }

        $job->delete();

        return response()->json(['message' => 'Lowongan berhasil dihapus']);
    }
}
