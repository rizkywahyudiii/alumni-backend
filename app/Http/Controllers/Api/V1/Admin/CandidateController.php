<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AlumniCandidate;
use App\Imports\AlumniCandidateImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AlumniCandidateTemplate;
use Illuminate\Support\Facades\Storage;

class CandidateController extends Controller
{
    // 1. List Data (Untuk tabel di frontend)
    public function index(Request $request)
    {
        $query = AlumniCandidate::query();

        if ($request->q) {
            $query->where('name', 'like', "%{$request->q}%")
                  ->orWhere('nim', 'like', "%{$request->q}%");
        }

        $candidates = $query->latest()->paginate(10);

        return response()->json([
            'status' => 'success',
            'data' => $candidates
        ]);
    }

    // 2. Import Data
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls|max:2048'
        ]);

        try {
            Excel::import(new AlumniCandidateImport, $request->file('file'));

            return response()->json([
                'status' => 'success',
                'message' => 'Data alumni berhasil diimport!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal import: ' . $e->getMessage()
            ], 500);
        }
    }

    // 3. Update Download Template
    public function downloadTemplate()
    {
        return Excel::download(new AlumniCandidateTemplate, 'template_master_alumni.xlsx');
    }
}
