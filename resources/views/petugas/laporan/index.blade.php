<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Peminjaman</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <style>
        body {
            background: #f8f9fa;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body {
                background: white !important;
            }

            .card {
                box-shadow: none !important;
                border: none !important;
            }

            .container {
                max-width: 100% !important;
                width: 100% !important;
            }

            table {
                font-size: 12px;
            }
        }
    </style>
</head>

<body>

<div class="container py-4">

    {{-- JUDUL DAN TOMBOL CETAK --}}
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <div>
            <h3 class="fw-bold mb-1">Laporan Peminjaman</h3>
            <p class="text-muted mb-0">
                Laporan seluruh transaksi peminjaman alat.
            </p>
        </div>

        <button onclick="window.print()" class="btn btn-primary">
            Cetak Laporan
        </button>
    </div>

    {{-- FILTER TANGGAL --}}
    <div class="card border-0 shadow-sm mb-4 no-print">
        <div class="card-body">

            <h5 class="fw-bold mb-3">Filter Tanggal Peminjaman</h5>

            <form action="{{ route('petugas.laporan.index') }}"
                  method="GET">

                <div class="row align-items-end">

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date"
                               name="tanggal_mulai"
                               class="form-control"
                               value="{{ request('tanggal_mulai') }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date"
                               name="tanggal_selesai"
                               class="form-control"
                               value="{{ request('tanggal_selesai') }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <button type="submit" class="btn btn-primary">
                            Tampilkan Laporan
                        </button>

                        <a href="{{ route('petugas.laporan.index') }}"
                           class="btn btn-secondary">
                            Reset
                        </a>
                    </div>

                </div>
            </form>

            @if ($errors->any())
                <div class="alert alert-danger mb-0">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>

    {{-- TABEL LAPORAN --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <div class="text-center mb-4">
                <h4 class="fw-bold">LAPORAN PEMINJAMAN ALAT</h4>
                <p class="mb-0">Sistem Informasi Peminjaman Alat</p>

                @if(request('tanggal_mulai') || request('tanggal_selesai'))
                    <p class="mb-0 mt-2">
                        Periode:
                        {{ request('tanggal_mulai') ? \Carbon\Carbon::parse(request('tanggal_mulai'))->format('d-m-Y') : 'Semua tanggal' }}
                        sampai
                        {{ request('tanggal_selesai') ? \Carbon\Carbon::parse(request('tanggal_selesai'))->format('d-m-Y') : 'Semua tanggal' }}
                    </p>
                @endif

                <hr>
            </div>

            <div class="table-responsive">

                <table class="table table-bordered align-middle">

                    <thead class="table-light">
                        <tr>
                            <th>No.</th>
                            <th>Nama Peminjam</th>
                            <th>Tanggal Pinjam</th>
                            <th>Tanggal Kembali</th>
                            <th>Daftar Alat</th>
                            <th>Status</th>
                            <th class="no-print">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($peminjaman as $item)

                            @php
                                if ($item->status == 'menunggu') {
                                    $statusCetak = 'Menunggu';
                                } elseif ($item->status == 'dipinjam') {
                                    $statusCetak = $item->pengembalian
                                        ? 'Sudah Dikembalikan'
                                        : 'Sedang Dipinjam';
                                } else {
                                    $statusCetak = 'Ditolak';
                                }

                                $daftarAlatCetak = $item->detail->map(function ($detail) {
                                    return ($detail->alat->nama_alat ?? '-') .
                                        ' (' . $detail->jumlah . ' buah)';
                                })->values()->all();
                            @endphp

                            <tr>
                                <td>
                                    {{ $peminjaman->firstItem() + $loop->index }}
                                </td>

                                <td>
                                    {{ $item->user->nama_user ?? '-' }}
                                </td>

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
                                    {{ $statusCetak }}
                                </td>

                                <td class="no-print">
                                    <button type="button"
                                            class="btn btn-sm btn-success"
                                            onclick="cetakSatu({{ \Illuminate\Support\Js::from([
                                                'id' => $item->getKey(),
                                                'nama' => $item->user->nama_user ?? '-',
                                                'tanggal_pinjam' => \Carbon\Carbon::parse($item->tnggl_pinjam)->format('d-m-Y'),
                                                'tanggal_kembali' => \Carbon\Carbon::parse($item->tnggl_kembali)->format('d-m-Y'),
                                                'alat' => $daftarAlatCetak,
                                                'status' => $statusCetak
                                            ]) }})">
                                        Cetak
                                    </button>
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="7" class="text-center">
                                    Belum ada data peminjaman.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- PAGINATION --}}
            <div class="no-print mt-3">
                {{ $peminjaman->links('pagination::bootstrap-5') }}
            </div>

            <div class="text-muted small no-print">
                Menampilkan maksimal 5 transaksi dalam satu halaman.
            </div>

        </div>
    </div>

    {{-- TOMBOL BAWAH --}}
    <div class="mt-3 no-print">

        <a href="{{ route('petugas.dashboard') }}"
           class="btn btn-secondary">
            Kembali ke Dashboard
        </a>

        <a href="{{ route('petugas.laporan.pengembalian') }}"
           class="btn btn-success">
            Laporan Pengembalian
        </a>

    </div>

</div>

<script>
    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, function (char) {
            const entities = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };

            return entities[char];
        });
    }

    function cetakSatu(data) {
        const jendela = window.open('', '_blank', 'width=900,height=700');

        if (!jendela) {
            alert('Izinkan pop-up pada browser agar struk dapat dicetak.');
            return;
        }

        const daftarAlat = (data.alat || [])
            .map(alat => `<li>${escapeHtml(alat)}</li>`)
            .join('');

        const isiStruk = `
            <!DOCTYPE html>
            <html lang="id">
            <head>
                <meta charset="UTF-8">
                <title>Struk Peminjaman</title>

                <style>
                    body {
                        font-family: Arial, sans-serif;
                        padding: 30px;
                        color: #000;
                    }

                    h2, p {
                        text-align: center;
                    }

                    table {
                        width: 100%;
                        border-collapse: collapse;
                        margin-top: 25px;
                    }

                    td {
                        padding: 10px;
                        border: 1px solid #ccc;
                    }

                    .qr {
                        text-align: center;
                        margin-top: 30px;
                    }

                    .qr img {
                        margin: auto;
                    }

                    @media print {
                        body {
                            padding: 10px;
                        }
                    }
                </style>
            </head>

            <body>

                <h2>STRUK PEMINJAMAN ALAT</h2>
                <p>Sistem Informasi Peminjaman Alat</p>

                <hr>

                <table>
                    <tr>
                        <td width="35%">ID Transaksi</td>
                        <td>${escapeHtml(data.id)}</td>
                    </tr>

                    <tr>
                        <td>Nama Peminjam</td>
                        <td>${escapeHtml(data.nama)}</td>
                    </tr>

                    <tr>
                        <td>Tanggal Pinjam</td>
                        <td>${escapeHtml(data.tanggal_pinjam)}</td>
                    </tr>

                    <tr>
                        <td>Tanggal Kembali</td>
                        <td>${escapeHtml(data.tanggal_kembali)}</td>
                    </tr>

                    <tr>
                        <td>Daftar Alat</td>
                        <td><ul>${daftarAlat}</ul></td>
                    </tr>

                    <tr>
                        <td>Status</td>
                        <td>${escapeHtml(data.status)}</td>
                    </tr>
                </table>

                <div class="qr">
                    <h3>QR CODE TRANSAKSI</h3>
                    <div id="qrcode"></div>
                    <p>ID Transaksi: ${escapeHtml(data.id)}</p>
                </div>

            </body>
            </html>
        `;

        jendela.document.open();
        jendela.document.write(isiStruk);
        jendela.document.close();

        const scriptQR = jendela.document.createElement('script');

        scriptQR.src = 'https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js';

        scriptQR.onload = function () {
            new jendela.QRCode(
                jendela.document.getElementById('qrcode'),
                {
                    text: 'ID Transaksi: ' + data.id,
                    width: 150,
                    height: 150
                }
            );

            setTimeout(function () {
                jendela.focus();
                jendela.print();
            }, 500);
        };

        scriptQR.onerror = function () {
            jendela.document.getElementById('qrcode').innerHTML =
                '<p>QR Code tidak dapat dimuat. Periksa koneksi internet.</p>';

            setTimeout(function () {
                jendela.focus();
                jendela.print();
            }, 500);
        };

        jendela.document.body.appendChild(scriptQR);
    }
</script>

</body>
</html>