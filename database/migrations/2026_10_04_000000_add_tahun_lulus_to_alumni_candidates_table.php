<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alumni_candidates', function (Blueprint $table) {
            $table->year('tahun_lulus')->nullable()->after('angkatan'); // NULL = mahasiswa aktif
        });
    }

    public function down(): void
    {
        Schema::table('alumni_candidates', function (Blueprint $table) {
            $table->dropColumn('tahun_lulus');
        });
    }
};
