<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman login.
     */
    public function login()
    {
        if (Auth::check()) {
            return $this->redirectDashboard(Auth::user()->level);
        }

        return view('auth.login');
    }

    /**
     * Memproses login pengguna.
     */
    public function prosesLogin(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($data)) {
            return back()
                ->withErrors([
                    'email' => 'Email atau password salah!',
                ])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = Auth::user();

        // Memeriksa level akun.
        if (!in_array($user->level, [
            'admin',
            'petugas',
            'peminjaman'
        ])) {
            Auth::logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()
                ->route('login')
                ->withErrors([
                    'email' => 'Level akun tidak dikenali.',
                ]);
        }

        return $this->redirectDashboard($user->level);
    }

    /**
     * Mengarahkan pengguna ke dashboard sesuai level.
     */
    private function redirectDashboard($level)
    {
        return match ($level) {
            'admin' => redirect()->route('admin.dashboard'),
            'petugas' => redirect()->route('petugas.dashboard'),
            'peminjaman' => redirect()->route('peminjaman.dashboard'),
            default => redirect()->route('login'),
        };
    }

    /**
     * Menampilkan halaman registrasi.
     */
    public function register()
    {
        if (Auth::check()) {
            return $this->redirectDashboard(Auth::user()->level);
        }

        return view('auth.register');
    }

    /**
     * Memproses pendaftaran pengguna baru.
     */
    public function prosesRegister(Request $request)
    {
        $data = $request->validate([
            'nama_user' => 'required|string|max:100',
            'email' => 'required|email|max:255|unique:user,email',
            'username' => 'required|string|max:255|unique:user,username',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'nama_user' => $data['nama_user'],
            'email' => $data['email'],
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
            'level' => 'peminjaman',
        ]);

        return redirect()
            ->route('login')
            ->with('success', 'Pendaftaran berhasil! Silakan login.');
    }

    /**
     * Mengakhiri sesi login.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'Anda berhasil logout.');
    }
}
