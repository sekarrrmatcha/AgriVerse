<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * Tampilkan form pendaftaran mahasiswa baru.
     */
    public function showRegistrationForm()
    {
        $prodis = Prodi::orderBy('nama')->get();

        return view('auth.register', ['prodis' => $prodis]);
    }

    /**
     * Daftarkan user baru, login-kan otomatis, lalu arahkan ke Dashboard.
     */
    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nim' => ['nullable', 'string', 'max:20'],
            'prodi_id' => ['nullable', 'exists:prodis,id'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'nim' => $data['nim'] ?? null,
            'prodi_id' => $data['prodi_id'] ?? null,
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }
}
