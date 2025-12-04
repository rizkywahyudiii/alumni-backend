<?php

namespace App\Http\Controllers\Api\V1\Alumni; 

use App\Http\Controllers\Controller; // biar kenal class Controller utama
use App\Models\TracerStudy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TracerStudyController extends Controller
{
    /**
     * Simpan atau Update data Tracer Study user yang sedang login
     */
    public function store(Request $request)
    {
        // 1. Sanitasi Input: Ubah string kosong "" menjadi NULL
        // Ini penting agar validasi 'numeric' tidak error saat field kosong
        $data = $request->all();
        foreach ($data as $key => $value) {
            if ($value === '') {
                $data[$key] = null;
            }
        }

        // 2. Ganti request data dengan data yang sudah disanitasi
        $request->merge($data);

        // 3. Validasi Dinamis
        $validator = Validator::make($request->all(), [
            'tahun_lulus'      => 'required|digits:4|integer|min:2000|max:'.(date('Y')+1),
            'status_pekerjaan' => 'required|in:Bekerja,Wirausaha,Lanjut Studi,Mencari Kerja,Tidak Bekerja',

            // Field di bawah ini WAJIB diisi HANYA JIKA statusnya Bekerja/Wirausaha
            'nama_instansi'    => [
                Rule::requiredIf(fn () => in_array($request->status_pekerjaan, ['Bekerja', 'Wirausaha'])),
                'nullable', 'string', 'max:255'
            ],
            'jabatan'          => [
                Rule::requiredIf(fn () => in_array($request->status_pekerjaan, ['Bekerja', 'Wirausaha'])),
                'nullable', 'string', 'max:255'
            ],
            'pendapatan'       => [
                Rule::requiredIf(fn () => in_array($request->status_pekerjaan, ['Bekerja', 'Wirausaha'])),
                'nullable', 'numeric' // Sekarang aman karena "" sudah jadi null
            ],
            'relevansi_studi'  => 'nullable|integer|min:1|max:5',
        ]);

        if ($validator->fails()) {
            // Kembalikan pesan error asli dari Laravel
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // 4. Simpan Data
        $tracer = TracerStudy::updateOrCreate(
            ['user_id' => Auth::id()],
            [
                'tahun_lulus'      => $request->tahun_lulus,
                'status_pekerjaan' => $request->status_pekerjaan,
                'nama_instansi'    => $request->nama_instansi,
                'jabatan'          => $request->jabatan,
                'jenis_instansi'   => $request->jenis_instansi,
                'pendapatan'       => $request->pendapatan,
                'relevansi_studi'  => $request->relevansi_studi,
            ]
        );

        return response()->json([
            'message' => 'Data Tracer Study berhasil disimpan!',
            'data'    => $tracer
        ], 200);
    }

    /**
     * Ambil data Tracer Study user yang sedang login (untuk ditampilkan di form)
     */
    public function me()
    {
        $tracer = TracerStudy::where('user_id', Auth::id())->first();

        if (!$tracer) {
            return response()->json(['message' => 'Belum mengisi tracer study', 'data' => null], 200);
        }

        return response()->json(['message' => 'Data ditemukan', 'data' => $tracer], 200);
    }
}
