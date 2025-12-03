<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Internship extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Auto-convert kolom tanggal jadi object Carbon (biar gampang diformat)
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Relasi: Magang milik satu User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi: Magang dilakukan di satu Company (Optional, bisa null)
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
