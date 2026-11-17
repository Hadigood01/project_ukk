
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Login Peminjaman Alat Komputer</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>

<body class="bg-light">

<div class="container">
    <div class="row justify-content-center align-items-center"
         style="min-height:100vh">

        <div class="col-md-5">

            <div class="card shadow border-0">
                <div class="card-body p-4">

                    <h3 class="text-center">
                        Peminjaman Alat Komputer
                    </h3>

                    <p class="text-center text-muted">
                        Silakan Login
                    </p>

                    {{-- Pesan berhasil --}}
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    {{-- Pesan kesalahan --}}
                    @if($errors->any())
                        <div class="alert alert-danger">
                            {{ $errors->first() }}
                        </div>
                    @endif

                    {{-- Form Login --}}
                    <form action="{{ route('login.proses') }}"
                          method="POST">

                        @csrf

                        <div class="mb-3">
                            <label for="email">Email</label>
                            <input
                                type="email"
                                name="email"
                                id="email"
                                class="form-control"
                                placeholder="Masukkan email"
                                value="{{ old('email') }}"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label for="password">Password</label>
                            <input
                                type="password"
                                name="password"
                                id="password"
                                class="form-control"
                                placeholder="Masukkan password"
                                required
                            >
                        </div>

                        <button type="submit"
                                class="btn btn-primary w-100">
                            Login
                        </button>

                    </form>

                    <hr>

                    {{-- Link Register --}}
                    <div class="text-center">
                        <p class="mb-2">
                            Belum punya akun?
                        </p>

                        <a href="{{ route('register') }}"
                           class="btn btn-outline-primary w-100">
                            Register / Daftar Akun
                        </a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
