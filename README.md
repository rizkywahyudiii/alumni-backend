# 🎓 Alumni System API (Backend)

Repositori ini berisi kode sumber backend untuk **Sistem Informasi Alumni**. Dibangun menggunakan framework **Laravel**, backend ini berfungsi sebagai RESTful API yang menyediakan data untuk frontend (React) dan aplikasi mobile (jika ada).

## 🛠 Tech Stack

* **Framework:** Laravel 12.x
* **Database:** MySQL / MariaDB
* **Authentication:** Laravel Sanctum (Token-based auth)
* **Language:** PHP 8.2+
* **Package Manager:** Composer

## 📦 Dependencies & Packages

### Core Dependencies
* `laravel/framework` ^12.0 - Framework utama Laravel
* `laravel/sanctum` ^4.2 - API Authentication (Token-based)
* `laravel/tinker` ^2.10.1 - REPL untuk debugging

### Development Dependencies
* `laravel/breeze` ^2.3 - Authentication scaffolding (API)
* `laravel/pail` ^1.2.2 - Real-time log viewer
* `laravel/pint` ^1.24 - Code style fixer
* `laravel/sail` ^1.41 - Docker development environment
* `fakerphp/faker` ^1.23 - Fake data generator
* `phpunit/phpunit` ^11.5.3 - Testing framework
* `nunomaduro/collision` ^8.6 - Error handler
* `mockery/mockery` ^1.6 - Mocking library

## 📂 Progress & Fitur Saat Ini

### ✅ 1. Authentication & Authorization
* ✅ Login & Register untuk Alumni
* ✅ Token-based authentication menggunakan Laravel Sanctum
* ✅ Middleware untuk proteksi route API
* ✅ CORS configuration untuk frontend React
* ✅ API routes untuk login/logout (`/api/login`, `/api/logout`)

### ✅ 2. User Management
* ✅ Tabel `users` dengan field extended:
  * `role`: enum (super_admin, admin, dosen, mahasiswa, alumni)
  * `status`: enum (active, inactive, graduated)
  * `nim` (Nomor Induk Mahasiswa)
  * `nip` (Nomor Induk Pegawai)
  * `angkatan` (Tahun masuk)
  * `tahun_lulus` (Tahun lulus)
* ✅ Relasi dengan `alumni_profiles` table

### ✅ 3. Alumni Profile Management
* ✅ CRUD Profile Alumni (`/api/v1/alumni/profile`)
* ✅ Upload avatar/foto profil
* ✅ Privacy settings (show_in_directory, allow_contact, show_email)
* ✅ Update biodata (phone, address, linkedin_url, gender, date_of_birth)

### ✅ 4. Career Management
* ✅ **Riwayat Pekerjaan (Employment)**
  * CRUD Employment (`/api/v1/alumni/employments`)
  * Support multiple employment types (full_time, part_time, freelance, entrepreneur)
  * Visibility control (public/private)
* ✅ **Riwayat Magang (Internship)**
  * CRUD Internship (`/api/v1/alumni/internships`)
  * Visibility control (public/private)

### ✅ 5. Tracer Study
* ✅ Form Tracer Study (`/api/v1/alumni/tracer-study`)
* ✅ Simpan/Update data tracer study
* ✅ Cek status pengisian tracer study (`/api/v1/alumni/tracer-study/me`)

### ✅ 6. Job Portal
* ✅ CRUD Lowongan Kerja (`/api/v1/alumni/jobs`)
* ✅ List semua lowongan
* ✅ Detail lowongan
* ✅ Posting lowongan oleh alumni
* ✅ Hapus lowongan (hanya owner)

### ✅ 7. Alumni Directory
* ✅ Direktori Alumni (`/api/v1/alumni/directory`)
* ✅ Search alumni
* ✅ Detail profil alumni publik
* ✅ Filter berdasarkan privacy settings

### ✅ 8. Dashboard & Analytics
* ✅ Dashboard Statistics (`/api/v1/alumni/dashboard/stats`)
* ✅ Total responden
* ✅ Status distribution (sebaran karir alumni)
* ✅ Active jobs count

### ✅ 9. Master Data
* ✅ Industries (`/api/v1/industries`)
* ✅ Skills (`/api/v1/skills`)
* ✅ Companies (`/api/v1/companies`)

## 🚀 Cara Menjalankan (Local Development)

1. **Clone repository:**
   ```bash
   git clone <repo_url>
   cd backend
   ```

2. **Install Dependencies:**
   ```bash
   composer install
   ```

3. **Environment Setup:**
   ```bash
   cp .env.example .env
   # Sesuaikan DB_DATABASE, DB_USERNAME, DB_PASSWORD di file .env
   # Pastikan FRONTEND_URL=http://localhost:5173 untuk CORS
   ```

4. **Generate Key:**
   ```bash
   php artisan key:generate
   ```

5. **Database Migration:**
   ```bash
   php artisan migrate
   # Jika perlu seed data awal:
   php artisan db:seed
   ```

6. **Run Server:**
   ```bash
   php artisan serve
   ```
   Server akan berjalan di `http://localhost:8000`

## 🔐 API Endpoints

### Public Routes
* `POST /api/login` - Login user
* `GET /api/v1/industries` - List industries
* `GET /api/v1/skills` - List skills
* `GET /api/v1/companies` - List companies

### Protected Routes (Require Auth Token)
* `GET /api/user` - Get current user data
* `POST /api/logout` - Logout user

#### Alumni Routes (`/api/v1/alumni/*`)
* `GET /api/v1/alumni/dashboard/stats` - Dashboard statistics
* `GET /api/v1/alumni/profile` - Get profile
* `PUT /api/v1/alumni/profile` - Update profile
* `GET /api/v1/alumni/employments` - List employments
* `POST /api/v1/alumni/employments` - Create employment
* `PUT /api/v1/alumni/employments/{id}` - Update employment
* `DELETE /api/v1/alumni/employments/{id}` - Delete employment
* `GET /api/v1/alumni/internships` - List internships
* `POST /api/v1/alumni/internships` - Create internship
* `PUT /api/v1/alumni/internships/{id}` - Update internship
* `DELETE /api/v1/alumni/internships/{id}` - Delete internship
* `POST /api/v1/alumni/tracer-study` - Save/Update tracer study
* `GET /api/v1/alumni/tracer-study/me` - Get my tracer study
* `GET /api/v1/alumni/jobs` - List jobs
* `POST /api/v1/alumni/jobs` - Create job
* `GET /api/v1/alumni/jobs/{id}` - Get job detail
* `DELETE /api/v1/alumni/jobs/{id}` - Delete job
* `GET /api/v1/alumni/directory` - Search alumni directory
* `GET /api/v1/alumni/directory/{id}` - Get alumni detail

## 🗺️ Roadmap Pengembangan Selanjutnya

### 🔒 Role-Based Access Control (RBAC)
Implementasi sistem akses berbasis role untuk membedakan hak akses antara:
* **Alumni** - Akses penuh ke profil sendiri, tracer study, job portal
* **Mahasiswa (On-going)** - Akses terbatas (view directory, apply jobs, tapi tidak bisa posting jobs)
* **Dosen** - Akses untuk melihat data alumni, statistik, dan monitoring tracer study
* **Kaprodi** - Akses admin untuk mengelola data prodi, export laporan, dan analytics lengkap

**Fitur RBAC yang akan dikembangkan:**
1. ✅ Middleware untuk role checking
2. ✅ Policy untuk authorization
3. ✅ Route grouping berdasarkan role
4. ✅ Dashboard berbeda per role
5. ✅ Menu navigation berdasarkan role
6. ✅ API endpoints dengan role-based access

### 📊 Analytics & Reporting
* Export data alumni ke Excel/PDF
* Laporan tracer study per angkatan
* Statistik serapan lulusan
* Grafik trend karir alumni

### 🔔 Notifications
* Notifikasi lowongan kerja baru
* Reminder pengisian tracer study
* Notifikasi approval/rejection

### 🌐 Integrasi Eksternal
* Integrasi dengan ERP kampus (sync data mahasiswa)
* Integrasi dengan LinkedIn API
* Integrasi dengan sistem email kampus

---

*Dokumentasi ini dibuat untuk memudahkan developer dan AI Assistant memahami konteks database dan alur logika aplikasi.*
