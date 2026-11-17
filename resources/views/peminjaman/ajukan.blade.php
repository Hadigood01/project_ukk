
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajukan Peminjaman</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">

    <div class="mb-4">
        <h3 class="fw-bold mb-1">Ajukan Peminjaman</h3>
        <p class="text-muted mb-0">Isi formulir untuk mengajukan peminjaman alat.</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Periksa kembali formulir:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">

            <form action="{{ route('peminjaman.store') }}" method="POST">
                @csrf

                <h5 class="fw-bold mb-3">Tanggal Peminjaman</h5>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Tanggal Pinjam</label>
                        <input type="date"
                               name="tnggl_pinjam"
                               class="form-control"
                               min="{{ date('Y-m-d') }}"
                               value="{{ old('tnggl_pinjam') }}"
                               required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-semibold">Tanggal Kembali</label>
                        <input type="date"
                               name="tnggl_kembali"
                               class="form-control"
                               min="{{ old('tnggl_pinjam', date('Y-m-d')) }}"
                               value="{{ old('tnggl_kembali') }}"
                               required>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0">Pilih Alat</h5>
                    <button type="button" class="btn btn-sm btn-outline-primary" id="tambahAlat">
                        <i class="bi bi-plus-circle me-1"></i> Tambah Alat
                    </button>
                </div>

                <div id="daftarAlat">

                    <div class="row g-2 mb-3 item-alat">
                        <div class="col-md-7">
                            <label class="form-label">Nama Alat</label>
                            <select name="alat[0][id_alat]" class="form-select" required>
                                <option value="">-- Pilih Alat --</option>

                                @foreach($alat as $item)
                                    <option value="{{ $item->id_alat }}"
                                            data-stok="{{ $item->stok }}">
                                        {{ $item->nama_alat }} (Stok: {{ $item->stok }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Jumlah</label>
                            <input type="number"
                                   name="alat[0][jumlah]"
                                   class="form-control"
                                   min="1"
                                   value="1"
                                   required>
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <button type="button" class="btn btn-danger w-100 hapusAlat" disabled>
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>

                </div>

                <div class="alert alert-info mt-3">
                    <i class="bi bi-info-circle me-1"></i>
                    Pastikan jumlah alat tidak melebihi stok yang tersedia.
                    Pengajuan akan menunggu persetujuan petugas.
                </div>

                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send me-1"></i> Kirim Pengajuan
                    </button>

                    <a href="{{ route('peminjaman.dashboard') }}" class="btn btn-secondary">
                        Kembali
                    </a>
                </div>

            </form>

        </div>
    </div>

</div>

<script>
    let nomorAlat = 1;

    const daftarAlat = document.getElementById('daftarAlat');
    const tombolTambah = document.getElementById('tambahAlat');

    tombolTambah.addEventListener('click', function () {
        const item = document.createElement('div');

        item.className = 'row g-2 mb-3 item-alat';

        item.innerHTML = `
            <div class="col-md-7">
                <label class="form-label">Nama Alat</label>
                <select name="alat[${nomorAlat}][id_alat]" class="form-select" required>
                    <option value="">-- Pilih Alat --</option>
                    @foreach($alat as $item)
                        <option value="{{ $item->id_alat }}">
                            {{ $item->nama_alat }} (Stok: {{ $item->stok }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Jumlah</label>
                <input type="number"
                       name="alat[${nomorAlat}][jumlah]"
                       class="form-control"
                       min="1"
                       value="1"
                       required>
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-danger w-100 hapusAlat">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
        `;

        daftarAlat.appendChild(item);
        nomorAlat++;
    });

    daftarAlat.addEventListener('click', function (event) {
        const tombol = event.target.closest('.hapusAlat');

        if (tombol && daftarAlat.querySelectorAll('.item-alat').length > 1) {
            tombol.closest('.item-alat').remove();
        }
    });
</script>

</body>
</html>
