
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Persetujuan Peminjaman</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">

    <div class="mb-4">
        <h3 class="fw-bold mb-1">Persetujuan Peminjaman</h3>
        <p class="text-muted mb-0">Periksa dan proses pengajuan peminjaman alat.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-hover align-middle">

                    <thead class="table-primary">
                        <tr>
                            <th>No.</th>
                            <th>Peminjam</th>
                            <th>Tanggal Pinjam</th>
                            <th>Tanggal Kembali</th>
                            <th>Alat yang Dipinjam</th>
                            <th>Status</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($peminjaman as $item)
                            <tr>
                                <td>
                                    {{ $loop->iteration + ($peminjaman->currentPage() - 1) * $peminjaman->perPage() }}
                                </td>

                                <td>
                                    <strong>{{ $item->user->nama_user ?? 'Pengguna tidak ditemukan' }}</strong>
                                </td>

                                <td>
                                    {{ \Carbon\Carbon::parse($item->tnggl_pinjam)->format('d-m-Y') }}
                                </td>

                                <td>
                                    {{ \Carbon\Carbon::parse($item->tnggl_kembali)->format('d-m-Y') }}
                                </td>

                                <td>
                                    @foreach($item->detail as $detail)
                                        <div class="mb-1">
                                            <i class="bi bi-tools text-primary me-1"></i>
                                            {{ $detail->alat->nama_alat ?? 'Alat tidak ditemukan' }}
                                            <span class="text-muted">
                                                ({{ $detail->jumlah }} buah)
                                            </span>
                                        </div>
                                    @endforeach
                                </td>

                                <td>
                                    <span class="badge bg-warning text-dark">
                                        Menunggu
                                    </span>
                                </td>

                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">

                                        <form action="{{ route('petugas.peminjaman.setujui', $item->id_pinjam) }}"
                                              method="POST"
                                              onsubmit="return confirm('Yakin ingin menyetujui peminjaman ini?')">
                                            @csrf

                                            <button type="submit" class="btn btn-sm btn-success">
                                                <i class="bi bi-check-circle"></i> Setujui
                                            </button>
                                        </form>

                                        <form action="{{ route('petugas.peminjaman.tolak', $item->id_pinjam) }}"
                                              method="POST"
                                              onsubmit="return confirm('Yakin ingin menolak peminjaman ini?')">
                                            @csrf

                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="bi bi-x-circle"></i> Tolak
                                            </button>
                                        </form>

                                    </div>
                                </td>
                            </tr>

                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                    Tidak ada pengajuan peminjaman yang menunggu persetujuan.
                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>
            </div>

            <div class="mt-3">
                {{ $peminjaman->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('petugas.dashboard') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
        </a>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
