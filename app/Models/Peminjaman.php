<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    protected $table = 'peminjaman';

    protected $primaryKey = 'id_pinjam';

    const CREATED_AT = 'create_at';
    const UPDATED_AT = null;

    protected $fillable = [
        'id_user',
        'disetujui_oleh',
        'tnggl_pinjam',
        'tnggl_kembali',
        'tanggal_kembali_petugas',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh', 'id_user');
    }

    public function detail()
    {
        return $this->hasMany(DetailPeminjaman::class, 'id_pinjam', 'id_pinjam');
    }

    public function pengembalian()
    {
        return $this->hasOne(Pengembalian::class, 'id_pinjam', 'id_pinjam');
    }
}