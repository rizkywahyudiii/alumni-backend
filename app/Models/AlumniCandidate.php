<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlumniCandidate extends Model
{
    use HasFactory;

    // 👇 TAMBAHKAN ARRAY INI
    protected $fillable = [
        'nim',
        'name',
        'date_of_birth',
        'prodi',
        'angkatan',
        'tahun_lulus',
    ];
}
