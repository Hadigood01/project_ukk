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
        ->orderBy('tnggl_kembali', 'asc')
        ->paginate(10);

    foreach ($peminjaman as $item) {

        $batas = Carbon::parse($item->tnggl_kembali)->startOfDay();

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
    | PETUGAS: DAFTAR PENGAJUAN
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
            ->orderBy('tnggl_kembali', 'asc')
            ->paginate(10);

        return view(
            'petugas.pengembalian.index',
            compact('peminjaman')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PETUGAS: MENERIMA PENGEMBALIAN
    |--------------------------------------------------------------------------
    */

    public function store(Request $request, $id)
    {
        $data = $request->validate([
            'tanggal_kembali' => 'required|date|before_or_equal:today',
            'kondisi_alat' => 'required|string|max:100',
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

            // Menghitung keterlambatan
            $tanggalBatas = Carbon::parse(
                $pinjam->tnggl_kembali
            )->startOfDay();

            $tanggalDikembalikan = Carbon::parse(
                $data['tanggal_kembali']
            )->startOfDay();

            $terlambat = max(
                0,
                (int) $tanggalBatas->diffInDays(
                    $tanggalDikembalikan,
                    false
                )
            );

            // Denda Rp1.000 per hari
            $denda = $terlambat * 1000;

            // Mengembalikan stok alat
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

            // Memperbarui pengajuan menjadi diterima
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

            // Notifikasi kepada peminjam
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
