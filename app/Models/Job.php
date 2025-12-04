<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Job extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'company',
        'location',
        'salary_range',
        'job_type',
        'description',
        'application_url',
        'closing_date',
        'is_active',
    ];

    // Relasi: Loker dimiliki oleh User (poster)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope untuk filter loker aktif saja
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->whereDate('closing_date', '>=', now());
    }
}
