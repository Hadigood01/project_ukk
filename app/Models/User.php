<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'user';

    protected $primaryKey = 'id_user';

    protected $fillable = [
        'nama_user',
        'email',
        'username',
        'password',
        'level',
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * Menentukan kolom password untuk autentikasi.
     */
    public function getAuthPasswordName()
    {
        return 'password';
    }

    /**
     * Relasi pengguna dengan data peminjaman.
     */
    public function peminjaman(): HasMany
    {
        return $this->hasMany(
            Peminjaman::class,
            'id_user',
            'id_user'
        );
    }

    /**
     * Relasi pengguna dengan log aktivitas.
     */
    public function logAktivitas(): HasMany
    {
        return $this->hasMany(
            LogAktivitas::class,
            'id_user',
            'id_user'
        );
    }
}
