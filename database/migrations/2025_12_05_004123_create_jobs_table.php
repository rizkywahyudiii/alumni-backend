<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();

            // Siapa yang posting loker ini? (Alumni/Admin)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('title');        // Posisi, misal: "Frontend Developer"
            $table->string('company');      // Nama Perusahaan
            $table->string('location');     // Lokasi, misal: "Jakarta Selatan (Remote)"
            $table->string('salary_range')->nullable(); // Misal: "8jt - 12jt" (String aja biar fleksibel)

            // Tipe pekerjaan: Full-time, Contract, Internship, Freelance
            $table->string('job_type')->default('Full-time');

            $table->text('description');    // Deskripsi lengkap & kualifikasi
            $table->string('application_url')->nullable(); // Link apply (LinkedIn/JobStreet/Email)

            $table->date('closing_date')->nullable(); // Batas akhir lamaran

            $table->boolean('is_active')->default(true); // Status loker aktif/tutup

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
