
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Petugas</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body {
            background: #f4f6fa;
            font-family: Arial, sans-serif;
        }

        .navbar {
            background: #243b64;
        }

        .welcome {
            background: linear-gradient(120deg, #243b64, #3974bd);
            color: white;
            border-radius: 14px;
            padding: 25px;
        }

        .stat-card {
            border: none;
            border-radius: 13px;
            transition: .2s;
        }

        .stat-card:hover {
            transform: translateY(-4px);
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 23px;
        }

        .menu-card {
            border: none;
            border-radius: 13px;
        }

        .menu-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 13px 15px;
            color: #344054;
            text-decoration: none;
            border-radius: 8px;
            transition: .2s;
        }

        .menu-link:hover {
            background: #edf3fc;
            color: #2463a8;
        }

        .menu-link i {
            font-size: 19px;
            width: 25px;
        }
    </style>
</head>

<body>

<nav class="navbar navbar-dark px-3 px-md-4 py-3">
    <div class="container-fluid">
        <span class="navbar-brand fw-semibold">
            <i class="bi bi-pc-display me-2"></i>
            Peminjaman Alat Komputer - Hadi Kusuma
        </span>

        <div class="d-flex align-items-center gap-3">

    <a href="{{ route('notifikasi.index') }}"
       class="btn btn-light btn-sm position-relative"
       title="Notifikasi">

        <i class="bi bi-bell fs-5"></i>

        @if(auth()->user()->unreadNotifications()->count() > 0)
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                {{ auth()->user()->unreadNotifications()->count() }}
            </span>
        @endif
    </a>

    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-danger btn-sm">
            <i class="bi bi-box-arrow-right me-1"></i> Logout
        </button>
    </form>

</div>
    </div>
</nav>

<div class="container py-4">

    <div class="welcome mb-4">
        <h3 class="fw-bold">Dashboard Petugas</h3>
        <p class="mb-0">
            Selamat datang, {{ auth()->user()->nama_user }}.
            Kelola persetujuan dan pengembalian alat melalui menu berikut.
        </p>
    </div>

    <h5 class="fw-bold mb-3">Ringkasan Peminjaman</h5>

    <div class="row g-3">

        <div class="col-6 col-lg-4">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body">
                    <div class="stat-icon bg-warning-subtle text-warning mb-3">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                    <p class="text-muted mb-1">Peminjaman Menunggu</p>
                    <h3 class="fw-bold mb-0">{{ $menunggu }}</h3>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-4">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body">
                    <div class="stat-icon bg-primary-subtle text-primary mb-3">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <p class="text-muted mb-1">Sedang Dipinjam</p>
                    <h3 class="fw-bold mb-0">{{ $dipinjam }}</h3>
                </div>
            </div>
        </div>

        <div class="col-6 col-lg-4">
            <div class="card stat-card shadow-sm h-100">
                <div class="card-body">
                    <div class="stat-icon bg-success-subtle text-success mb-3">
                        <i class="bi bi-arrow-return-left"></i>
                    </div>
                    <p class="text-muted mb-1">Total Pengembalian</p>
                    <h3 class="fw-bold mb-0">{{ $pengembalian }}</h3>
                </div>
            </div>
        </div>

    </div>

    <div class="card menu-card shadow-sm mt-4">
        <div class="card-body p-3 p-md-4">

            <h5 class="fw-bold mb-3">
                <i class="bi bi-grid me-2"></i>Menu Petugas
            </h5>

            <div class="row g-2">

                <div class="col-md-6 col-lg-4">
                    <a href="{{ route('petugas.peminjaman.index') }}" class="menu-link">
                        <i class="bi bi-clipboard-check text-primary"></i>
                        Persetujuan Peminjaman
                        <i class="bi bi-chevron-right ms-auto"></i>
                    </a>
                </div>

                <div class="col-md-6 col-lg-4">
                    <a href="{{ route('petugas.pengembalian.index') }}" class="menu-link">
                        <i class="bi bi-arrow-return-left text-success"></i>
                        Memantau Pengembalian
                        <i class="bi bi-chevron-right ms-auto"></i>
                    </a>
                </div>

                <div class="col-md-6 col-lg-4">
                    <a href="{{ route('petugas.laporan.index') }}" class="menu-link">
                        <i class="bi bi-file-earmark-bar-graph text-warning"></i>
                        Mencetak Laporan
                        <i class="bi bi-chevron-right ms-auto"></i>
                    </a>
                </div>

            </div>
        </div>
    </div>

    <p class="text-center text-muted small mt-4">
        Sistem Informasi Peminjaman Alat Komputer
    </p>
    <p class="text-center text-muted small mt-4">
        Powered-by Hadi Kusuma
    </p>

</div>

</body>
</html>
