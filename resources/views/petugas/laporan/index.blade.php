
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Peminjaman</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: white !important;
            }

            .card {
                box-shadow: none !important;
                border: none !important;
            }
        }
    </style>
</head>
<body class="bg-light">

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h3 class="fw-bold mb-1">Laporan Peminjaman</h3>
            <p class="text-muted mb-0">Laporan seluruh transaksi peminjaman alat.</p>
        </div>

        <button onclick="window.print()" class="btn btn-primary">
            <i class="bi bi-printer"></i> Cetak Laporan
        </button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <div class="text-center mb-4">
                <h4 class="fw-bold">LAPORAN PEMINJAMAN ALAT</h4>
                <p class="mb-0">Sistem Informasi Peminjaman Alat</p>
                <hr>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered align-middle">

                    <thead class="table-light">
                        <tr>
                            <th>No.</th>
                            <th>Nama Peminjam</th>
                            <th>Tanggal Pinjam</th>
                            <th>Tanggal Kembali</th>
                            <th>Daftar Alat</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($peminjaman as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>

                                <td>{{ $item->user->nama_user ?? '-' }}</td>

                                <td>{{ \Carbon\Carbon::parse($item->tnggl_pinjam)->format('d-m-Y') }}</td>

                                <td>{{ \Carbon\Carbon::parse($item->tnggl_kembali)->format('d-m-Y') }}</td>

                                <td>
                                    @foreach($item->detail as $detail)
                                        <div>
                                            {{ $detail->alat->nama_alat ?? '-' }}
                                            ({{ $detail->jumlah }} buah)
                                        </div>
                                    @endforeach
                                </td>

                                <td>
                                    @if($item->status == 'menunggu')
                                        Menunggu
                                    @elseif($item->status == 'dipinjam')
                                        {{ $item->pengembalian ? 'Sudah Dikembalikan' : 'Sedang Dipinjam' }}
                                    @else
                                        Ditolak
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">
                                    Belum ada data peminjaman.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

            <div class="no-print mt-3">
                {{ $peminjaman->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>

    <div class="mt-3 no-print">
        <a href="{{ route('petugas.dashboard') }}" class="btn btn-secondary">
            Kembali ke Dashboard
        </a>

        <a href="{{ route('petugas.laporan.pengembalian') }}" class="btn btn-success">
            Laporan Pengembalian
        </a>
    </div>

</div>

</body>
</html>
