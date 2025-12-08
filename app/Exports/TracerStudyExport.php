<?php

namespace App\Exports;

use App\Models\TracerStudy; // Pastikan model ini ada
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TracerStudyExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    /**
    * Mengambil data dari database
    */
    public function collection()
    {
        // Kita load relasi 'user' agar bisa ambil Nama & NIM
        // Pastikan di Model TracerStudy ada: public function user() { return $this->belongsTo(User::class); }
        return TracerStudy::with('user')->get();
    }

    /**
    * Menentukan Judul Header di Excel (Baris 1)
    */
    public function headings(): array
    {
        return [
            'Nama Alumni',
            'NIM',
            'Tahun Lulus',
            'Status Pekerjaan',
            'Nama Instansi',
            'Jenis Instansi',
            'Jabatan',
            'Pendapatan',
            'Relevansi Studi (Skala)',
            'Tanggal Input'
        ];
    }

    /**
    * Mapping data per baris (Sesuai urutan headings)
    */
    public function map($row): array
    {
        return [
            $row->user->name ?? 'User Terhapus',
            $row->user->nim ?? '-',
            $row->tahun_lulus,
            $row->status_pekerjaan,
            $row->nama_instansi ?? '-',
            $row->jenis_instansi ?? '-',
            $row->jabatan ?? '-',
            // Format Rupiah sederhana
            $row->pendapatan ? 'Rp ' . number_format($row->pendapatan, 0, ',', '.') : '-',
            $row->relevansi_studi ?? '-',
            $row->created_at ? $row->created_at->format('d-m-Y H:i') : '-',
        ];
    }
}
