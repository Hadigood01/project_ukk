<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengembalian extends Model
{
    protected $table = 'pengembalian';

    protected $primaryKey = 'id_kembali';

    public $timestamps = false;

    protected $fillable = [
        'id_pinjam',
        'tanggal_kembali',
        'kondisi_alat',
        'denda',
        'diterima_oleh',
        'status',
    ];

    public function peminjaman()
    {
        return $this->belongsTo(
            Peminjaman::class,
            'id_pinjam',
            'id_pinjam'
        );
    }

    public function petugas()
    {
        return $this->belongsTo(
            User::class,
            'diterima_oleh',
            'id_user'
        );
    }
}
