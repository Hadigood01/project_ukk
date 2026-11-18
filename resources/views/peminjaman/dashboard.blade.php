
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
            background-color: #f4f6f9;
        }

        .navbar-custom {
            background-color: #172b4d;
        }

        .navbar-brand {
            font-weight: bold;
            color: white !important;
        }

        .menu-card {
            border: none;
            border-radius: 12px;
            transition: 0.2s;
            height: 100%;
        }

        .menu-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 5px 18px rgba(0, 0, 0, 0.12);
        }

        .menu-icon {
            font-size: 35px;
            margin-bottom: 12px;
        }

        .notification-btn {
            position: relative;
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container">

            <a class="navbar-brand" href="{{ route('peminjaman.dashboard') }}">
                <i class="bi bi-box-seam"></i>
                Sistem Peminjaman Alat
            </a>

            <div class="d-flex align-items-center gap-3">

                <span class="text-white">
                    Halo, {{ auth()->user()->nama_user }}
                </span>

                <!-- TOMBOL NOTIFIKASI -->
                <a href="{{ route('notifikasi.index') }}"
                   class="btn btn-light btn-sm notification-btn"
                   title="Notifikasi">

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
                        <i class="bi bi-box-arrow-right"></i>
                        Logout
                    </button>
                </form>

            </div>
        </div>
    </nav>

    <!-- KONTEN -->
    <div class="container py-4">

        <!-- JUDUL -->
        <div class="mb-4">
            <h3 class="fw-bold">Dashboard Peminjam</h3>

            <p class="text-muted">
                Selamat datang di Sistem Informasi Peminjaman Alat.
                Silakan pilih menu yang tersedia.
            </p>
        </div>

        <!-- INFORMASI -->
        <div class="alert alert-primary">
            <i class="bi bi-info-circle"></i>
            Gunakan menu di bawah untuk melihat alat, mengajukan
            peminjaman, dan memantau transaksi Anda.
        </div>

        <!-- MENU -->
        <div class="row g-4">

            <!-- DATA ALAT -->
            <div class="col-md-4">
                <div class="card menu-card shadow-sm">
                    <div class="card-body text-center p-4">

                        <div class="menu-icon text-primary">
                            <i class="bi bi-pc-display"></i>
                        </div>

                        <h5 class="fw-bold">Data Alat</h5>

                        <p class="text-muted">
                            Lihat daftar alat yang tersedia untuk dipinjam.
                        </p>

                        <a href="{{ route('peminjaman.alat.index') }}"
                           class="btn btn-primary w-100">
                            Lihat Data Alat
                        </a>

                    </div>
                </div>
            </div>

            <!-- AJUKAN PEMINJAMAN -->
            <div class="col-md-4">
                <div class="card menu-card shadow-sm">
                    <div class="card-body text-center p-4">

                        <div class="menu-icon text-success">
                            <i class="bi bi-send-check"></i>
                        </div>

                        <h5 class="fw-bold">Ajukan Peminjaman</h5>

                        <p class="text-muted">
                            Ajukan peminjaman alat sesuai kebutuhan.
                        </p>

                        <a href="{{ route('peminjaman.create') }}"
                           class="btn btn-success w-100">
                            Ajukan Sekarang
                        </a>

                    </div>
                </div>
            </div>

            <!-- RIWAYAT PEMINJAMAN -->
            <div class="col-md-4">
                <div class="card menu-card shadow-sm">
                    <div class="card-body text-center p-4">

                        <div class="menu-icon text-info">
                            <i class="bi bi-clock-history"></i>
                        </div>

                        <h5 class="fw-bold">Riwayat Peminjaman</h5>

                        <p class="text-muted">
                            Lihat status dan riwayat transaksi peminjaman.
                        </p>

                        <a href="{{ route('peminjaman.riwayat') }}"
                           class="btn btn-info text-white w-100">
                            Lihat Riwayat
                        </a>

                    </div>
                </div>
            </div>

            <!-- PENGAJUAN PENGEMBALIAN -->
            <div class="col-md-4">
                <div class="card menu-card shadow-sm">
                    <div class="card-body text-center p-4">

                        <div class="menu-icon text-warning">
                            <i class="bi bi-arrow-return-left"></i>
                        </div>

                        <h5 class="fw-bold">Pengajuan Pengembalian</h5>

                        <p class="text-muted">
                            Lihat peminjaman dan ajukan pengembalian alat.
                        </p>

                        <a href="{{ route('peminjaman.pengembalian.index') }}"
                           class="btn btn-warning w-100">
                            Lihat Pengembalian
                        </a>

                    </div>
                </div>
            </div>

            <!-- NOTIFIKASI -->
            <div class="col-md-4">
                <div class="card menu-card shadow-sm">
                    <div class="card-body text-center p-4">

                        <div class="menu-icon text-danger">
                            <i class="bi bi-bell"></i>
                        </div>

                        <h5 class="fw-bold">Notifikasi</h5>

                        <p class="text-muted">
                            Lihat informasi terbaru mengenai transaksi Anda.
                        </p>

                        <a href="{{ route('notifikasi.index') }}"
                           class="btn btn-danger w-100">
                            Buka Notifikasi

                            @if($jumlahNotifikasi > 0)
                                ({{ $jumlahNotifikasi }})
                            @endif
                        </a>

                    </div>
                </div>
            </div>

        </div>

        <!-- FOOTER -->
        <div class="text-center text-muted mt-5">
            <small>
                &copy; {{ date('Y') }} Sistem Informasi Peminjaman Alat
            </small>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
