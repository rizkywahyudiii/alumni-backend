<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tracer_studies', function (Blueprint $table) {
            $table->id();

            // Relasi ke tabel users (alumni)
            // on delete cascade artinya jika user dihapus, data tracer-nya ikut terhapus
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->year('tahun_lulus');

            // Status: 'Bekerja', 'Wirausaha', 'Lanjut Studi', 'Mencari Kerja', 'Tidak Bekerja'
            $table->string('status_pekerjaan');

            // Data Pekerjaan/Usaha (Nullable karena kalau nganggur/lanjut studi, ini kosong)
            $table->string('nama_instansi')->nullable(); // Nama PT atau Universitas (jika lanjut studi)
            $table->string('jabatan')->nullable();
            $table->string('jenis_instansi')->nullable(); // Swasta, BUMN, Pemerintahan, Start-up

            // Pendapatan (Gunakan Decimal biar presisi, atau Integer juga oke)
            // 15 digit total, 2 digit di belakang koma
            $table->decimal('pendapatan', 15, 2)->nullable();

            // Skala Relevansi (Misal 1 = Sangat Tidak Relevan, 5 = Sangat Relevan)
            $table->tinyInteger('relevansi_studi')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracer_studies');
    }
};
