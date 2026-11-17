
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Peminjam</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
          rel="stylesheet">

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

        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 25px;
        }

        .menu-card {
            border: none;
            border-radius: 13px;
        }

        .menu-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 15px;
            color: #344054;
            text-decoration: none;
            border-radius: 9px;
            transition: .2s;
            height: 100%;
        }

        .menu-link:hover {
            background: #edf3fc;
            color: #2463a8;
        }

        .menu-link i {
            font-size: 21px;
            width: 27px;
            flex-shrink: 0;
        }

        .menu-link small {
            display: block;
            color: #7b8494;
            margin-top: 3px;
        }

        .notification-btn {
            width: 38px;
            height: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>

<body>

<nav class="navbar navbar-dark px-3 px-md-4 py-3">
    <div class="container-fluid">

        <span class="navbar-brand fw-semibold">
            <i class="bi bi-pc-display me-2"></i>
            Peminjaman Alat Komputer
        </span>

        <div class="d-flex align-items-center gap-3">

            <!-- NOTIFIKASI -->
            <a href="{{ route('notifikasi.index') }}"
               class="btn btn-light btn-sm position-relative notification-btn">

                <i class="bi bi-bell fs-5"></i>

                @php
                    $jumlahNotifikasi = auth()->user()
                        ->unreadNotifications()
                        ->count();
                @endphp

                @if($jumlahNotifikasi > 0)
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        {{ $jumlahNotifikasi }}
                    </span>
                @endif

            </a>

            <!-- LOGOUT -->
            <form action="{{ route('logout') }}" method="POST">
                @csrf

                <button type="submit" class="btn btn-danger btn-sm">
                    <i class="bi bi-box-arrow-right me-1"></i>
                    Logout
                </button>
            </form>

        </div>
    </div>
</nav>

<div class="container py-4">

    <!-- SAMBUTAN -->
    <div class="welcome mb-4">

        <h3 class="fw-bold">Dashboard Peminjam</h3>

        <p class="mb-0">
            Selamat datang, {{ auth()->user()->nama_user }}.
            Silakan pilih menu untuk melihat dan mengajukan peminjaman alat.
        </p>

    </div>

    <!-- RINGKASAN -->
    <div class="row g-3">

        <div class="col-12 col-md-5 col-lg-4">

            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">

                    <div class="stat-icon bg-primary-subtle text-primary mb-3">
                        <i class="bi bi-clipboard-check"></i>
                    </div>

                    <p class="text-muted mb-1">
                        Total Pengajuan Saya
                    </p>

                    <h2 class="fw-bold mb-0">
                        {{ $jumlah }}
                    </h2>

                    <small class="text-muted">
                        Jumlah seluruh pengajuan peminjaman
                    </small>

                </div>
            </div>

        </div>

    </div>

    <!-- MENU PEMINJAM -->
    <div class="card menu-card shadow-sm mt-4">

        <div class="card-body p-3 p-md-4">

            <h5 class="fw-bold mb-3">
                <i class="bi bi-grid me-2"></i>
                Menu Peminjam
            </h5>

            <div class="row g-2">

                <!-- DAFTAR ALAT -->
                <div class="col-md-6 col-lg-4">

                    <a href="{{ route('peminjaman.alat.index') }}"
                       class="menu-link">

                        <i class="bi bi-pc-display text-primary"></i>

                        <span>
                            <strong>Daftar Alat</strong>
                            <small>
                                Melihat alat yang tersedia untuk dipinjam
                            </small>
                        </span>

                        <i class="bi bi-chevron-right ms-auto"></i>

                    </a>

                </div>

                <!-- AJUKAN PEMINJAMAN -->
                <div class="col-md-6 col-lg-4">

                    <a href="{{ route('peminjaman.create') }}"
                       class="menu-link">

                        <i class="bi bi-plus-circle text-success"></i>

                        <span>
                            <strong>Ajukan Peminjaman</strong>
                            <small>
                                Mengirim pengajuan peminjaman alat
                            </small>
                        </span>

                        <i class="bi bi-chevron-right ms-auto"></i>

                    </a>

                </div>

                <!-- RIWAYAT PEMINJAMAN -->
                <div class="col-md-6 col-lg-4">

                    <a href="{{ route('peminjaman.riwayat') }}"
                       class="menu-link">

                        <i class="bi bi-clock-history text-warning"></i>

                        <span>
                            <strong>Riwayat Peminjaman</strong>
                            <small>
                                Melihat status dan riwayat pengajuan
                            </small>
                        </span>

                        <i class="bi bi-chevron-right ms-auto"></i>

                    </a>

                </div>

                <!-- PENGEMBALIAN -->
                <div class="col-md-6 col-lg-4">

                    <a href="{{ route('peminjaman.pengembalian.index') }}"
                       class="menu-link">

                        <i class="bi bi-arrow-return-left text-danger"></i>

                        <span>
                            <strong>Pengembalian Alat</strong>
                            <small>
                                Melihat alat yang harus dikembalikan
                            </small>
                        </span>

                        <i class="bi bi-chevron-right ms-auto"></i>

                    </a>

                </div>

            </div>
        </div>
    </div>

    <!-- FOOTER -->
    <p class="text-center text-muted small mt-4">
        Sistem Informasi Peminjaman Alat Komputer
    </p>

    <p class="text-center text-muted small">
        Powered by Hadi Kusuma
    </p>

</div>

</body>
</html>
