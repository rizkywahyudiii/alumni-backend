<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();
        $password = Hash::make('password'); // Default password for all

        // 1. Super Admin (IT Support / Developer)
        DB::table('users')->insert([
            'name' => 'Super Admin',
            'email' => 'admin@alumni.id',
            'password' => $password,
            'role' => 'super_admin',
            'status' => 'active',
            'email_verified_at' => $now,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 2. Kaprodi (Admin Prodi)
        DB::table('users')->insert([
            'name' => 'Kaprodi Ilmu Komputer',
            'email' => 'kaprodi@alumni.id',
            'password' => $password,
            'role' => 'admin',
            'status' => 'active',
            'email_verified_at' => $now,
            'nip' => '198001012000121001',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 3. Dosen
        DB::table('users')->insert([
            'name' => 'Dosen Web Modern',
            'email' => 'dosen@alumni.id',
            'password' => $password,
            'role' => 'dosen',
            'status' => 'active',
            'email_verified_at' => $now,
            'nip' => '198502022010121002',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 4. Mahasiswa (On-going)
        DB::table('users')->insert([
            'name' => 'Mahasiswa Semester Akhir',
            'email' => 'mhs@alumni.id',
            'password' => $password,
            'role' => 'mahasiswa',
            'status' => 'active',
            'email_verified_at' => $now,
            'nim' => '210001001',
            'angkatan' => 2021,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // 5. Alumni (Lulusan) - Contoh Data Lengkap
        $alumniId = DB::table('users')->insertGetId([
            'name' => 'Andi Sanjaya',
            'email' => 'andi@gmail.com', // Email pribadi
            'password' => $password,
            'role' => 'alumni',
            'status' => 'graduated',
            'email_verified_at' => $now,
            'nim' => '170001001',
            'angkatan' => 2018,
            'tahun_lulus' => 2022,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Create Profile for Alumni
        DB::table('alumni_profiles')->insert([
            'user_id' => $alumniId,
            'phone' => '081234567890',
            'gender' => 'L',
            'linkedin_url' => 'https://linkedin.com/in/andialumni',
            'privacy_settings' => json_encode([
                'show_in_directory' => true,
                'allow_contact' => true,
                'show_email' => false
            ]),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Optional: Insert dummy employment for this alumni
        // (Biar dashboard gak kosong pas pertama kali buka)
        DB::table('employments')->insert([
            'user_id' => $alumniId,
            'company_name' => 'GoTo Group (Gojek Tokopedia)', // Match with master data name
            'title' => 'Senior Backend Engineer',
            'employment_type' => 'full_time',
            'start_date' => '2022-01-01',
            'salary_range' => '10-20jt',
            'description' => 'Developing high scale backend services.',
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
