<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\Pengembalian;

class DashboardController extends Controller
{
    public function admin()
    {
        return view('admin.dashboard', [
            'jumlahUser' => User::count(),
            'jumlahAlat' => Alat::count(),
            'jumlahPeminjaman' => Peminjaman::count(),
            'jumlahPengembalian' => Pengembalian::count(),
        ]);
    }

    public function petugas()
    {
        return view('petugas.dashboard', [
            'menunggu' => Peminjaman::where('status', 'menunggu')->count(),
            'dipinjam' => Peminjaman::where('status', 'dipinjam')->count(),
            'pengembalian' => Pengembalian::count(),
        ]);
    }

    public function peminjaman()
    {
        return view('peminjaman.dashboard', [
            'jumlah' => Peminjaman::where('id_user', auth()->id())->count(),
        ]);
    }
}
