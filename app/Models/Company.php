<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'is_verified' => 'boolean', // Ubah 0/1 jadi true/false otomatis
    ];

    // Relasi: Perusahaan milik satu industri
    public function industry()
    {
        return $this->belongsTo(Industry::class);
    }

    // Relasi: Perusahaan punya banyak alumni yang bekerja (History)
    public function employments()
    {
        return $this->hasMany(Employment::class);
    }

    // Relasi: Perusahaan punya banyak pemagang
    public function internships()
    {
        return $this->hasMany(Internship::class);
    }
}
