<?php

namespace App\Http\Controllers;

use App\Models\Peminjaman;
use App\Models\Pengembalian;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    /**
     * Menampilkan laporan seluruh peminjaman.
     */
    public function index(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
        ]);

        $query = Peminjaman::with([
            'user',
            'detail.alat',
            'pengembalian'
        ]);

        // Filter berdasarkan tanggal mulai peminjaman.
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate(
                'tnggl_pinjam',
                '>=',
                $request->tanggal_mulai
            );
        }

        // Filter berdasarkan tanggal selesai peminjaman.
        if ($request->filled('tanggal_selesai')) {
            $query->whereDate(
                'tnggl_pinjam',
                '<=',
                $request->tanggal_selesai
            );
        }

        // Tampilkan 5 data setiap halaman.
        $peminjaman = $query
            ->orderByDesc('tnggl_pinjam')
            ->paginate(5)
            ->withQueryString();

        return view('petugas.laporan.index', compact('peminjaman'));
    }

    /**
     * Menampilkan laporan pengembalian alat.
     */
    public function pengembalian(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'nullable|date',
            'tanggal_selesai' => 'nullable|date|after_or_equal:tanggal_mulai',
        ]);

        $query = Pengembalian::with([
            'peminjaman.user'
        ]);

        // Filter berdasarkan tanggal mulai pengembalian.
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate(
                'tanggal_kembali',
                '>=',
                $request->tanggal_mulai
            );
        }

        // Filter berdasarkan tanggal selesai pengembalian.
        if ($request->filled('tanggal_selesai')) {
            $query->whereDate(
                'tanggal_kembali',
                '<=',
                $request->tanggal_selesai
            );
        }

        // Hitung total denda dari seluruh data hasil filter.
        $totalDenda = (clone $query)->sum('denda');

        // Bagian laporan pengembalian tetap 20 data setiap halaman.
        $pengembalian = $query
            ->latest('tanggal_kembali')
            ->paginate(20)
            ->withQueryString();

        return view(
            'petugas.laporan.pengembalian',
            compact('pengembalian', 'totalDenda')
        );
    }
}