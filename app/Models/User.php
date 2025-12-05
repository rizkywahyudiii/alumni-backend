<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Employment;
use App\Models\Internship;
use App\Models\AlumniProfile;
use App\Models\Skill;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',          // Tambahan
        'status',        // Tambahan
        'nim',           // Tambahan
        'nip',           // Tambahan
        'angkatan',      // Tambahan
        'tahun_lulus',   // Tambahan
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        // Opsional: Casting tahun agar konsisten
        'angkatan' => 'integer',
        'tahun_lulus' => 'integer',
    ];

    // Relasi: User memiliki banyak Employments (Pekerjaan)
    public function employments()
    {
        return $this->hasMany(Employment::class);
    }

    // Relasi: User memiliki banyak Internships (Magang)
    public function internships()
    {
        return $this->hasMany(Internship::class);
    }

    // Relasi: User memiliki satu Alumni Profile
    public function alumniProfile()
    {
        return $this->hasOne(AlumniProfile::class);
    }

    // Relasi: User memiliki banyak Skills
    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'user_skills');
    }

    public function tracerStudy()
    {
        // HasOne jika diasumsikan 1 alumni hanya punya 1 data tracer terkini
        // Atau HasMany jika mau mencatat riwayat pekerjaan (Career History)
        // Untuk simpelnya Tracer Study biasanya HasOne (update data terbaru)
        return $this->hasOne(TracerStudy::class);
    }

    public function jobs()
    {
        return $this->hasMany(Job::class);
    }

    // ========== RBAC HELPER METHODS ==========

    /**
     * Cek apakah user adalah Super Admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /**
     * Cek apakah user adalah Admin/Kaprodi
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin' || $this->isSuperAdmin();
    }

    /**
     * Cek apakah user adalah Dosen
     */
    public function isDosen(): bool
    {
        return $this->role === 'dosen' || $this->isAdmin();
    }

    /**
     * Cek apakah user adalah Alumni
     */
    public function isAlumni(): bool
    {
        return $this->role === 'alumni';
    }

    /**
     * Cek apakah user adalah Mahasiswa (On-going)
     */
    public function isMahasiswa(): bool
    {
        return $this->role === 'mahasiswa';
    }

    /**
     * Cek apakah user memiliki salah satu role yang diberikan
     */
    public function hasRole(...$roles): bool
    {
        return in_array($this->role, $roles);
    }

    /**
     * Cek apakah user memiliki akses untuk melihat data alumni
     * (Alumni bisa lihat sendiri, Dosen/Kaprodi bisa lihat semua)
     */
    public function canViewAlumni(int $alumniId): bool
    {
        if ($this->isAdmin() || $this->isDosen()) {
            return true; // Admin dan Dosen bisa lihat semua
        }

        return $this->id === $alumniId; // Alumni hanya bisa lihat profil sendiri
    }

    /**
     * Cek apakah user memiliki akses untuk posting lowongan kerja
     * (Alumni dan Admin bisa posting, Mahasiswa tidak bisa)
     */
    public function canPostJob(): bool
    {
        return $this->isAlumni() || $this->isAdmin();
    }

    /**
     * Cek apakah user memiliki akses untuk melihat dashboard analytics
     */
    public function canViewAnalytics(): bool
    {
        return $this->isAdmin() || $this->isDosen();
    }
}

