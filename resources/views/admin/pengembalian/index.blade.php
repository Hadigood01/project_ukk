
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pengembalian</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">

    <div class="mb-4">
        <h3 class="fw-bold">Data Pengembalian</h3>
        <p class="text-muted">Daftar transaksi pengembalian alat.</p>
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
                            <th>Nama Peminjam</th>
                            <th>Tanggal Kembali</th>
                            <th>Kondisi Alat</th>
                            <th>Denda</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($pengembalian as $item)
                            <tr>
                                <td>
                                    {{ $loop->iteration + ($pengembalian->currentPage() - 1) * $pengembalian->perPage() }}
                                </td>

                                <td>
                                    {{ $item->peminjaman->user->nama_user ?? '-' }}
                                </td>

                                <td>
                                    {{ \Carbon\Carbon::parse($item->tanggal_kembali)->format('d-m-Y') }}
                                </td>

                                <td>
                                    @if($item->kondisi_alat == 'Baik')
                                        <span class="badge bg-success">Baik</span>
                                    @elseif($item->kondisi_alat == 'Rusak Ringan')
                                        <span class="badge bg-warning text-dark">Rusak Ringan</span>
                                    @else
                                        <span class="badge bg-danger">Rusak Berat</span>
                                    @endif
                                </td>

                                <td>
                                    Rp{{ number_format($item->denda, 0, ',', '.') }}
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    Belum ada data pengembalian.
                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>
            </div>

            {{ $pengembalian->links('pagination::bootstrap-5') }}

        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            Kembali ke Dashboard
        </a>
    </div>

</div>

</body>
</html>
