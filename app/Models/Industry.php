<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Industry extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    // Relasi: Satu industri punya banyak perusahaan
    public function companies()
    {
        return $this->hasMany(Company::class);
    }
}
