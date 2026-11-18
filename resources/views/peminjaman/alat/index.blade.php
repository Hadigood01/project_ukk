
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Daftar Alat</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
          rel="stylesheet">

    <style>
        body {
            background-color: #f4f6f9;
        }

        .card-alat {
            border: none;
            border-radius: 12px;
            transition: 0.2s;
        }

        .card-alat:hover {
            transform: translateY(-4px);
            box-shadow: 0 5px 18px rgba(0, 0, 0, 0.12);
        }

        .icon-alat {
            width: 55px;
            height: 55px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            background-color: #e7f1ff;
            color: #0d6efd;
        }
    </style>
</head>

<body>

<div class="container py-4">

    <!-- JUDUL HALAMAN -->
    <div class="mb-4">
        <h3 class="fw-bold mb-1">
            <i class="bi bi-tools text-primary"></i>
            Daftar Alat
        </h3>

        <p class="text-muted mb-0">
            Lihat alat yang tersedia untuk dipinjam.
        </p>
    </div>

    <!-- PESAN SUKSES -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>
        </div>
    @endif

    <!-- PESAN ERROR -->
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-circle me-2"></i>
            {{ session('error') }}

            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>
        </div>
    @endif

    <!-- DAFTAR ALAT -->
    <div class="row g-3">

        @forelse($alat as $item)

            <div class="col-md-6 col-lg-4">

                <div class="card card-alat shadow-sm h-100">

                    <div class="card-body p-4">

                        <!-- ICON DAN STATUS -->
                        <div class="d-flex justify-content-between align-items-start mb-3">

                            <div class="icon-alat">
                                <i class="bi bi-tools fs-3"></i>
                            </div>

                            @if($item->stok > 0)
                                <span class="badge bg-success">
                                    Tersedia
                                </span>
                            @else
                                <span class="badge bg-danger">
                                    Stok Habis
                                </span>
                            @endif

                        </div>

                        <!-- NAMA ALAT -->
                        <h5 class="fw-bold">
                            {{ $item->nama_alat }}
                        </h5>

                        <!-- KATEGORI -->
                        <p class="text-muted mb-2">
                            <i class="bi bi-tag me-1"></i>
                            {{ $item->kategori->nama_kategori ?? 'Tanpa kategori' }}
                        </p>

                        <hr>

                        <!-- STOK -->
                        <p class="mb-2">
                            <i class="bi bi-box-seam me-2 text-primary"></i>
                            Stok:
                            <strong>{{ $item->stok }}</strong>
                        </p>

                        <!-- KONDISI -->
                        <p class="mb-2">
                            <i class="bi bi-clipboard-check me-2 text-primary"></i>
                            Kondisi: {{ $item->kondisi }}
                        </p>

                        <!-- LOKASI -->
                        <p class="mb-0">
                            <i class="bi bi-geo-alt me-2 text-primary"></i>
                            Lokasi: {{ $item->lokasi }}
                        </p>

                    </div>

                </div>

            </div>

        @empty

            <div class="col-12">

                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center py-5">

                        <i class="bi bi-inbox fs-1 text-muted"></i>

                        <h5 class="mt-3">
                            Belum Ada Alat
                        </h5>

                        <p class="text-muted mb-0">
                            Saat ini belum ada alat yang tersedia untuk dipinjam.
                        </p>

                    </div>
                </div>

            </div>

        @endforelse

    </div>

    <!-- PAGINATION -->
    <div class="mt-4">
        {{ $alat->links('pagination::bootstrap-5') }}
    </div>

    <!-- TOMBOL NAVIGASI -->
    <div class="d-flex flex-wrap gap-2 mt-4">

        <a href="{{ route('peminjaman.create') }}"
           class="btn btn-primary">

            <i class="bi bi-plus-circle me-1"></i>
            Ajukan Peminjaman

        </a>

        <a href="{{ route('peminjaman.dashboard') }}"
           class="btn btn-secondary">

            <i class="bi bi-arrow-left me-1"></i>
            Kembali ke Dashboard

        </a>

    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
