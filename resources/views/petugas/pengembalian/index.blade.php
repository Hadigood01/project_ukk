
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengembalian Alat</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">

    <div class="mb-4">
        <h3 class="fw-bold mb-1">Pengembalian Alat</h3>
        <p class="text-muted mb-0">
            Daftar alat yang sedang dipinjam dan perlu dikembalikan.
        </p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            <i class="bi bi-check-circle me-1"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
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
                            <th>Nama Peminjam</th>
                            <th>Tanggal Pinjam</th>
                            <th>Batas Pengembalian</th>
                            <th>Alat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($peminjaman as $item)
                            <tr>
                                <td>
                                    {{ $loop->iteration + ($peminjaman->currentPage() - 1) * $peminjaman->perPage() }}
                                </td>

                                <td>{{ $item->user->nama_user ?? '-' }}</td>

                                <td>
                                    {{ \Carbon\Carbon::parse($item->tnggl_pinjam)->format('d-m-Y') }}
                                </td>

                                <td>
                                    {{ \Carbon\Carbon::parse($item->tnggl_kembali)->format('d-m-Y') }}
                                </td>

                                <td>
                                    @foreach($item->detail as $detail)
                                        <div>
                                            {{ $detail->alat->nama_alat ?? '-' }}
                                            ({{ $detail->jumlah }} buah)
                                        </div>
                                    @endforeach
                                </td>

                                <td>
                                    <button type="button"
                                            class="btn btn-sm btn-success"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalKembali{{ $item->id_pinjam }}">
                                        <i class="bi bi-box-arrow-in-left"></i>
                                        Proses Pengembalian
                                    </button>
                                </td>
                            </tr>

                            {{-- Modal Pengembalian --}}
                            <div class="modal fade"
                                 id="modalKembali{{ $item->id_pinjam }}"
                                 tabindex="-1"
                                 aria-hidden="true">

                                <div class="modal-dialog">
                                    <div class="modal-content">

                                        <form action="{{ route('petugas.pengembalian.store', $item->id_pinjam) }}"
                                              method="POST"
                                              class="form-pengembalian">

                                            @csrf

                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold">
                                                    <i class="bi bi-arrow-return-left me-1"></i>
                                                    Form Pengembalian Alat
                                                </h5>

                                                <button type="button"
                                                        class="btn-close"
                                                        data-bs-dismiss="modal"></button>
                                            </div>

                                            <div class="modal-body">

                                                <p class="mb-2">
                                                    Peminjam:
                                                    <strong>{{ $item->user->nama_user ?? '-' }}</strong>
                                                </p>

                                                <p class="mb-3">
                                                    Batas pengembalian:
                                                    <strong class="text-primary">
                                                        {{ \Carbon\Carbon::parse($item->tnggl_kembali)->format('d-m-Y') }}
                                                    </strong>
                                                </p>

                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">
                                                        Tanggal Pengembalian
                                                    </label>

                                                    <input type="date"
                                                           name="tanggal_kembali"
                                                           class="form-control tanggal-kembali"
                                                           value="{{ date('Y-m-d') }}"
                                                           max="{{ date('Y-m-d') }}"
                                                           data-batas="{{ \Carbon\Carbon::parse($item->tnggl_kembali)->format('Y-m-d') }}"
                                                           required>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">
                                                        Kondisi Alat
                                                    </label>

                                                    <select name="kondisi_alat"
                                                            class="form-select"
                                                            required>
                                                        <option value="">-- Pilih Kondisi --</option>
                                                        <option value="Baik">Baik</option>
                                                        <option value="Rusak Ringan">Rusak Ringan</option>
                                                        <option value="Rusak Berat">Rusak Berat</option>
                                                    </select>
                                                </div>

                                                <div class="card bg-light border-0">
                                                    <div class="card-body">

                                                        <div class="d-flex justify-content-between mb-2">
                                                            <span>Hari keterlambatan</span>
                                                            <strong class="jumlah-hari">0 hari</strong>
                                                        </div>

                                                        <div class="d-flex justify-content-between">
                                                            <span>Perkiraan denda</span>
                                                            <strong class="text-danger nominal-denda">
                                                                Rp0
                                                            </strong>
                                                        </div>

                                                        <hr>

                                                        <small class="text-muted">
                                                            Tarif denda: Rp1.000 per hari keterlambatan.
                                                        </small>

                                                    </div>
                                                </div>

                                            </div>

                                            <div class="modal-footer">
                                                <button type="button"
                                                        class="btn btn-secondary"
                                                        data-bs-dismiss="modal">
                                                    Batal
                                                </button>

                                                <button type="submit" class="btn btn-success">
                                                    <i class="bi bi-save me-1"></i>
                                                    Simpan Pengembalian
                                                </button>
                                            </div>

                                        </form>

                                    </div>
                                </div>
                            </div>

                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                    Tidak ada alat yang sedang dipinjam.
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
            <i class="bi bi-arrow-left me-1"></i>
            Kembali ke Dashboard
        </a>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.querySelectorAll('.form-pengembalian').forEach(function(form) {

    const inputTanggal = form.querySelector('.tanggal-kembali');
    const batasTanggal = inputTanggal.dataset.batas;
    const jumlahHari = form.querySelector('.jumlah-hari');
    const nominalDenda = form.querySelector('.nominal-denda');

    function hitungDenda() {

        const tanggalDipilih = inputTanggal.value;

        if (!tanggalDipilih) {
            jumlahHari.textContent = '0 hari';
            nominalDenda.textContent = 'Rp0';
            return;
        }

        const batas = new Date(batasTanggal + 'T00:00:00');
        const kembali = new Date(tanggalDipilih + 'T00:00:00');

        const selisih = Math.floor(
            (kembali - batas) / (1000 * 60 * 60 * 24)
        );

        const terlambat = Math.max(0, selisih);
        const denda = terlambat * 1000;

        jumlahHari.textContent = terlambat + ' hari';

        nominalDenda.textContent = 'Rp' +
            new Intl.NumberFormat('id-ID').format(denda);
    }

    inputTanggal.addEventListener('change', hitungDenda);

    hitungDenda();
});
</script>

</body>
</html>
