
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi Transaksi</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Notifikasi Transaksi</h3>

        <a href="javascript:history.back()" class="btn btn-secondary">
            Kembali
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('notifikasi.bacaSemua') }}" method="POST" class="mb-3">
        @csrf
        <button type="submit" class="btn btn-primary btn-sm">
            Tandai Semua Sudah Dibaca
        </button>
    </form>

    @forelse($notifikasi as $item)
        <div class="card mb-3 shadow-sm">
            <div class="card-body">

                <h6>
                    {{ $item->data['judul'] ?? 'Notifikasi Transaksi' }}
                </h6>

                <p class="mb-2">
                    {{ $item->data['pesan'] ?? 'Ada informasi transaksi baru.' }}
                </p>

                <small class="text-muted">
                    {{ $item->created_at->format('d-m-Y H:i') }}
                </small>

                @if(is_null($item->read_at))
                    <span class="badge bg-warning text-dark ms-2">
                        Belum Dibaca
                    </span>

                    <form action="{{ route('notifikasi.baca', $item->id) }}"
                          method="POST" class="mt-2">
                        @csrf
                        <button type="submit" class="btn btn-success btn-sm">
                            Tandai Sudah Dibaca
                        </button>
                    </form>
                @else
                    <span class="badge bg-success ms-2">
                        Sudah Dibaca
                    </span>
                @endif

            </div>
        </div>
    @empty
        <div class="alert alert-info">
            Belum ada notifikasi transaksi.
        </div>
    @endforelse

    {{ $notifikasi->links() }}

</div>

</body>
</html>
