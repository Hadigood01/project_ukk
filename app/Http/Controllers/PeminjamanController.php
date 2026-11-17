<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\DetailPeminjaman;
use App\Models\LogAktivitas;
use App\Models\User;
use App\Notifications\TransaksiNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PeminjamanController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | DATA PEMINJAMAN ADMIN
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $peminjaman = Peminjaman::with([
            'user',
            'detail.alat',
            'pengembalian'
        ])
            ->latest('create_at')
            ->paginate(10);

        return view('admin.peminjaman.index', compact('peminjaman'));
    }

    /*
    |--------------------------------------------------------------------------
    | FORM AJUKAN PEMINJAMAN
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $alat = Alat::where('stok', '>', 0)->get();

        return view('peminjaman.ajukan', compact('alat'));
    }

    /*
    |--------------------------------------------------------------------------
    | SIMPAN PENGAJUAN PEMINJAMAN
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        $data = $request->validate([
            'tnggl_pinjam' => 'required|date|after_or_equal:today',
            'tnggl_kembali' => 'required|date|after_or_equal:tnggl_pinjam',
            'alat' => 'required|array|min:1',
            'alat.*.id_alat' => 'required|exists:alat,id_alat',
            'alat.*.jumlah' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($data) {

            $pinjam = Peminjaman::create([
                'id_user' => auth()->id(),
                'tnggl_pinjam' => $data['tnggl_pinjam'],
                'tnggl_kembali' => $data['tnggl_kembali'],
                'status' => 'menunggu',
            ]);

            foreach ($data['alat'] as $item) {
                DetailPeminjaman::create([
                    'id_pinjam' => $pinjam->id_pinjam,
                    'id_alat' => $item['id_alat'],
                    'jumlah' => $item['jumlah'],
                ]);
            }

            LogAktivitas::create([
                'id_user' => auth()->id(),
                'aktivitas' => 'Mengajukan peminjaman alat',
            ]);

            // Notifikasi untuk admin dan petugas
            User::whereIn('level', ['admin', 'petugas'])
                ->get()
                ->each(function ($user) use ($pinjam) {

                    $user->notify(new TransaksiNotification(
                        'Pengajuan Peminjaman Baru',
                        'Ada pengajuan peminjaman baru dengan ID '
                            . $pinjam->id_pinjam
                            . ' yang perlu diperiksa.',
                        'peminjaman_baru',
                        (int) $pinjam->id_pinjam
                    ));

                });
        });

        return redirect()
            ->route('peminjaman.riwayat')
            ->with('success', 'Pengajuan peminjaman berhasil dikirim.');
    }

    /*
    |--------------------------------------------------------------------------
    | DAFTAR PENGAJUAN UNTUK PETUGAS
    |--------------------------------------------------------------------------
    */

    public function persetujuan()
    {
        $peminjaman = Peminjaman::with([
            'user',
            'detail.alat',
            'pengembalian'
        ])
            ->where('status', 'menunggu')
            ->latest('create_at')
            ->paginate(10);

        return view('petugas.peminjaman.index', compact('peminjaman'));
    }

    /*
    |--------------------------------------------------------------------------
    | SETUJUI PEMINJAMAN
    |--------------------------------------------------------------------------
    */

    public function setujui($id)
    {
        DB::transaction(function () use ($id) {

            $pinjam = Peminjaman::with('detail')
                ->lockForUpdate()
                ->findOrFail($id);

            if ($pinjam->status !== 'menunggu') {
                throw ValidationException::withMessages([
                    'status' => 'Peminjaman sudah diproses.',
                ]);
            }

            // Periksa dan kurangi stok alat
            foreach ($pinjam->detail as $detail) {

                $alat = Alat::where('id_alat', $detail->id_alat)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($alat->stok < $detail->jumlah) {
                    throw ValidationException::withMessages([
                        'stok' => 'Stok ' . $alat->nama_alat
                            . ' tidak mencukupi.',
                    ]);
                }

                $alat->decrement('stok', $detail->jumlah);
            }

            // Perbarui status peminjaman
            $pinjam->update([
                'status' => 'dipinjam',
                'disetujui_oleh' => auth()->id(),
            ]);

            LogAktivitas::create([
                'id_user' => auth()->id(),
                'aktivitas' => 'Menyetujui peminjaman ' . $id,
            ]);

            // Notifikasi kepada peminjam
            $peminjam = User::findOrFail($pinjam->id_user);

            $peminjam->notify(new TransaksiNotification(
                'Peminjaman Disetujui',
                'Pengajuan peminjaman dengan ID '
                    . $pinjam->id_pinjam
                    . ' telah disetujui petugas.',
                'peminjaman_disetujui',
                (int) $pinjam->id_pinjam
            ));
        });

        return back()->with(
            'success',
            'Peminjaman berhasil disetujui.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | TOLAK PEMINJAMAN
    |--------------------------------------------------------------------------
    */

    public function tolak($id)
    {
        DB::transaction(function () use ($id) {

            $pinjam = Peminjaman::lockForUpdate()
                ->findOrFail($id);

            if ($pinjam->status !== 'menunggu') {
                throw ValidationException::withMessages([
                    'status' => 'Peminjaman sudah diproses.',
                ]);
            }

            $pinjam->update([
                'status' => 'ditolak',
                'disetujui_oleh' => auth()->id(),
            ]);

            LogAktivitas::create([
                'id_user' => auth()->id(),
                'aktivitas' => 'Menolak peminjaman ' . $id,
            ]);

            // Notifikasi kepada peminjam
            $peminjam = User::findOrFail($pinjam->id_user);

            $peminjam->notify(new TransaksiNotification(
                'Peminjaman Ditolak',
                'Pengajuan peminjaman dengan ID '
                    . $pinjam->id_pinjam
                    . ' telah ditolak petugas.',
                'peminjaman_ditolak',
                (int) $pinjam->id_pinjam
            ));
        });

        return back()->with(
            'success',
            'Peminjaman berhasil ditolak.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RIWAYAT PEMINJAMAN USER
    |--------------------------------------------------------------------------
    */

    public function riwayat()
    {
        $peminjaman = Peminjaman::with([
            'detail.alat',
            'pengembalian'
        ])
            ->where('id_user', auth()->id())
            ->latest('create_at')
            ->paginate(10);

        return view('peminjaman.riwayat', compact('peminjaman'));
    }

    /*
    |--------------------------------------------------------------------------
    | FORM EDIT PEMINJAMAN ADMIN
    |--------------------------------------------------------------------------
    */

    public function edit($id)
    {
        $peminjaman = Peminjaman::with([
            'user',
            'detail.alat'
        ])->findOrFail($id);

        if ($peminjaman->status !== 'menunggu') {
            return redirect()
                ->route('admin.peminjaman.index')
                ->with(
                    'error',
                    'Hanya pengajuan yang menunggu yang dapat diedit.'
                );
        }

        return view('admin.peminjaman.edit', compact('peminjaman'));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE PEMINJAMAN
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'tnggl_pinjam' => 'required|date',
            'tnggl_kembali' => 'required|date|after_or_equal:tnggl_pinjam',
        ]);

        $pinjam = Peminjaman::findOrFail($id);

        if ($pinjam->status !== 'menunggu') {
            return back()->with(
                'error',
                'Hanya pengajuan menunggu yang dapat diedit.'
            );
        }

        $pinjam->update([
            'tnggl_pinjam' => $data['tnggl_pinjam'],
            'tnggl_kembali' => $data['tnggl_kembali'],
        ]);

        return redirect()
            ->route('admin.peminjaman.index')
            ->with('success', 'Data peminjaman berhasil diperbarui.');
    }

    /*
    |--------------------------------------------------------------------------
    | HAPUS PEMINJAMAN
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {

            $pinjam = Peminjaman::lockForUpdate()
                ->findOrFail($id);

            if ($pinjam->status === 'dipinjam') {
                throw ValidationException::withMessages([
                    'status' => 'Transaksi yang sedang dipinjam tidak dapat dihapus.',
                ]);
            }

            if ($pinjam->pengembalian()->exists()) {
                throw ValidationException::withMessages([
                    'status' => 'Transaksi yang memiliki data pengembalian tidak dapat dihapus.',
                ]);
            }

            // Hapus detail terlebih dahulu
            $pinjam->detail()->delete();

            // Hapus transaksi
            $pinjam->delete();
        });

        return redirect()
            ->route('admin.peminjaman.index')
            ->with('success', 'Data peminjaman berhasil dihapus.');
    }
}
