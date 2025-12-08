<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth; // Jangan lupa import ini

class UserController extends Controller
{
    /**
     * Get All Users
     */
    public function index(Request $request)
    {
        $currentUser = Auth::user();
        $query = User::with('alumniProfile')->latest();

        // 🛡️ SECURITY: Sembunyikan Super Admin dari mata Admin Biasa
        if ($currentUser->role !== 'super_admin') {
            $query->where('role', '!=', 'super_admin');
        }

        // Fitur Pencarian
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nim', 'like', "%{$search}%");
            });
        }

        // Filter Role
        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        return response()->json([
            'status' => 'success',
            'data' => $query->paginate(10)
        ]);
    }

    /**
     * Update User
     */
    public function update(Request $request, $id)
    {
        $currentUser = Auth::user();
        $targetUser = User::findOrFail($id);

        // 🛡️ SECURITY 1: Admin Biasa TIDAK BOLEH mengedit Super Admin
        if ($targetUser->role === 'super_admin' && $currentUser->role !== 'super_admin') {
            return response()->json(['message' => 'Anda tidak memiliki akses untuk mengubah data Super Admin.'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nim'  => 'nullable|string|max:20',
            // Pastikan role yang dikirim valid
            'role' => 'required|in:super_admin,admin,dosen,alumni,mahasiswa',
            'password' => 'nullable|min:8',
        ]);

        // 🛡️ SECURITY 2: Admin Biasa TIDAK BOLEH mengangkat user jadi Super Admin
        if ($validated['role'] === 'super_admin' && $currentUser->role !== 'super_admin') {
            return response()->json(['message' => 'Anda tidak memiliki hak akses untuk memberikan role Super Admin.'], 403);
        }

        // Update data
        $targetUser->name = $validated['name'];
        $targetUser->nim = $validated['nim'];
        $targetUser->role = $validated['role'];

        if (!empty($validated['password'])) {
            $targetUser->password = Hash::make($validated['password']);
        }

        $targetUser->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Data pengguna berhasil diperbarui',
            'data' => $targetUser
        ]);
    }

    /**
     * Delete User
     */
    public function destroy($id)
    {
        $currentUser = Auth::user();
        $targetUser = User::findOrFail($id);

        // Cek hapus diri sendiri
        if ($targetUser->id === $currentUser->id) {
            return response()->json(['message' => 'Anda tidak bisa menghapus akun sendiri!'], 403);
        }

        // 🛡️ SECURITY: Admin Biasa TIDAK BOLEH menghapus Super Admin
        if ($targetUser->role === 'super_admin' && $currentUser->role !== 'super_admin') {
            return response()->json(['message' => 'Anda tidak memiliki akses untuk menghapus Super Admin.'], 403);
        }

        $targetUser->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Pengguna berhasil dihapus permanen'
        ]);
    }
}
