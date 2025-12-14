<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AlumniCandidateSeeder extends Seeder
{
    public function run()
    {
        // Masukkan data diri kamu sendiri buat ngetes
        DB::table('alumni_candidates')->insert([
            [
                'nim' => '4233250024', // Ganti dengan NIM kamu
                'name' => 'Rizky Wahyudi',
                'date_of_birth' => '2003-10-23', // Format YYYY-MM-DD (Kunci Rahasia)
                'prodi' => 'Ilmu Komputer',
                'angkatan' => 2023,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Tambah data lain jika perlu
        ]);
    }
}
