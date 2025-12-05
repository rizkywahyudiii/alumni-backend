# 🔒 Role-Based Access Control (RBAC) Documentation

Dokumentasi ini menjelaskan implementasi Role-Based Access Control (RBAC) di aplikasi Alumni System.

## 📋 Roles yang Tersedia

Aplikasi memiliki 5 role utama:

1. **super_admin** - Super Administrator (IT Support/Developer)
2. **admin** - Admin/Kaprodi (Kepala Program Studi)
3. **dosen** - Dosen
4. **alumni** - Alumni (Lulusan)
5. **mahasiswa** - Mahasiswa (On-going/Masih kuliah)

## 🎯 Permission Matrix

| Fitur | Alumni | Mahasiswa | Dosen | Kaprodi | Super Admin |
|-------|--------|-----------|-------|---------|-------------|
| Lihat Profil Sendiri | ✅ | ✅ | ✅ | ✅ | ✅ |
| Edit Profil Sendiri | ✅ | ✅ | ✅ | ✅ | ✅ |
| Tracer Study | ✅ | ❌ | ❌ | ❌ | ❌ |
| Riwayat Pekerjaan | ✅ | ❌ | ❌ | ❌ | ❌ |
| Riwayat Magang | ✅ | ❌ | ❌ | ❌ | ❌ |
| Posting Lowongan | ✅ | ❌ | ❌ | ✅ | ✅ |
| Lihat Lowongan | ✅ | ✅ | ✅ | ✅ | ✅ |
| Direktori Alumni | ✅ | ✅ | ✅ | ✅ | ✅ |
| Dashboard Analytics | ❌ | ❌ | ✅ | ✅ | ✅ |
| Export Data | ❌ | ❌ | ❌ | ✅ | ✅ |
| Kelola User | ❌ | ❌ | ❌ | ✅ | ✅ |

## 🛠️ Implementasi

### 1. Middleware CheckRole

Middleware untuk memvalidasi role user sebelum mengakses route.

**Lokasi:** `app/Http/Middleware/CheckRole.php`

**Penggunaan:**
```php
Route::middleware(['auth:sanctum', 'role:alumni'])->group(function () {
    // Route hanya untuk alumni
});

Route::middleware(['auth:sanctum', 'role:dosen,admin'])->group(function () {
    // Route untuk dosen atau admin
});
```

### 2. Helper Methods di User Model

User model memiliki helper methods untuk checking role:

```php
$user->isSuperAdmin();      // Cek apakah super admin
$user->isAdmin();           // Cek apakah admin/kaprodi
$user->isDosen();           // Cek apakah dosen
$user->isAlumni();          // Cek apakah alumni
$user->isMahasiswa();       // Cek apakah mahasiswa
$user->hasRole('alumni', 'mahasiswa'); // Cek apakah punya salah satu role
$user->canViewAlumni($alumniId);       // Cek apakah bisa lihat profil alumni
$user->canPostJob();                    // Cek apakah bisa posting job
$user->canViewAnalytics();              // Cek apakah bisa lihat analytics
```

### 3. Route Grouping

Route dikelompokkan berdasarkan role untuk memudahkan maintenance.

**Contoh struktur:**
```php
// Alumni routes
Route::middleware(['auth:sanctum', 'role:alumni'])->prefix('v1/alumni')->group(function () {
    // Route alumni
});

// Mahasiswa routes
Route::middleware(['auth:sanctum', 'role:mahasiswa'])->prefix('v1/mahasiswa')->group(function () {
    // Route mahasiswa
});

// Dosen routes
Route::middleware(['auth:sanctum', 'role:dosen,admin'])->prefix('v1/dosen')->group(function () {
    // Route dosen
});

// Admin routes
Route::middleware(['auth:sanctum', 'role:admin,super_admin'])->prefix('v1/admin')->group(function () {
    // Route admin
});
```

## 📝 Contoh Penggunaan

### Di Controller

```php
public function index(Request $request)
{
    $user = $request->user();
    
    // Cek role
    if ($user->isAlumni()) {
        // Logic untuk alumni
    } elseif ($user->isDosen()) {
        // Logic untuk dosen
    }
    
    // Atau menggunakan helper
    if ($user->canViewAnalytics()) {
        // Tampilkan analytics
    }
}
```

### Di Route

```php
// Route hanya untuk alumni
Route::middleware(['auth:sanctum', 'role:alumni'])->post('/jobs', [JobController::class, 'store']);

// Route untuk multiple roles
Route::middleware(['auth:sanctum', 'role:alumni,mahasiswa'])->get('/directory', [DirectoryController::class, 'index']);

// Route untuk admin dan dosen
Route::middleware(['auth:sanctum', 'role:admin,dosen'])->get('/analytics', [AnalyticsController::class, 'index']);
```

## 🚀 Next Steps

1. ✅ Middleware CheckRole sudah dibuat
2. ✅ Helper methods di User model sudah ditambahkan
3. ⏳ Update routes/api.php untuk menggunakan role-based grouping
4. ⏳ Buat Policy untuk authorization yang lebih kompleks (opsional)
5. ⏳ Update frontend untuk role-based navigation dan UI

## 📚 Referensi

- File contoh route: `routes/api-rbac-example.php`
- Middleware: `app/Http/Middleware/CheckRole.php`
- User Model: `app/Models/User.php`

