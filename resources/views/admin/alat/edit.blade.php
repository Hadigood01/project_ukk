
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Alat</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">

    <div class="mb-4">
        <h3 class="fw-bold mb-1">Edit Data Alat</h3>
        <p class="text-muted mb-0">Perbarui informasi alat yang dipilih.</p>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">

            <form action="{{ route('admin.alat.update', $alat->id_alat) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama Alat</label>
                    <input type="text"
                           name="nama_alat"
                           class="form-control @error('nama_alat') is-invalid @enderror"
                           value="{{ old('nama_alat', $alat->nama_alat) }}">

                    @error('nama_alat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Kategori</label>
                    <select name="id_kategori"
                            class="form-select @error('id_kategori') is-invalid @enderror">

                        <option value="">-- Pilih Kategori --</option>

                        @foreach($kategori as $item)
                            <option value="{{ $item->id_kategori }}"
                                {{ old('id_kategori', $alat->id_kategori) == $item->id_kategori ? 'selected' : '' }}>
                                {{ $item->nama_kategori }}
                            </option>
                        @endforeach
                    </select>

                    @error('id_kategori')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Stok</label>
                    <input type="number"
                           name="stok"
                           min="0"
                           class="form-control @error('stok') is-invalid @enderror"
                           value="{{ old('stok', $alat->stok) }}">

                    @error('stok')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Kondisi Alat</label>
                    <select name="kondisi"
                            class="form-select @error('kondisi') is-invalid @enderror">

                        <option value="">-- Pilih Kondisi --</option>

                        <option value="Baik"
                            {{ old('kondisi', $alat->kondisi) == 'Baik' ? 'selected' : '' }}>
                            Baik
                        </option>

                        <option value="Rusak Ringan"
                            {{ old('kondisi', $alat->kondisi) == 'Rusak Ringan' ? 'selected' : '' }}>
                            Rusak Ringan
                        </option>

                        <option value="Rusak Berat"
                            {{ old('kondisi', $alat->kondisi) == 'Rusak Berat' ? 'selected' : '' }}>
                            Rusak Berat
                        </option>
                    </select>

                    @error('kondisi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Lokasi</label>
                    <input type="text"
                           name="lokasi"
                           class="form-control @error('lokasi') is-invalid @enderror"
                           value="{{ old('lokasi', $alat->lokasi) }}">

                    @error('lokasi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Simpan Perubahan
                    </button>

                    <a href="{{ route('admin.alat.index') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Kembali
                    </a>
                </div>

            </form>

        </div>
    </div>

</div>

</body>
</html>
