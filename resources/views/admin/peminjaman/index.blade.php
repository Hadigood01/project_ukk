
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Peminjaman</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<div class="container-fluid py-4 px-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-start mb-4">

        <div>
            <h3 class="fw-bold mb-1">Data Peminjaman</h3>
            <p class="text-muted mb-0">
                Daftar seluruh transaksi peminjaman alat.
            </p>
        </div>

        <a href="{{ route('admin.dashboard') }}"
           class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>
            Kembali ke Dashboard
        </a>

    </div>

    {{-- PESAN SUKSES --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle me-1"></i>
            {{ session('success') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- PESAN ERROR --}}
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-circle me-1"></i>
            {{ session('error') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- VALIDASI --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Terjadi kesalahan:</strong>

            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    {{-- TABEL PEMINJAMAN --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-hover align-middle">

                    <thead class="table-primary">
                        <tr>
                            <th>No.</th>
                            <th>Nama Peminjam</th>
                            <th>Tanggal Pinjam</th>
                            <th>Batas Kembali</th>
                            <th>Daftar Alat</th>
                            <th>Status Peminjaman</th>
                            <th>Pengembalian</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($peminjaman as $item)

                            @php
                                $pengembalian = $item->pengembalian;

                                $sudahDiterima = $pengembalian
                                    && $pengembalian->status === 'diterima';

                                $sedangDiajukan = $pengembalian
                                    && $pengembalian->status === 'diajukan';
                            @endphp

                            <tr>

                                {{-- NOMOR --}}
                                <td>
                                    {{ $loop->iteration + ($peminjaman->currentPage() - 1) * $peminjaman->perPage() }}
                                </td>

                                {{-- NAMA PEMINJAM --}}
                                <td>
                                    <div class="fw-semibold">
                                        {{ $item->user->nama_user ?? '-' }}
                                    </div>
                                </td>

                                {{-- TANGGAL PINJAM --}}
                                <td>
                                    @if($item->tnggl_pinjam)
                                        {{ \Carbon\Carbon::parse($item->tnggl_pinjam)->format('d-m-Y') }}
                                    @else
                                        -
                                    @endif
                                </td>

                                {{-- BATAS KEMBALI --}}
                                <td>
                                    @if($item->tnggl_kembali)
                                        {{ \Carbon\Carbon::parse($item->tnggl_kembali)->format('d-m-Y') }}
                                    @else
                                        -
                                    @endif
                                </td>

                                {{-- DAFTAR ALAT --}}
                                <td>
                                    @forelse($item->detail as $detail)

                                        <div class="mb-1">
                                            <i class="bi bi-pc-display text-primary me-1"></i>

                                            {{ $detail->alat->nama_alat ?? 'Alat tidak ditemukan' }}

                                            <span class="text-muted">
                                                ({{ $detail->jumlah }} buah)
                                            </span>
                                        </div>

                                    @empty

                                        <span class="text-muted">
                                            Tidak ada detail alat
                                        </span>

                                    @endforelse
                                </td>

                                {{-- STATUS PEMINJAMAN --}}
                                <td>

                                    @if($item->status === 'menunggu')

                                        <span class="badge bg-warning text-dark">
                                            <i class="bi bi-clock me-1"></i>
                                            Menunggu Persetujuan
                                        </span>

                                    @elseif($item->status === 'ditolak')

                                        <span class="badge bg-danger">
                                            <i class="bi bi-x-circle me-1"></i>
                                            Ditolak
                                        </span>

                                    @elseif($item->status === 'dipinjam' && $sudahDiterima)

                                        <span class="badge bg-success">
                                            <i class="bi bi-check-circle me-1"></i>
                                            Selesai
                                        </span>

                                    @elseif($item->status === 'dipinjam')

                                        <span class="badge bg-primary">
                                            <i class="bi bi-box-seam me-1"></i>
                                            Sedang Dipinjam
                                        </span>

                                    @else

                                        <span class="badge bg-secondary">
                                            {{ ucfirst($item->status) }}
                                        </span>

                                    @endif

                                </td>

                                {{-- INFORMASI PENGEMBALIAN --}}
                                <td>

                                    @if($sudahDiterima)

                                        <div class="text-success fw-semibold mb-1">
                                            <i class="bi bi-check-circle"></i>
                                            Sudah Diterima
                                        </div>

                                        <small class="d-block">
                                            Tanggal:
                                            {{ $pengembalian->tanggal_kembali
                                                ? \Carbon\Carbon::parse($pengembalian->tanggal_kembali)->format('d-m-Y')
                                                : '-' }}
                                        </small>

                                        <small class="d-block text-danger fw-semibold">
                                            Denda:
                                            Rp{{ number_format($pengembalian->denda ?? 0, 0, ',', '.') }}
                                        </small>

                                    @elseif($sedangDiajukan)

                                        <span class="badge bg-info text-dark">
                                            <i class="bi bi-hourglass-split me-1"></i>
                                            Menunggu Pemeriksaan
                                        </span>

                                    @else

                                        <span class="text-muted">
                                            Belum kembali
                                        </span>

                                    @endif

                                </td>

                                {{-- TOMBOL AKSI --}}
                                <td>
                                    <div class="d-flex gap-1">

                                        {{-- EDIT --}}
                                        @if($item->status === 'menunggu')

                                            <a href="{{ route('admin.peminjaman.edit', $item->id_pinjam) }}"
                                               class="btn btn-sm btn-warning"
                                               title="Edit Peminjaman">

                                                <i class="bi bi-pencil-square"></i>

                                            </a>

                                        @endif

                                        {{-- HAPUS --}}
                                        @if($item->status !== 'dipinjam' && !$pengembalian)

                                            <form action="{{ route('admin.peminjaman.destroy', $item->id_pinjam) }}"
                                                  method="POST"
                                                  onsubmit="return confirm('Yakin ingin menghapus data peminjaman ini?')">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="btn btn-sm btn-danger"
                                                        title="Hapus Peminjaman">

                                                    <i class="bi bi-trash"></i>

                                                </button>

                                            </form>

                                        @endif

                                        {{-- JIKA TIDAK ADA AKSI --}}
                                        @if($item->status !== 'menunggu'
                                            && ($item->status === 'dipinjam' || $pengembalian))

                                            <span class="text-muted small">
                                                Tidak ada aksi
                                            </span>

                                        @endif

                                    </div>
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">

                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>

                                    <strong>Belum ada data peminjaman.</strong>

                                    <div class="small">
                                        Data transaksi akan muncul di sini.
                                    </div>

                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>
            </div>

            {{-- PAGINATION --}}
            <div class="mt-3">
                {{ $peminjaman->links('pagination::bootstrap-5') }}
            </div>

        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
