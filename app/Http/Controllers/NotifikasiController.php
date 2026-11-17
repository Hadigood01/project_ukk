<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    /**
     * Menampilkan daftar notifikasi pengguna.
     */
    public function index()
    {
        $notifikasi = auth()->user()
            ->notifications()
            ->latest()
            ->paginate(15);

        return view('notifikasi.index', compact('notifikasi'));
    }

    /**
     * Menandai satu notifikasi sebagai sudah dibaca.
     */
    public function baca($id)
    {
        $notifikasi = auth()->user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail();

        $notifikasi->markAsRead();

        return back()->with('success', 'Notifikasi sudah dibaca.');
    }

    /**
     * Menandai semua notifikasi sebagai sudah dibaca.
     */
    public function bacaSemua()
    {
        auth()->user()
            ->unreadNotifications
            ->markAsRead();

        return back()->with('success', 'Semua notifikasi sudah dibaca.');
    }
}
