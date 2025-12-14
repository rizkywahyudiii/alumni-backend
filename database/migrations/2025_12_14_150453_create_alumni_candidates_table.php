<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('alumni_candidates', function (Blueprint $table) {
            $table->id();
            $table->string('nim')->unique(); // Kunci Pencarian
            $table->string('name');          // Nama Asli dari Kampus
            $table->date('date_of_birth');   // Kunci Rahasia
            $table->string('prodi')->nullable();
            $table->year('angkatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alumni_candidates');
    }
};
