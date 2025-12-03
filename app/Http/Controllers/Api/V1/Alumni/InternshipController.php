<?php

namespace App\Http\Controllers\Api\V1\Alumni;

use App\Http\Controllers\Controller;
use App\Models\Internship;
use Illuminate\Http\Request;
use App\Models\User;

class InternshipController extends Controller
{
    public function index(Request $request)
    {
        /** @var User $user */
        $user = $request->user();
        return response()->json($user->internships()->orderBy('start_date', 'desc')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_name' => 'required|string',
            'company_id'   => 'nullable|exists:companies,id',
            'title'        => 'required|string', // Posisi magang
            'start_date'   => 'required|date',
            'end_date'     => 'nullable|date|after_or_equal:start_date',
            'description'  => 'nullable|string',
            'is_public'    => 'boolean'
        ]);

        /** @var User $user */
        $user = $request->user();
        $internship = $user->internships()->create($validated);

        return response()->json(['message' => 'Internship added', 'data' => $internship], 201);
    }

    public function update(Request $request, Internship $internship)
    {
        if ($internship->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'company_name' => 'required|string',
            'company_id'   => 'nullable|exists:companies,id',
            'title'        => 'required|string',
            'start_date'   => 'required|date',
            'end_date'     => 'nullable|date|after_or_equal:start_date',
            'description'  => 'nullable|string',
            'is_public'    => 'boolean'
        ]);

        $internship->update($validated);

        return response()->json(['message' => 'Internship updated', 'data' => $internship]);
    }

    public function destroy(Request $request, Internship $internship)
    {
        if ($internship->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $internship->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
