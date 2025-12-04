<?php

namespace App\Http\Controllers\Api\V1\Alumni;

use App\Http\Controllers\Controller;
use App\Models\TracerStudy;
use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Total Alumni yang sudah mengisi Tracer Study
        $totalResponden = TracerStudy::count();

        // 2. Data Pie Chart: Sebaran Status Pekerjaan
        // Query: SELECT status_pekerjaan, COUNT(*) as total FROM tracer_studies GROUP BY status_pekerjaan
        $statusStats = TracerStudy::select('status_pekerjaan', DB::raw('count(*) as total'))
            ->groupBy('status_pekerjaan')
            ->get();

        // 3. Data Tambahan: Jumlah Lowongan Aktif
        $activeJobs = Job::where('is_active', true)->count();

        return response()->json([
            'message' => 'Dashboard Data',
            'data' => [
                'total_responden' => $totalResponden,
                'status_distribution' => $statusStats,
                'active_jobs' => $activeJobs
            ]
        ]);
    }
}
