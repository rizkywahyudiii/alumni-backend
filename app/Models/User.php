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
}
