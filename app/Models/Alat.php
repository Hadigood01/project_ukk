<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alat extends Model
{
    protected $table = 'alat';
    protected $primaryKey = 'id_alat';

    protected $fillable = [
        'nama_alat',
        'id_kategori',
        'stok',
        'kondisi',
        'lokasi',
    ];

    public function kategori()
    {
        return $this->belongsTo(
            Kategori::class,
            'id_kategori',
            'id_kategori'
        );
    }

    public function detailPeminjaman()
    {
        return $this->hasMany(
            DetailPeminjaman::class,
            'id_alat',
            'id_alat'
        );
    }
}
