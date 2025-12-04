<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TracerStudy extends Model
{
    use HasFactory;

    protected $table = 'tracer_studies';

    protected $fillable = [
        'user_id',
        'tahun_lulus',
        'status_pekerjaan',
        'nama_instansi',
        'jabatan',
        'jenis_instansi',
        'pendapatan',
        'relevansi_studi',
    ];

    // Relasi ke User (Setiap data tracer dimiliki oleh 1 User)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
