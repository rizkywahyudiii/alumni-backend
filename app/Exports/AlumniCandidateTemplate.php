<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AlumniCandidateTemplate implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'nim',
            'nama_lengkap',
            'prodi',
            'angkatan',
            'tanggal_lahir', // Berikan petunjuk format di header jika perlu
        ];
    }

    public function array(): array
    {
        // Data Dummy sebagai contoh format
        return [
            ['4183111050', 'Contoh Mahasiswa 1', 'Ilmu Komputer', '2020', '2000-01-30'],
            ['4183111051', 'Contoh Mahasiswa 2', 'Sistem Informasi', '2021', '2001-12-25'],
        ];
    }

    // Bikin Header jadi Bold biar rapi
    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
