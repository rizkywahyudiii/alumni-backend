# 🎓 Alumni System API (Backend)

Repositori ini berisi kode sumber backend untuk **Sistem Informasi Alumni**. Dibangun menggunakan framework **Laravel**, backend ini berfungsi sebagai RESTful API yang menyediakan data untuk frontend (React) dan aplikasi mobile (jika ada).

## 🛠 Tech Stack
* **Framework:** Laravel 10.x / 11.x
* **Database:** MySQL / MariaDB
* **Authentication:** Laravel Sanctum (Token based auth)
* **Language:** PHP 8.x

## 📂 Progress & Fitur Saat Ini
Sejauh ini, backend telah menangani fitur-fitur fundamental berikut:

### 1. Authentication & Authorization
* Login & Register untuk Alumni dan Staff/Dosen.
* Middleware untuk proteksi route API.

### 2. User Management (Extended)
Kami telah melakukan kustomisasi pada tabel `users` untuk mengakomodasi kebutuhan data akademik.
* **Migration Status:** Tabel `users` telah dimodifikasi (create & alter).
* **Custom Fields:**
    * `nip` (Nomor Induk Pegawai)
    * `nidn` (Nomor Induk Dosen Nasional)
    * `kode_dosen`
    * `pangkat` & `golongan`
    * `status_kepegawaian`
    * `dosen_id` (Relasi ke data master dosen)
* *Note:* Struktur ini disiapkan untuk integrasi dengan data ERP kampus (e.g., referensi `erp_unimed`).

### 3. Master Data
* CRUD dasar untuk data referensi akademik.

## 🚀 Cara Menjalankan (Local Development)

1.  **Clone repository:**
    ```bash
    git clone <repo_url>
    cd backend
    ```
2.  **Install Dependencies:**
    ```bash
    composer install
    ```
3.  **Environment Setup:**
    ```bash
    cp .env.example .env
    # Sesuaikan DB_DATABASE, DB_USERNAME, DB_PASSWORD di file .env
    ```
4.  **Generate Key:**
    ```bash
    php artisan key:generate
    ```
5.  **Database Migration:**
    ```bash
    php artisan migrate
    # Jika perlu seed data awal:
    php artisan db:seed
    ```
6.  **Run Server:**
    ```bash
    php artisan serve
    ```

## 🗺️ Langkah Selanjutnya (Next Steps)
Berikut adalah roadmap pengembangan backend yang akan segera dieksekusi:

1.  **[TODO] Endpoint Tracer Study:** Membuat Controller dan Model untuk menyimpan kuesioner tracer study alumni (pekerjaan, gaji, relevansi studi).
2.  **[TODO] API Lowongan Kerja (Job Portal):** Endpoint untuk CRUD lowongan kerja yang bisa diposting oleh admin atau mitra perusahaan.
3.  **[TODO] Data Analytics Endpoint:** Menyediakan API agregat untuk dashboard (misal: query SQL untuk menghitung % alumni yang sudah bekerja) -> *Persiapan untuk visualisasi Big Data di frontend.*
4.  **[TODO] Export Data:** Fitur export data alumni ke format Excel/PDF untuk laporan prodi.

---
*Dokumentasi ini dibuat untuk memudahkan developer dan AI Assistant memahami konteks database dan alur logika aplikasi.*
