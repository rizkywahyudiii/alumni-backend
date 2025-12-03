<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\AlumniProfile;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): Response
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            // Validasi tambahan untuk Alumni
            'nim' => ['required', 'string', 'max:20', 'unique:'.User::class],
            'angkatan' => ['required', 'integer', 'min:2000', 'max:'.(date('Y'))],
            'tahun_lulus' => ['nullable', 'integer', 'min:2000'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'alumni',     // Default role saat register sendiri
            'status' => 'active',   // Default active
            'nim' => $request->nim,
            'angkatan' => $request->angkatan,
            'tahun_lulus' => $request->tahun_lulus ?? null,
        ]);

        // Otomatis buat record kosong di tabel alumni_profiles
        // Supaya nanti tinggal update, gak perlu create manual
        AlumniProfile::create([
            'user_id' => $user->id,
            'privacy_settings' => [
                'show_in_directory' => true,
                'allow_contact' => false
            ]
        ]);

        event(new Registered($user));

        Auth::login($user);

        return response()->noContent();
    }
}
