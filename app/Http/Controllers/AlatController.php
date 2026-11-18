<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Kategori;
use Illuminate\Http\Request;

class AlatController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ADMIN: MENAMPILKAN DATA ALAT
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $alat = Alat::with('kategori')
            ->latest()
            ->paginate(10);

        return view('admin.alat.index', compact('alat'));
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN: FORM TAMBAH ALAT
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $kategori = Kategori::orderBy('nama_kategori')->get();

        return view('admin.alat.create', compact('kategori'));
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN: MENYIMPAN DATA ALAT
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | ADMIN: FORM EDIT ALAT
    |--------------------------------------------------------------------------
    */

    public function edit($id)
    {
        $alat = Alat::findOrFail($id);

        $kategori = Kategori::orderBy('nama_kategori')->get();

        return view('admin.alat.edit', compact('alat', 'kategori'));
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN: MEMPERBARUI DATA ALAT
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | ADMIN: MENGHAPUS DATA ALAT
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        $alat = Alat::findOrFail($id);

        // Periksa apakah alat sudah digunakan dalam transaksi
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

    /*
    |--------------------------------------------------------------------------
    | PEMINJAM: MENAMPILKAN DAFTAR ALAT
    |--------------------------------------------------------------------------
    */

    public function daftar()
    {
        $alat = Alat::with('kategori')
            ->where('stok', '>', 0)
            ->orderBy('nama_alat')
            ->paginate(12);

        return view('peminjaman.alat.index', compact('alat'));
    }
}
