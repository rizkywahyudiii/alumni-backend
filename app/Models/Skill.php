<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Relasi: Skill dimiliki banyak User (Many-to-Many)
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_skills');
    }
}
