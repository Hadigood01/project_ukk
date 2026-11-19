<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\LogAktivitas;
use App\Models\User;
use App\Notifications\TransaksiNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PengembalianController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | ADMIN: RIWAYAT PENGEMBALIAN
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $pengembalian = Pengembalian::with([
            'peminjaman.user',
            'petugas'
        ])
            ->latest('id_kembali')
            ->paginate(10);

        return view('admin.pengembalian.index', compact('pengembalian'));
    }

    /*
    |--------------------------------------------------------------------------
    | PETUGAS: MENGATUR BATAS PENGEMBALIAN
    |--------------------------------------------------------------------------
    */

    public function aturBatas(Request $request, $id)
    {
        $data = $request->validate([
            'tanggal_kembali_petugas' => 'required|date',
        ], [
            'tanggal_kembali_petugas.required' => 'Tanggal batas pengembalian wajib diisi.',
            'tanggal_kembali_petugas.date' => 'Tanggal batas pengembalian tidak valid.',
        ]);

        $pinjam = Peminjaman::findOrFail($id);

        if (!in_array($pinjam->status, ['menunggu', 'dipinjam'])) {
            return back()->with(
                'error',
                'Tanggal hanya bisa diatur untuk peminjaman yang menunggu persetujuan atau sedang dipinjam.'
            );
        }

        $pinjam->update([
            'tanggal_kembali_petugas' => $data['tanggal_kembali_petugas'],
        ]);

        LogAktivitas::create([
            'id_user' => auth()->id(),
            'aktivitas' => 'Mengatur batas pengembalian peminjaman ID '
                . $pinjam->id_pinjam
                . ' menjadi '
                . Carbon::parse($data['tanggal_kembali_petugas'])->format('d-m-Y'),
        ]);

        return back()->with(
            'success',
            'Batas pengembalian berhasil diatur oleh Petugas.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PEMINJAM: MENGAJUKAN PENGEMBALIAN
    |--------------------------------------------------------------------------
    */

    public function ajukanPengembalian($id)
    {
        DB::transaction(function () use ($id) {

            $pinjam = Peminjaman::where('id_user', auth()->id())
                ->lockForUpdate()
                ->findOrFail($id);

            if ($pinjam->status !== 'dipinjam') {
                abort(422, 'Peminjaman belum disetujui atau sudah selesai.');
            }

            if ($pinjam->pengembalian()->exists()) {
                abort(422, 'Pengajuan pengembalian sudah pernah dibuat.');
            }

            Pengembalian::create([
                'id_pinjam' => $pinjam->id_pinjam,
                'tanggal_kembali' => null,
                'kondisi_alat' => null,
                'denda' => 0,
                'diterima_oleh' => null,
                'status' => 'diajukan',
            ]);

            LogAktivitas::create([
                'id_user' => auth()->id(),
                'aktivitas' => 'Mengajukan pengembalian peminjaman ID '
                    . $pinjam->id_pinjam,
            ]);

            User::whereIn('level', ['admin', 'petugas'])
                ->get()
                ->each(function ($user) use ($pinjam) {

                    $user->notify(new TransaksiNotification(
                        'Pengajuan Pengembalian Baru',
                        'Peminjam mengajukan pengembalian transaksi ID '
                            . $pinjam->id_pinjam,
                        'pengembalian_baru',
                        (int) $pinjam->id_pinjam
                    ));
                });
        });

        return redirect()
            ->route('peminjaman.pengembalian.index')
            ->with('success', 'Pengajuan pengembalian berhasil dikirim.');
    }

    /*
    |--------------------------------------------------------------------------
    | PEMINJAM: MELIHAT PENGEMBALIAN SAYA
    |--------------------------------------------------------------------------
    */

    public function pengembalianSaya()
    {
        $peminjaman = Peminjaman::with([
            'detail.alat',
            'pengembalian'
        ])
            ->where('id_user', auth()->id())
            ->where('status', 'dipinjam')
            ->whereDoesntHave('pengembalian')
            ->orderByRaw('COALESCE(tanggal_kembali_petugas, tnggl_kembali) ASC')
            ->paginate(10);

        foreach ($peminjaman as $item) {

            $batasTanggal = $item->tanggal_kembali_petugas
                ?? $item->tnggl_kembali;

            $batas = Carbon::parse($batasTanggal)->startOfDay();

            $hariTerlambat = max(
                0,
                (int) $batas->diffInDays(today(), false)
            );

            $item->hari_terlambat = $hariTerlambat;
            $item->estimasi_denda = $hariTerlambat * 1000;
        }

        return view(
            'peminjaman.pengembalian.index',
            compact('peminjaman')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PETUGAS: DAFTAR PENGAJUAN PENGEMBALIAN
    |--------------------------------------------------------------------------
    */

    public function monitor()
    {
        $peminjaman = Peminjaman::with([
            'user',
            'detail.alat',
            'pengembalian'
        ])
            ->where('status', 'dipinjam')
            ->whereHas('pengembalian', function ($query) {
                $query->where('status', 'diajukan');
            })
            ->orderByRaw('COALESCE(tanggal_kembali_petugas, tnggl_kembali) ASC')
            ->paginate(10);

        return view(
            'petugas.pengembalian.index',
            compact('peminjaman')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PETUGAS: MENERIMA PENGEMBALIAN DAN MENGHITUNG DENDA
    |--------------------------------------------------------------------------
    */

    public function store(Request $request, $id)
    {
        $data = $request->validate([
            'tanggal_kembali' => 'required|date|before_or_equal:today',
            'kondisi_alat' => 'required|string|max:100',
        ], [
            'tanggal_kembali.required' => 'Tanggal pengembalian sebenarnya wajib diisi.',
            'tanggal_kembali.before_or_equal' => 'Tanggal pengembalian tidak boleh melebihi hari ini.',
            'kondisi_alat.required' => 'Kondisi alat wajib dipilih.',
        ]);

        DB::transaction(function () use ($data, $id) {

            $pinjam = Peminjaman::with('detail.alat')
                ->lockForUpdate()
                ->findOrFail($id);

            if ($pinjam->status !== 'dipinjam') {
                abort(422, 'Peminjaman tidak berstatus dipinjam.');
            }

            $pengembalian = Pengembalian::where(
                'id_pinjam',
                $id
            )
                ->lockForUpdate()
                ->firstOrFail();

            if ($pengembalian->status !== 'diajukan') {
                abort(422, 'Pengajuan belum ada atau sudah diproses.');
            }

            // Mengambil batas tanggal dari Petugas.
            // Jika belum diatur, gunakan tanggal dari peminjam.
            $tanggalBatas = Carbon::parse(
                $pinjam->tanggal_kembali_petugas ?? $pinjam->tnggl_kembali
            )->startOfDay();

            // Tanggal aktual alat diterima.
            $tanggalDikembalikan = Carbon::parse(
                $data['tanggal_kembali']
            )->startOfDay();

            // Menghitung jumlah hari keterlambatan.
            $terlambat = max(
                0,
                (int) $tanggalBatas->diffInDays(
                    $tanggalDikembalikan,
                    false
                )
            );

            // Denda Rp1.000 per hari.
            $denda = $terlambat * 1000;

            // Mengembalikan stok alat.
            foreach ($pinjam->detail as $detail) {

                $alat = Alat::where('id_alat', $detail->id_alat)
                    ->lockForUpdate()
                    ->firstOrFail();

                $alat->increment('stok', $detail->jumlah);

                if ($data['kondisi_alat'] !== 'Baik') {
                    $alat->update([
                        'kondisi' => $data['kondisi_alat'],
                    ]);
                }
            }

            // Menyimpan data pengembalian.
            $pengembalian->update([
                'tanggal_kembali' => $data['tanggal_kembali'],
                'kondisi_alat' => $data['kondisi_alat'],
                'denda' => $denda,
                'diterima_oleh' => auth()->id(),
                'status' => 'diterima',
            ]);

            

            LogAktivitas::create([
                'id_user' => auth()->id(),
                'aktivitas' => 'Menerima pengembalian peminjaman ID '
                    . $id
                    . '. Denda Rp'
                    . number_format($denda, 0, ',', '.'),
            ]);

            // Notifikasi kepada peminjam.
            $peminjam = User::findOrFail($pinjam->id_user);

            $peminjam->notify(new TransaksiNotification(
                'Pengembalian Selesai',
                'Pengembalian transaksi ID '
                    . $pinjam->id_pinjam
                    . ' telah diterima. Denda: Rp'
                    . number_format($denda, 0, ',', '.'),
                'pengembalian_diterima',
                (int) $pinjam->id_pinjam
            ));
        });

        return back()->with(
            'success',
            'Pengembalian berhasil diterima dan denda berhasil dihitung.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ADMIN: DETAIL PENGEMBALIAN
    |--------------------------------------------------------------------------
    */

    public function show($id)
    {
        $pengembalian = Pengembalian::with([
            'peminjaman.detail.alat',
            'peminjaman.user',
            'petugas'
        ])->findOrFail($id);

        return view(
            'admin.pengembalian.show',
            compact('pengembalian')
        );
    }

    public function create()
    {
        return redirect()->route('admin.pengembalian.index');
    }

    public function edit($id)
    {
        abort(403);
    }

    public function update(Request $request, $id)
    {
        abort(403);
    }

    public function destroy($id)
    {
        abort(403);
    }
}