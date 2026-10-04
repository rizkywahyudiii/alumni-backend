<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlumniProfile extends Model
{
    use HasFactory;

    protected $guarded = ['id']; // Allow all columns to be mass assigned except ID

    protected $casts = [
        'privacy_settings' => 'array', // Otomatis convert JSON DB ke Array PHP
        'date_of_birth' => 'date:Y-m-d',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
