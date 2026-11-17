
@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">Edit Data User</h4>
            <p class="text-muted mb-0">Perbarui informasi pengguna.</p>
        </div>

        <a href="{{ route('admin.user.index') }}"
           class="btn btn-secondary">
            Kembali
        </a>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('admin.user.update', $user->id_user) }}"
                  method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Nama User</label>
                    <input type="text"
                           name="nama_user"
                           class="form-control"
                           value="{{ old('nama_user', $user->nama_user) }}"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email"
                           name="email"
                           class="form-control"
                           value="{{ old('email', $user->email) }}"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text"
                           name="username"
                           class="form-control"
                           value="{{ old('username', $user->username) }}"
                           required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Level User</label>
                    <select name="level" class="form-select" required>
                        <option value="admin"
                            {{ old('level', $user->level) == 'admin' ? 'selected' : '' }}>
                            Admin
                        </option>
                        <option value="petugas"
                            {{ old('level', $user->level) == 'petugas' ? 'selected' : '' }}>
                            Petugas
                        </option>
                        <option value="peminjaman"
                            {{ old('level', $user->level) == 'peminjaman' ? 'selected' : '' }}>
                            Peminjam
                        </option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Password Baru</label>
                    <input type="password"
                           name="password"
                           class="form-control"
                           placeholder="Kosongkan jika tidak ingin mengubah password">
                    <small class="text-muted">
                        Isi hanya jika ingin mengganti password.
                    </small>
                </div>

                <button type="submit" class="btn btn-primary">
                    Simpan Perubahan
                </button>

                <a href="{{ route('admin.user.index') }}"
                   class="btn btn-light">
                    Batal
                </a>
            </form>

        </div>
    </div>
</div>
@endsection
