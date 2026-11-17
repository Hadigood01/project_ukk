
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

<div class="container-fluid py-4 px-4">

    <div class="mb-4">
        <h3 class="fw-bold mb-1">
            <i class="bi bi-clock-history text-primary me-2"></i>
            Log Aktivitas
        </h3>

        <p class="text-muted mb-0">
            Riwayat aktivitas pengguna dalam aplikasi peminjaman alat.
        </p>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-hover align-middle">

                    <thead class="table-primary">
                        <tr>
                            <th>No.</th>
                            <th>Nama Pengguna</th>
                            <th>Username</th>
                            <th>Aktivitas</th>
                            <th>Waktu</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($logs as $log)
                            <tr>
                                <td>
                                    {{ $loop->iteration + ($logs->currentPage() - 1) * $logs->perPage() }}
                                </td>

                                <td>
                                    <div class="fw-semibold">
                                        {{ $log->user->nama_user ?? 'Pengguna tidak ditemukan' }}
                                    </div>
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
                            </tr>

                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                    Belum ada aktivitas yang tercatat.
                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>
            </div>

            <div class="mt-3">
                {{ $logs->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Kembali ke Dashboard
        </a>
    </div>

</div>

</body>
</html>
