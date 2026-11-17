<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPeminjaman extends Model
{
    protected $table = 'detail_peminjaman';
    protected $primaryKey = 'id_detail';

    public $timestamps = false;

    protected $fillable = [
        'id_pinjam',
        'id_alat',
        'jumlah',
    ];

    public function peminjaman()
    {
        return $this->belongsTo(
            Peminjaman::class,
            'id_pinjam',
            'id_pinjam'
        );
    }

    public function alat()
    {
        return $this->belongsTo(
            Alat::class,
            'id_alat',
            'id_alat'
        );
    }
}
