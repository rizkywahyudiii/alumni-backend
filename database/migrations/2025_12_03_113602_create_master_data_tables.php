<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Industries
        Schema::create('industries', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Technology", "Finance"
            $table->timestamps();
        });

        // 2. Skills
        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Laravel", "Project Management"
            $table->string('category')->nullable(); // e.g., "Hard Skill"
            $table->timestamps();
        });

        // 3. Companies
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('industry_id')->nullable()->constrained('industries')->nullOnDelete();
            $table->string('location')->nullable();
            $table->string('website')->nullable();
            $table->boolean('is_verified')->default(false); // Verified by Admin
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
        Schema::dropIfExists('skills');
        Schema::dropIfExists('industries');
    }
};
