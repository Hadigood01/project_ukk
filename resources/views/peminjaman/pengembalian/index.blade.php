
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pengembalian Alat Saya</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<nav class="navbar navbar-dark px-3 py-3" style="background:#243b64">
    <div class="container-fluid">

        <span class="navbar-brand fw-bold">
            <i class="bi bi-arrow-return-left me-2"></i>
            Pengembalian Alat
        </span>

        <a href="{{ route('peminjaman.dashboard') }}"
           class="btn btn-light btn-sm">
            <i class="bi bi-arrow-left"></i> Dashboard
        </a>

    </div>
</nav>

<div class="container py-4">

    <h4 class="fw-bold mb-2">Pengembalian Alat Saya</h4>

    <p class="text-muted">
        Ajukan pengembalian alat yang sedang kamu pinjam kepada petugas.
    </p>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
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

            @forelse($peminjaman as $item)

                <div class="border rounded p-3 mb-3">

                    <div class="d-flex justify-content-between flex-wrap gap-2">

                        <strong>
                            Peminjaman #{{ $item->id_pinjam }}
                        </strong>

                        @if(!$item->pengembalian)

                            <span class="badge bg-warning text-dark">
                                Belum Mengajukan Pengembalian
                            </span>

                        @elseif($item->pengembalian->status === 'diajukan')

                            <span class="badge bg-info text-dark">
                                Menunggu Pemeriksaan Petugas
                            </span>

                        @elseif($item->pengembalian->status === 'diterima')

                            <span class="badge bg-success">
                                Pengembalian Diterima
                            </span>

                        @endif

                    </div>

                    <hr>

                    <p>
                        <strong>Tanggal Pinjam:</strong>
                        {{ \Carbon\Carbon::parse($item->tnggl_pinjam)->format('d-m-Y') }}
                    </p>

                    <p>
                        <strong>Batas Pengembalian:</strong>
                        {{ \Carbon\Carbon::parse($item->tnggl_kembali)->format('d-m-Y') }}
                    </p>

                    <strong>Daftar Alat:</strong>

                    <ul class="mt-2">

                        @foreach($item->detail as $detail)

                            <li>
                                {{ $detail->alat->nama_alat ?? 'Alat tidak ditemukan' }}
                                — {{ $detail->jumlah }} unit
                            </li>

                        @endforeach

                    </ul>

                    @if($item->hari_terlambat > 0)

                        <div class="alert alert-danger">
                            Terlambat {{ $item->hari_terlambat }} hari.

                            Estimasi denda:

                            <strong>
                                Rp{{ number_format($item->estimasi_denda, 0, ',', '.') }}
                            </strong>
                        </div>

                    @else

                        <div class="alert alert-success">
                            Belum terlambat mengembalikan alat.
                        </div>

                    @endif

                    @if(!$item->pengembalian)

                        <form
                            action="{{ route('peminjaman.pengembalian.ajukan', $item->id_pinjam) }}"
                            method="POST"
                            onsubmit="return confirm('Apakah kamu yakin ingin mengajukan pengembalian alat ini?')">

                            @csrf

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send me-1"></i>
                                Ajukan Pengembalian
                            </button>

                        </form>

                    @elseif($item->pengembalian->status === 'diajukan')

                        <div class="alert alert-info mb-0">
                            <i class="bi bi-hourglass-split me-2"></i>

                            Pengajuan sudah dikirim.
                            Silakan tunggu pemeriksaan petugas.
                        </div>

                    @elseif($item->pengembalian->status === 'diterima')

                        <div class="alert alert-success mb-0">

                            <i class="bi bi-check-circle me-2"></i>

                            Pengembalian sudah diterima petugas.

                            <br>

                            Denda:

                            <strong>
                                Rp{{ number_format($item->pengembalian->denda, 0, ',', '.') }}
                            </strong>

                        </div>

                    @endif

                </div>

            @empty

                <div class="text-center py-5">

                    <i class="bi bi-clipboard-check display-4 text-success"></i>

                    <h5 class="mt-3">
                        Tidak Ada Alat yang Perlu Dikembalikan
                    </h5>

                    <p class="text-muted">
                        Saat ini kamu tidak memiliki peminjaman aktif.
                    </p>

                </div>

            @endforelse

            {{ $peminjaman->links() }}

        </div>
    </div>

</div>

</body>
</html>
