<?php

namespace App\Http\Controllers\Api\V1\Shared;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Industry;
use App\Models\Skill;
use Illuminate\Http\Request;

class MasterDataController extends Controller
{
    public function industries()
    {
        return response()->json(Industry::all());
    }

    public function skills()
    {
        // Grouping by category biar enak di frontend
        $skills = Skill::all()->groupBy('category');
        return response()->json($skills);
    }

    public function companies()
    {
        // Return verified companies only
        return response()->json(Company::where('is_verified', true)->get());
    }
}
