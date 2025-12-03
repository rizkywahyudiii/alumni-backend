<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Internship extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'company_name', 'company_id', 'title',
        'start_date', 'end_date', 'description',
        'is_public' // Tambahan
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_public' => 'boolean', // Tambahan
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
