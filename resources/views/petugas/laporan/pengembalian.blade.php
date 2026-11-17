
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pengembalian</title>

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
            <h3 class="fw-bold mb-1">Laporan Pengembalian</h3>
            <p class="text-muted mb-0">Laporan transaksi pengembalian alat.</p>
        </div>

        <button onclick="window.print()" class="btn btn-primary">
            <i class="bi bi-printer"></i> Cetak Laporan
        </button>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <div class="text-center mb-4">
                <h4 class="fw-bold">LAPORAN PENGEMBALIAN ALAT</h4>
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
                            <th>Tanggal Pengembalian</th>
                            <th>Kondisi Alat</th>
                            <th>Denda</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($pengembalian as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>

                                <td>{{ $item->peminjaman->user->nama_user ?? '-' }}</td>

                                <td>
                                    {{ \Carbon\Carbon::parse($item->peminjaman->tnggl_pinjam)->format('d-m-Y') }}
                                </td>

                                <td>
                                    {{ \Carbon\Carbon::parse($item->tanggal_kembali)->format('d-m-Y') }}
                                </td>

                                <td>{{ $item->kondisi_alat }}</td>

                                <td>
                                    Rp{{ number_format($item->denda, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center">
                                    Belum ada data pengembalian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                    <tfoot>
                        <tr>
                            <th colspan="5" class="text-end">Total Denda</th>
                            <th>
                                Rp{{ number_format($pengembalian->sum('denda'), 0, ',', '.') }}
                            </th>
                        </tr>
                    </tfoot>

                </table>
            </div>

            <div class="no-print mt-3">
                {{ $pengembalian->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>

    <div class="mt-3 no-print">
        <a href="{{ route('petugas.laporan.index') }}" class="btn btn-secondary">
            Kembali ke Laporan Peminjaman
        </a>
    </div>

</div>

</body>
</html>
