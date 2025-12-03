<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Employments (Riwayat Kerja)
        Schema::create('employments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Link ke master company (optional)
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->string('company_name'); // Tetap simpan nama text jaga-jaga kalau company_id null

            $table->string('title'); // Jabatan
            $table->enum('employment_type', ['full_time', 'part_time', 'freelance', 'contract', 'entrepreneur']);

            $table->date('start_date');
            $table->date('end_date')->nullable(); // Null = Masih bekerja (Current Job)

            // Menggunakan Enum untuk Range Gaji (Privacy)
            $table->string('salary_range')->nullable(); // Disimpan string: '<5jt', '5-10jt', dst.

            $table->text('description')->nullable(); // Jobdesk
            $table->timestamps();
        });

        // 2. Internships (Riwayat Magang)
        Schema::create('internships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->string('company_name');

            $table->string('title');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->text('description')->nullable();

            $table->timestamps();
        });

        // 3. User Skills Pivot (Many-to-Many)
        Schema::create('user_skills', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('skill_id')->constrained('skills')->onDelete('cascade');
            $table->primary(['user_id', 'skill_id']); // Prevent duplicate skill entries for one user
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_skills');
        Schema::dropIfExists('internships');
        Schema::dropIfExists('employments');
    }
};
