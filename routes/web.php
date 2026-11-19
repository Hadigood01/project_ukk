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

// LOGIN
Route::get('/', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'prosesLogin'])->name('login.proses');

// REGISTER
Route::get('/register', [AuthController::class, 'register'])->name('register');
Route::post('/register', [AuthController::class, 'prosesRegister'])->name('register.proses');

// LOGOUT
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// NOTIFIKASI
Route::middleware('auth')
    ->prefix('notifikasi')
    ->name('notifikasi.')
    ->group(function () {
        Route::get('/', [NotifikasiController::class, 'index'])->name('index');
        Route::post('/baca-semua', [NotifikasiController::class, 'bacaSemua'])->name('bacaSemua');
        Route::post('/{id}/baca', [NotifikasiController::class, 'baca'])->name('baca');
    });

// ADMIN
Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');

        Route::resource('user', UserController::class);
        Route::resource('kategori', KategoriController::class);
        Route::resource('alat', AlatController::class);

        Route::get('/peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
        Route::get('/peminjaman/{id}/edit', [PeminjamanController::class, 'edit'])->name('peminjaman.edit');
        Route::put('/peminjaman/{id}', [PeminjamanController::class, 'update'])->name('peminjaman.update');
        Route::delete('/peminjaman/{id}', [PeminjamanController::class, 'destroy'])->name('peminjaman.destroy');

        Route::get('/pengembalian', [PengembalianController::class, 'index'])->name('pengembalian.index');
        Route::get('/pengembalian/{id}', [PengembalianController::class, 'show'])->name('pengembalian.show');

        Route::get('/log', [LogAktivitasController::class, 'index'])->name('log.index');
        Route::delete('/log/{id}', [LogAktivitasController::class, 'destroy'])->name('log.destroy');
    });

// PETUGAS
Route::middleware(['auth', 'role:petugas'])
    ->prefix('petugas')
    ->name('petugas.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'petugas'])->name('dashboard');

        Route::get('/alat', [AlatController::class, 'index'])->name('alat.index');

        // PEMINJAMAN
        Route::get('/peminjaman', [PeminjamanController::class, 'persetujuan'])
            ->name('peminjaman.index');

        Route::post('/peminjaman/{id}/setujui', [PeminjamanController::class, 'setujui'])
            ->name('peminjaman.setujui');

        Route::post('/peminjaman/{id}/tolak', [PeminjamanController::class, 'tolak'])
            ->name('peminjaman.tolak');

        // PETUGAS MENGATUR BATAS PENGEMBALIAN
        Route::post('/peminjaman/{id}/atur-batas', [PengembalianController::class, 'aturBatas'])
            ->name('peminjaman.aturBatas');

        // PENGEMBALIAN
        Route::get('/pengembalian', [PengembalianController::class, 'monitor'])
            ->name('pengembalian.index');

        Route::post('/pengembalian/{id}', [PengembalianController::class, 'store'])
            ->name('pengembalian.store');

        // LAPORAN
        Route::get('/laporan', [LaporanController::class, 'index'])
            ->name('laporan.index');

        Route::get('/laporan/pengembalian', [LaporanController::class, 'pengembalian'])
            ->name('laporan.pengembalian');
    });

// PEMINJAM
Route::middleware(['auth', 'role:peminjaman'])
    ->prefix('peminjam')
    ->name('peminjaman.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'peminjaman'])
            ->name('dashboard');

        Route::get('/alat', [AlatController::class, 'daftar'])
            ->name('alat.index');

        Route::get('/peminjaman', [PeminjamanController::class, 'create'])
            ->name('create');

        Route::post('/peminjaman', [PeminjamanController::class, 'store'])
            ->name('store');

        Route::get('/riwayat', [PeminjamanController::class, 'riwayat'])
            ->name('riwayat');

        Route::get('/pengembalian', [PengembalianController::class, 'pengembalianSaya'])
            ->name('pengembalian.index');

        Route::get('/pengembalian/{id}/ajukan', [
            PengembalianController::class,
            'formAjukanPengembalian'
        ])->name('pengembalian.form');

        Route::post('/pengembalian/{id}', [
            PengembalianController::class,
            'ajukanPengembalian'
        ])->name('pengembalian.ajukan');
    });