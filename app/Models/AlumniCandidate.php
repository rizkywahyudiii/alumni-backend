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

    // tahun_lulus NULL = mahasiswa (ongoing), terisi = alumni.
    // Setiap kandidat disimpan (termasuk via import), user dengan NIM yang sama ikut disesuaikan.
    protected static function booted(): void
    {
        static::saved(function (AlumniCandidate $candidate) {
            User::where('nim', $candidate->nim)
                ->whereIn('role', ['mahasiswa', 'alumni']) // admin/dosen tidak disentuh
                ->update([
                    'tahun_lulus' => $candidate->tahun_lulus,
                    'role'        => $candidate->tahun_lulus ? 'alumni' : 'mahasiswa',
                ]);
        });
    }
}
