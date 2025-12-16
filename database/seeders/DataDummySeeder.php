<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Job;
use Carbon\Carbon;
use App\Models\TracerStudy;
use App\Models\AlumniProfile; // Pastikan model ini ada, atau pakai relasi user->alumniProfile()

class DataDummySeeder extends Seeder
{
    public function run(): void
    {
        // Gunakan Faker bahasa Indonesia biar datanya riil
        $faker = \Faker\Factory::create('id_ID');
        $now = Carbon::now();

        $this->command->info('🌱 Mulai menanam data dummy...');

        // // 1. Buat 1 Akun Utama buat Kamu Login (Biar ga capek register)
        // $me = User::firstOrCreate(
        //     ['email' => 'admin@alumni.com'],
        //     [
        //         'name' => 'Super Admin',
        //         'password' => Hash::make('password'), // Password: password
        //         'nim' => '12345678',
        //         'angkatan' => '2019',
        //         'tahun_lulus' => '2023',
        //         'role' => 'admin',
        //         'avatar' => null,
        //     ]
        // );

        // // Buat Profil Admin
        // $me->alumniProfile()->updateOrCreate(['user_id' => $me->id], [
        //     'phone' => '081234567890',
        //     'address' => 'Medan, Sumatera Utara',
        //     'gender' => 'L',
        //     'privacy_settings' => ['show_in_directory' => true, 'allow_contact' => true, 'show_email' => true]
        // ]);

        // 2. Buat 15 User Alumni Dummy
        for ($i = 0; $i < 15; $i++) {
            $gender = $faker->randomElement(['L', 'P']);
            $user = User::create([
                'name' => $faker->name($gender === 'L' ? 'male' : 'female'),
                'email' => $faker->unique()->email,
                'password' => Hash::make('password'),
                'nim' => $faker->unique()->numerify('419#####'),
                'angkatan' => $faker->numberBetween(2015, 2020),
                'tahun_lulus' => $faker->numberBetween(2019, 2024),
                'email_verified_at' => $now,
                'role' => 'alumni',
            ]);

            // A. Buat Profil Alumni (PENTING BIAR MUNCUL DI DIREKTORI)
            $user->alumniProfile()->create([
                'phone' => $faker->phoneNumber,
                'address' => $faker->city . ', Indonesia',
                'linkedin_url' => 'https://linkedin.com/in/' . $user->name,
                'gender' => $gender,
                'date_of_birth' => $faker->date('Y-m-d', '2000-01-01'),
                'privacy_settings' => [
                    'show_in_directory' => true, // Set TRUE biar muncul
                    'allow_contact' => $faker->boolean(70), // 70% chance boleh dikontak
                    'show_email' => $faker->boolean(50)
                ]
            ]);

            // B. Buat Tracer Study (PENTING BIAR ADA INFO KERJA)
            $status = $faker->randomElement(['Bekerja', 'Wirausaha', 'Lanjut Studi', 'Mencari Kerja']);

            $user->tracerStudy()->create([
                'tahun_lulus' => $user->tahun_lulus,
                'status_pekerjaan' => $status,
                'nama_instansi' => ($status == 'Bekerja') ? $faker->company : null,
                'jabatan' => ($status == 'Bekerja') ? $faker->jobTitle : null,
                'jenis_instansi' => ($status == 'Bekerja') ? 'Swasta' : null,
                'pendapatan' => ($status == 'Bekerja') ? $faker->numberBetween(5000000, 15000000) : null,
                'relevansi_studi' => $faker->numberBetween(3, 5),
            ]);

            // C. Buat Job Vacancy (Lowongan) - Cuma beberapa user yang posting
            if ($faker->boolean(30)) { // 30% user posting loker
                Job::create([
                    'user_id' => $user->id,
                    'title' => $faker->jobTitle,
                    'company' => $faker->company,
                    'location' => $faker->city,
                    'job_type' => $faker->randomElement(['Full-time', 'Part-time', 'Remote', 'Internship']),
                    'salary_range' => $faker->numberBetween(5, 20) . ' Juta',
                    'description' => $faker->paragraph(5),
                    'application_url' => $faker->boolean ? $faker->url : $faker->email,
                    'closing_date' => $faker->dateTimeBetween('now', '+2 months'),
                    'is_active' => true,
                ]);
            }
        }

        $this->command->info('✅ Berhasil membuat 15 Alumni Dummy + Data Profil + Tracer + Loker!');
    }
}
