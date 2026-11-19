<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class LogAktivitas extends Model
{
    protected $table = 'log_aktivitas';

    // Sesuai dengan $table->id() pada migration
    protected $primaryKey = 'id';

    protected $fillable = [
        'id_user',
        'aktivitas',
    ];

    public function user()
    {
        return $this->belongsTo(
            User::class,
            'id_user',
            'id_user'
        );
    }
}
