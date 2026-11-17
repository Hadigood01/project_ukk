<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Kategori;
use Illuminate\Http\Request;

class AlatController extends Controller
{
    // Menampilkan seluruh data alat untuk admin
    public function index()
    {
        $alat = Alat::with('kategori')
            ->latest()
            ->paginate(10);

        return view('admin.alat.index', compact('alat'));
    }

    // Menampilkan halaman tambah alat
    public function create()
    {
        $kategori = Kategori::orderBy('nama_kategori')->get();

        return view('admin.alat.create', compact('kategori'));
    }

    // Menyimpan data alat baru
    public function store(Request $request)
    {
        $data = $request->validate([
            'nama_alat' => 'required|string|max:100',
            'id_kategori' => 'required|exists:kategori,id_kategori',
            'stok' => 'required|integer|min:0',
            'kondisi' => 'required|string|max:100',
            'lokasi' => 'required|string|max:100',
        ]);

        Alat::create($data);

        return redirect()
            ->route('admin.alat.index')
            ->with('success', 'Data alat berhasil ditambahkan.');
    }

    // Menampilkan halaman edit alat
    public function edit($id)
    {
        $alat = Alat::findOrFail($id);

        $kategori = Kategori::orderBy('nama_kategori')->get();

        return view('admin.alat.edit', compact('alat', 'kategori'));
    }

    // Memperbarui data alat
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'nama_alat' => 'required|string|max:100',
            'id_kategori' => 'required|exists:kategori,id_kategori',
            'stok' => 'required|integer|min:0',
            'kondisi' => 'required|string|max:100',
            'lokasi' => 'required|string|max:100',
        ]);

        $alat = Alat::findOrFail($id);

        $alat->update($data);

        return redirect()
            ->route('admin.alat.index')
            ->with('success', 'Data alat berhasil diperbarui.');
    }

    // Menghapus data alat
    public function destroy($id)
    {
        $alat = Alat::findOrFail($id);

        // Periksa apakah alat digunakan dalam transaksi
        if ($alat->detailPeminjaman()->exists()) {
            return back()->with(
                'error',
                'Alat tidak dapat dihapus karena sudah digunakan dalam transaksi peminjaman.'
            );
        }

        $alat->delete();

        return redirect()
            ->route('admin.alat.index')
            ->with('success', 'Data alat berhasil dihapus.');
    }

    // Menampilkan daftar alat untuk peminjam
    public function daftar()
    {
        $alat = Alat::with('kategori')
            ->orderBy('nama_alat')
            ->paginate(12);

        return view('peminjaman.alat.index', compact('alat'));
    }
}
