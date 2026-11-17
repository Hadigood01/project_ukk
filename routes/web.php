
<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AlatController;
use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\PengembalianController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\LogAktivitasController;
use App\Http\Controllers\NotifikasiController;


// ========================================
// LOGIN
// ========================================

Route::get('/', [AuthController::class, 'login'])
    ->name('login');

Route::post('/login', [AuthController::class, 'prosesLogin'])
    ->name('login.proses');


// ========================================
// REGISTER
// ========================================

Route::get('/register', [AuthController::class, 'register'])
    ->name('register');

Route::post('/register', [AuthController::class, 'prosesRegister'])
    ->name('register.proses');


// ========================================
// LOGOUT
// ========================================

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');


// ========================================
// NOTIFIKASI
// ========================================

Route::middleware('auth')
    ->prefix('notifikasi')
    ->name('notifikasi.')
    ->group(function () {

        Route::get('/', [NotifikasiController::class, 'index'])
            ->name('index');

        Route::post('/baca-semua', [NotifikasiController::class, 'bacaSemua'])
            ->name('bacaSemua');

        Route::post('/{id}/baca', [NotifikasiController::class, 'baca'])
            ->name('baca');
    });


// ========================================
// ADMIN
// ========================================

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'admin'])
            ->name('dashboard');

        // Manajemen pengguna
        Route::resource('user', UserController::class);

        // Manajemen kategori
        Route::resource('kategori', KategoriController::class);

        // Manajemen alat
        Route::resource('alat', AlatController::class);

        // Manajemen peminjaman
        Route::get('/peminjaman', [PeminjamanController::class, 'index'])
            ->name('peminjaman.index');

        Route::get('/peminjaman/{id}/edit', [PeminjamanController::class, 'edit'])
            ->name('peminjaman.edit');

        Route::put('/peminjaman/{id}', [PeminjamanController::class, 'update'])
            ->name('peminjaman.update');

        Route::delete('/peminjaman/{id}', [PeminjamanController::class, 'destroy'])
            ->name('peminjaman.destroy');

        // Riwayat pengembalian
        Route::get('/pengembalian', [PengembalianController::class, 'index'])
            ->name('pengembalian.index');

        Route::get('/pengembalian/{id}', [PengembalianController::class, 'show'])
            ->name('pengembalian.show');

        // Log aktivitas
        Route::get('/log', [LogAktivitasController::class, 'index'])
            ->name('log.index');
    });


// ========================================
// PETUGAS
// ========================================

Route::middleware(['auth', 'role:petugas'])
    ->prefix('petugas')
    ->name('petugas.')
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'petugas'])
            ->name('dashboard');

        // Daftar pengajuan peminjaman
        Route::get('/peminjaman', [PeminjamanController::class, 'persetujuan'])
            ->name('peminjaman.index');

        // Menyetujui peminjaman
        Route::post('/peminjaman/{id}/setujui', [PeminjamanController::class, 'setujui'])
            ->name('peminjaman.setujui');

        // Menolak peminjaman
        Route::post('/peminjaman/{id}/tolak', [PeminjamanController::class, 'tolak'])
            ->name('peminjaman.tolak');

        // Daftar alat yang harus dikembalikan
        Route::get('/pengembalian', [PengembalianController::class, 'monitor'])
            ->name('pengembalian.index');

        // Memproses pengembalian
        Route::post('/pengembalian/{id}', [PengembalianController::class, 'store'])
            ->name('pengembalian.store');

        // Laporan peminjaman
        Route::get('/laporan', [LaporanController::class, 'index'])
            ->name('laporan.index');

        // Laporan pengembalian
        Route::get('/laporan/pengembalian', [LaporanController::class, 'pengembalian'])
            ->name('laporan.pengembalian');
    });



// ========================================
// PEMINJAM
// ========================================

Route::middleware(['auth', 'role:peminjaman'])
    ->prefix('peminjam')
    ->name('peminjaman.')
    ->group(function () {

        // Dashboard peminjam
        Route::get('/dashboard', [DashboardController::class, 'peminjaman'])
            ->name('dashboard');

        // Daftar alat
        Route::get('/alat', [AlatController::class, 'index'])
            ->name('alat.index');

        // Form pengajuan peminjaman
        Route::get('/peminjaman', [PeminjamanController::class, 'create'])
            ->name('create');

        // Proses pengajuan peminjaman
        Route::post('/peminjaman', [PeminjamanController::class, 'store'])
            ->name('store');

        // Riwayat peminjaman
        Route::get('/riwayat', [PeminjamanController::class, 'riwayat'])
            ->name('riwayat');

        // Daftar pengembalian milik peminjam
        Route::get('/pengembalian', [PengembalianController::class, 'pengembalianSaya'])
            ->name('pengembalian.index');

        // Form pengajuan pengembalian
        Route::get('/pengembalian/{id}/ajukan', [
            PengembalianController::class,
            'formAjukanPengembalian'
        ])->name('pengembalian.form');

        // Proses pengajuan pengembalian
        Route::post('/pengembalian/{id}', [
            PengembalianController::class,
            'ajukanPengembalian'
        ])->name('pengembalian.ajukan');
    });

