<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumni_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('linkedin_url')->nullable();
            $table->enum('gender', ['L', 'P'])->nullable();
            $table->date('date_of_birth')->nullable();

            // Privacy Settings disimpan sebagai JSON agar fleksibel
            // Default: Show Name & Company (Basic)
            $table->json('privacy_settings')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumni_profiles');
    }
};
