<?php

namespace App\Imports;

use App\Models\AlumniCandidate;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AlumniCandidateImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Validasi sederhana: Pastikan NIM dan Nama ada
        if (!isset($row['nim']) || !isset($row['nama_lengkap'])) {
            return null;
        }

        // Konversi Tanggal Excel ke Format SQL (YYYY-MM-DD)
        // Excel menyimpan tanggal sebagai angka, jadi perlu dikonversi
        $dob = null;
        if (isset($row['tanggal_lahir'])) {
            try {
                // Cek apakah formatnya angka (Excel Date) atau String biasa
                if (is_numeric($row['tanggal_lahir'])) {
                    $dob = Date::excelToDateTimeObject($row['tanggal_lahir'])->format('Y-m-d');
                } else {
                    $dob = date('Y-m-d', strtotime($row['tanggal_lahir']));
                }
            } catch (\Exception $e) {
                $dob = null; // Default jika error
            }
        }

        return AlumniCandidate::updateOrCreate(
            ['nim' => $row['nim']], // Kunci pencarian (Agar tidak duplikat)
            [
                'name'          => $row['nama_lengkap'],
                'prodi'         => $row['prodi'] ?? null,
                'angkatan'      => $row['angkatan'] ?? null,
                'date_of_birth' => $dob,
            ]
        );
    }
}
