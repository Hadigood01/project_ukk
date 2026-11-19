```php
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pengembalian Alat</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Riwayat Pengembalian Alat</h4>
        </div>

        <div class="card-body">

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if($pengembalian->count() > 0)

                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">

                        <thead class="table-light">
                            <tr>
                                <th>No.</th>
                                <th>ID Pengembalian</th>
                                <th>Nama Peminjam</th>
                                <th>Tanggal Pengembalian</th>
                                <th>Denda</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($pengembalian as $item)
                                <tr>
                                    <td>{{ $pengembalian->firstItem() + $loop->index }}</td>

                                    <td>{{ $item->id_kembali }}</td>

                                    <td>
                                        {{ $item->peminjaman->user->nama_user ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $item->tanggal_kembali
                                            ? \Carbon\Carbon::parse($item->tanggal_kembali)->format('d-m-Y')
                                            : '-' }}
                                    </td>

                                    <td class="fw-bold text-danger">
                                        Rp{{ number_format($item->denda ?? 0, 0, ',', '.') }}
                                    </td>

                                    <td>
                                        @if($item->status == 'diterima')
                                            <span class="badge bg-success">Diterima</span>
                                        @else
                                            <span class="badge bg-warning text-dark">
                                                {{ ucfirst($item->status) }}
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        <a href="{{ route('admin.pengembalian.show', $item->id_kembali) }}"
                                           class="btn btn-primary btn-sm">
                                            Lihat Nota
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>

                    </table>
                </div>

                <div class="mt-3">
                    {{ $pengembalian->links() }}
                </div>

            @else
                <div class="alert alert-info text-center">
                    Belum ada riwayat pengembalian alat.
                </div>
            @endif

        </div>
    </div>

</div>

</body>
</html>
```