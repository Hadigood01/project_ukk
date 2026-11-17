
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Peminjaman</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">

    <div class="card shadow-sm border-0">

        <div class="card-header bg-primary text-white py-3">
            <h5 class="mb-0">
                <i class="bi bi-pencil-square me-2"></i>
                Edit Data Peminjaman
            </h5>
        </div>

        <div class="card-body p-4">

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mb-4">
                <label class="form-label fw-bold">Nama Peminjam</label>
                <input type="text"
                       class="form-control"
                       value="{{ $peminjaman->user->nama_user ?? $peminjaman->user->name ?? '-' }}"
                       readonly>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Daftar Alat</label>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Nama Alat</th>
                                <th>Jumlah</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($peminjaman->detail as $index => $detail)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $detail->alat->nama_alat ?? '-' }}</td>
                                    <td>{{ $detail->jumlah }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center">
                                        Tidak ada detail alat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <form action="{{ route('admin.peminjaman.update', $peminjaman->id_pinjam) }}"
                  method="POST">

                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label fw-bold">
                        Tanggal Pinjam
                    </label>

                    <input type="date"
                           name="tnggl_pinjam"
                           class="form-control"
                           value="{{ old('tnggl_pinjam', \Carbon\Carbon::parse($peminjaman->tnggl_pinjam)->format('Y-m-d')) }}"
                           required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">
                        Tanggal Kembali
                    </label>

                    <input type="date"
                           name="tnggl_kembali"
                           class="form-control"
                           value="{{ old('tnggl_kembali', \Carbon\Carbon::parse($peminjaman->tnggl_kembali)->format('Y-m-d')) }}"
                           required>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold">
                        Status Peminjaman
                    </label>

                    <input type="text"
                           class="form-control"
                           value="{{ ucfirst($peminjaman->status) }}"
                           readonly>

                    <small class="text-muted">
                        Status hanya dapat diubah melalui proses persetujuan petugas.
                    </small>
                </div>

                <div class="d-flex gap-2">

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i>
                        Simpan Perubahan
                    </button>

                    <a href="{{ route('admin.peminjaman.index') }}"
                       class="btn btn-secondary">
                        <i class="bi bi-arrow-left me-1"></i>
                        Kembali
                    </a>

                </div>

            </form>

        </div>
    </div>

</div>

</body>
</html>
