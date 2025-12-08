# 🎓 Alumni System API (Backend)

Repositori ini berisi kode sumber backend untuk **Sistem Informasi Alumni**. Dibangun menggunakan framework **Laravel**, backend ini berfungsi sebagai RESTful API yang menyediakan data untuk frontend (React).

## 🛠 Tech Stack

* **Framework:** Laravel 12.x
* **Database:** MySQL / MariaDB
* **Authentication:** Laravel Sanctum (Token-based auth)
* **Excel Export:** Maatwebsite/Excel
* **Language:** PHP 8.2+

## 📦 Progress & Fitur

### ✅ 1. Authentication & Security (RBAC)
* ✅ **Role-Based Access Control (RBAC):** Middleware `CheckRole` untuk membatasi akses endpoint.
* ✅ **Multi-Role System:** Support role `super_admin`, `admin`, `dosen`, `alumni`, `mahasiswa`.
* ✅ **Hierarchical Protection:** Admin biasa tidak bisa melihat, mengedit, atau menghapus Super Admin.
* ✅ Login & Logout (Sanctum).

### ✅ 2. Admin & Kaprodi Features
* ✅ **User Management:** CRUD User (List, Edit Role, Delete User).
* ✅ **Export Laporan:** Download data Tracer Study ke format Excel (`.xlsx`).
* ✅ **Dashboard Analytics:** Statistik total responden, sebaran karir, dll.

### ✅ 3. Alumni Features
* ✅ **Profile:** Update biodata, foto, dan privasi.
* ✅ **Tracer Study:** Input data karir (status kerja, gaji, relevansi studi).
* ✅ **Career:** Input riwayat pekerjaan dan magang.
* ✅ **Directory:** Melihat profil alumni lain (sesuai privasi).

### ✅ 4. Job Portal
* ✅ **List Lowongan:** Search & Filter (Tipe job, Punya saya).
* ✅ **Posting Lowongan:** Hanya untuk Alumni & Admin.
* ✅ **Permission:** Mahasiswa hanya bisa melihat (View Only).

## 🚀 Roadmap Selanjutnya

1.  **📧 Email System (SMTP)**
    * Fitur Lupa Password (Reset Password).
    * Email Reminder otomatis untuk pengisian Tracer Study.
    * Notifikasi lowongan baru via email.
2.  **🔔 In-App Notifications**
    * Notifikasi real-time di dashboard.
3.  **📈 Advanced Analytics**
    * Grafik trend gaji alumni per tahun lulus.
    * Laporan kesesuaian kurikulum.

## 💻 Cara Install Library Tambahan

Jika baru pull update ini, jalankan:

```bash
composer require maatwebsite/excel
