```php
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Pengembalian Alat</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f1f5f9;
        }

        .nota {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 35px;
            border-radius: 10px;
        }

        .judul {
            text-align: center;
            border-bottom: 2px solid #222;
            padding-bottom: 20px;
            margin-bottom: 25px;
        }

        .total-denda {
            background: #f8f9fa;
            border: 1px solid #ddd;
            padding: 15px;
            border-radius: 8px;
        }

        @media print {
            body {
                background: white;
            }

            .nota {
                max-width: 100%;
                padding: 10px;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>

<div class="container py-4">

    @php
        $pinjam = $pengembalian->peminjaman;

        $batasTanggal = $pinjam->tanggal_kembali_petugas
            ?? $pinjam->tnggl_kembali;

        $tanggalBatas = \Carbon\Carbon::parse($batasTanggal)->startOfDay();

        $tanggalAktual = $pengembalian->tanggal_kembali
            ? \Carbon\Carbon::parse($pengembalian->tanggal_kembali)->startOfDay()
            : null;

        $hariTerlambat = $tanggalAktual
            ? max(0, (int) $tanggalBatas->diffInDays($tanggalAktual, false))
            : 0;

        // Mengambil denda yang sudah tersimpan di database
        $denda = (float) ($pengembalian->denda ?? 0);
    @endphp

    <div class="nota shadow-sm">

        <div class="judul">
            <h3 class="fw-bold">NOTA PENGEMBALIAN ALAT</h3>
            <p class="mb-0">Sistem Informasi Peminjaman Alat</p>
            <small class="text-muted">Bukti transaksi pengembalian alat</small>
        </div>

        <h5 class="fw-bold mb-3">Informasi Transaksi</h5>

        <table class="table table-borderless">
            <tr>
                <td width="40%">ID Pengembalian</td>
                <td>: {{ $pengembalian->id_kembali }}</td>
            </tr>

            <tr>
                <td>ID Peminjaman</td>
                <td>: {{ $pinjam->id_pinjam }}</td>
            </tr>

            <tr>
                <td>Nama Peminjam</td>
                <td>: {{ $pinjam->user->nama_user ?? '-' }}</td>
            </tr>

            <tr>
                <td>Tanggal Pinjam</td>
                <td>: {{ \Carbon\Carbon::parse($pinjam->tnggl_pinjam)->format('d-m-Y') }}</td>
            </tr>

            <tr>
                <td>Batas Pengembalian</td>
                <td>: {{ \Carbon\Carbon::parse($batasTanggal)->format('d-m-Y') }}</td>
            </tr>

            <tr>
                <td>Tanggal Dikembalikan</td>
                <td>: {{ $tanggalAktual ? $tanggalAktual->format('d-m-Y') : '-' }}</td>
            </tr>

            <tr>
                <td>Kondisi Alat</td>
                <td>: {{ $pengembalian->kondisi_alat ?? '-' }}</td>
            </tr>

            <tr>
                <td>Petugas Penerima</td>
                <td>: {{ $pengembalian->petugas->nama_user ?? '-' }}</td>
            </tr>

            <tr>
                <td>Status</td>
                <td>: {{ ucfirst($pengembalian->status) }}</td>
            </tr>
        </table>

        <h5 class="fw-bold mt-4 mb-3">Daftar Alat</h5>

        <div class="table-responsive">
            <table class="table table-bordered">

                <thead class="table-light">
                    <tr>
                        <th>No.</th>
                        <th>Nama Alat</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($pinjam->detail as $detail)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
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

        <div class="total-denda mt-4">

            <div class="d-flex justify-content-between mb-2">
                <span>Jumlah Hari Terlambat</span>
                <strong>{{ $hariTerlambat }} hari</strong>
            </div>

            <div class="d-flex justify-content-between mb-2">
                <span>Tarif Denda</span>
                <strong>Rp1.000 / hari</strong>
            </div>

            <hr>

            <div class="d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">TOTAL DENDA</h5>

                <h4 class="fw-bold text-danger mb-0">
                    Rp{{ number_format($denda, 0, ',', '.') }}
                </h4>
            </div>

        </div>

        <div class="mt-4 text-center">
            <p class="mb-1">Terima kasih telah mengembalikan alat.</p>
            <small class="text-muted">
                Nota ini merupakan bukti transaksi pengembalian alat.
            </small>
        </div>

        <div class="d-flex justify-content-between mt-4 no-print">

            <a href="{{ route('admin.pengembalian.index') }}"
               class="btn btn-secondary">
                Kembali
            </a>

            <button onclick="window.print()" class="btn btn-primary">
                Cetak Nota
            </button>

        </div>

    </div>
</div>

</body>
</html>
```