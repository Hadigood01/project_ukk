
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Aktivitas</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-start mb-4">

        <div>
            <h3 class="fw-bold mb-1">
                <i class="bi bi-clock-history text-primary me-2"></i>
                Log Aktivitas
            </h3>

            <p class="text-muted mb-0">
                Riwayat aktivitas pengguna dalam aplikasi peminjaman alat.
            </p>
        </div>

        <a href="{{ route('admin.dashboard') }}"
           class="btn btn-secondary mt-3">
            <i class="bi bi-arrow-left me-1"></i>
            Kembali ke Dashboard
        </a>

    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-1"></i>
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

    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold mb-0">
                    <i class="bi bi-list-ul me-2"></i>
                    Riwayat Aktivitas
                </h5>

                <span class="badge bg-primary">
                    Total: {{ $logs->total() }} Aktivitas
                </span>
            </div>

            <div class="table-responsive">

                <table class="table table-hover align-middle">

                    <thead class="table-primary">
                        <tr>
                            <th>No.</th>
                            <th>Nama Pengguna</th>
                            <th>Username</th>
                            <th>Aktivitas</th>
                            <th>Waktu</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($logs as $log)
                            <tr>

                                <td>
                                    {{ $loop->iteration + ($logs->currentPage() - 1) * $logs->perPage() }}
                                </td>

                                <td class="fw-semibold">
                                    {{ $log->user->nama_user ?? 'Pengguna tidak ditemukan' }}
                                </td>

                                <td>
                                    {{ $log->user->username ?? '-' }}
                                </td>

                                <td>
                                    <span class="badge bg-info text-dark">
                                        {{ $log->aktivitas }}
                                    </span>
                                </td>

                                <td>
                                    @if($log->created_at)
                                        {{ \Carbon\Carbon::parse($log->created_at)->format('d-m-Y H:i:s') }}
                                    @else
                                        -
                                    @endif
                                </td>

                                <td class="text-center">

                                    <form action="{{ route('admin.log.destroy', ['id' => $log->id]) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Yakin ingin menghapus log aktivitas ini?')">

                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="bi bi-trash"></i>
                                            Hapus
                                        </button>

                                    </form>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                    Belum ada aktivitas yang tercatat.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3">

                <div class="text-muted small">
                    Menampilkan {{ $logs->firstItem() ?? 0 }}
                    sampai {{ $logs->lastItem() ?? 0 }}
                    dari {{ $logs->total() }} aktivitas
                </div>

                <div>
                    {{ $logs->links('pagination::bootstrap-5') }}
                </div>

            </div>

        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
