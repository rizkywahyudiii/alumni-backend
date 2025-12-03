<?php

namespace App\Http\Controllers\Api\V1\Alumni;

use App\Http\Controllers\Controller;
use App\Models\Employment;
use App\Models\User;
use Illuminate\Http\Request;

class EmploymentController extends Controller
{
    public function index(Request $request)
    {
        // Kita paksa PHP tahu bahwa $user adalah model User kita
        /** @var User $user */
        $user = $request->user();

        // Sekarang method employments() pasti terbaca
        $employments = $user->employments()->orderBy('start_date', 'desc')->get();

        return response()->json($employments);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string',
            'company_id' => 'nullable|exists:companies,id',
            'title' => 'required|string',
            'employment_type' => 'required|in:full_time,part_time,freelance,contract,entrepreneur',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'salary_range' => 'nullable|string',
            'description' => 'nullable|string'
        ]);

        /** @var User $user */
        $user = $request->user();

        // Gunakan variabel $user yang sudah didefinisikan
        $employment = $user->employments()->create($validated);

        return response()->json([
            'message' => 'Employment added successfully',
            'data' => $employment
        ], 201);
    }

    // PUT: Update pekerjaan
    public function update(Request $request, Employment $employment)
    {
        // 1. Pastikan yang edit adalah pemilik data
        if ($employment->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // 2. Validasi input (sama dengan store)
        $validated = $request->validate([
            'company_name' => 'required|string',
            'company_id' => 'nullable|exists:companies,id',
            'title' => 'required|string',
            'employment_type' => 'required|in:full_time,part_time,freelance,contract,entrepreneur',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'salary_range' => 'nullable|string',
            'description' => 'nullable|string',
            'is_public' => 'boolean' // Opsional, defaultnya true dari database kalau kosong
        ]);

        // 3. Update database
        $employment->update($validated);

        return response()->json([
            'message' => 'Employment updated successfully',
            'data' => $employment
        ]);
    }

    public function destroy(Request $request, Employment $employment)
    {
         if ($employment->user_id !== $request->user()->id) {
             return response()->json(['message' => 'Unauthorized'], 403);
         }
         $employment->delete();
         return response()->json(['message' => 'Deleted successfully']);
    }
}
