
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Peminjaman</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">

    <div class="mb-4">
        <h3 class="fw-bold">Riwayat Peminjaman</h3>
        <p class="text-muted">Daftar pengajuan dan status pengembalian alat Anda.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>No.</th>
                            <th>Tanggal Pinjam</th>
                            <th>Rencana Kembali</th>
                            <th>Alat</th>
                            <th>Status Peminjaman</th>
                            <th>Status Pengembalian</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($peminjaman as $item)
                            @php
                                $batas = \Carbon\Carbon::parse($item->tnggl_kembali)->startOfDay();
                                $hariTerlambat = max(0, (int) $batas->diffInDays(today(), false));
                                $estimasiDenda = $hariTerlambat * 1000;
                            @endphp

                            <tr>
                                <td>
                                    {{ $loop->iteration + ($peminjaman->currentPage() - 1) * $peminjaman->perPage() }}
                                </td>

                                <td>
                                    {{ \Carbon\Carbon::parse($item->tnggl_pinjam)->format('d-m-Y') }}
                                </td>

                                <td>
                                    {{ $batas->format('d-m-Y') }}
                                </td>

                                <td>
                                    @foreach($item->detail as $detail)
                                        <div>
                                            {{ $detail->alat->nama_alat ?? 'Alat tidak ditemukan' }}
                                            <span class="text-muted">
                                                ({{ $detail->jumlah }} buah)
                                            </span>
                                        </div>
                                    @endforeach
                                </td>

                                <td>
                                    @if($item->status === 'menunggu')
                                        <span class="badge bg-warning text-dark">Menunggu</span>
                                    @elseif($item->status === 'dipinjam')
                                        <span class="badge bg-primary">Disetujui / Dipinjam</span>
                                    @else
                                        <span class="badge bg-danger">Ditolak</span>
                                    @endif
                                </td>

                                <td>
                                    @if($item->pengembalian && $item->pengembalian->status === 'diterima')

                                        @if($item->pengembalian->denda > 0)
                                            <span class="badge bg-danger">Terlambat</span>
                                        @else
                                            <span class="badge bg-success">Tepat Waktu</span>
                                        @endif

                                        <div class="small mt-2">
                                            Tanggal aktual:
                                            {{ \Carbon\Carbon::parse($item->pengembalian->tanggal_kembali)->format('d-m-Y') }}
                                        </div>

                                        <div class="small">
                                            Denda:
                                            <strong class="text-danger">
                                                Rp{{ number_format($item->pengembalian->denda, 0, ',', '.') }}
                                            </strong>
                                        </div>

                                    @elseif($item->pengembalian && $item->pengembalian->status === 'diajukan')

                                        <span class="badge bg-info text-dark">
                                            Menunggu Pemeriksaan Petugas
                                        </span>

                                        @if($hariTerlambat > 0)
                                            <div class="small text-danger mt-2">
                                                Terlambat {{ $hariTerlambat }} hari
                                                <br>
                                                Estimasi denda:
                                                Rp{{ number_format($estimasiDenda, 0, ',', '.') }}
                                            </div>
                                        @endif

                                    @elseif($item->status === 'dipinjam')

                                        @if($hariTerlambat > 0)
                                            <span class="badge bg-danger">Belum Kembali - Terlambat</span>

                                            <div class="small text-danger mt-2">
                                                Terlambat {{ $hariTerlambat }} hari
                                                <br>
                                                Estimasi denda:
                                                Rp{{ number_format($estimasiDenda, 0, ',', '.') }}
                                            </div>
                                        @else
                                            <span class="badge bg-warning text-dark">
                                                Belum Dikembalikan
                                            </span>
                                        @endif

                                    @else
                                        <span class="text-muted">Belum tersedia</span>
                                    @endif
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    Belum ada riwayat peminjaman.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $peminjaman->links('pagination::bootstrap-5') }}

        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('peminjaman.dashboard') }}" class="btn btn-secondary">
            Kembali ke Dashboard
        </a>

        <a href="{{ route('peminjaman.create') }}" class="btn btn-primary">
            Ajukan Peminjaman
        </a>
    </div>

</div>

</body>
</html>
