
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Alat</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">

    <div class="mb-4">
        <h3 class="fw-bold mb-1">Daftar Alat</h3>
        <p class="text-muted mb-0">Lihat alat yang tersedia untuk dipinjam.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-3">

        @forelse($alat as $item)
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm h-100">

                    <div class="card-body">

                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="bg-primary-subtle text-primary rounded-3 p-3">
                                <i class="bi bi-tools fs-3"></i>
                            </div>

                            @if($item->stok > 0)
                                <span class="badge bg-success">Tersedia</span>
                            @else
                                <span class="badge bg-danger">Stok Habis</span>
                            @endif
                        </div>

                        <h5 class="fw-bold">{{ $item->nama_alat }}</h5>

                        <p class="text-muted mb-2">
                            {{ $item->kategori->nama_kategori ?? 'Tanpa kategori' }}
                        </p>

                        <hr>

                        <p class="mb-2">
                            <i class="bi bi-box-seam me-2 text-primary"></i>
                            Stok: <strong>{{ $item->stok }}</strong>
                        </p>

                        <p class="mb-2">
                            <i class="bi bi-clipboard-check me-2 text-primary"></i>
                            Kondisi: {{ $item->kondisi }}
                        </p>

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
                        <p class="text-muted mt-2 mb-0">Belum ada alat yang tersedia.</p>
                    </div>
                </div>
            </div>
        @endforelse

    </div>

    <div class="mt-4">
        {{ $alat->links('pagination::bootstrap-5') }}
    </div>

    <div class="d-flex gap-2 mt-3">
        <a href="{{ route('peminjaman.ajukan') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Ajukan Peminjaman
        </a>

        <a href="{{ route('peminjaman.dashboard') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

</div>

</body>
</html>
