<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TransaksiNotification extends Notification
{
    use Queueable;

    protected $judul;
    protected $pesan;
    protected $jenis;
    protected $idTransaksi;

    public function __construct(
        string $judul,
        string $pesan,
        string $jenis,
        ?int $idTransaksi = null
    ) {
        $this->judul = $judul;
        $this->pesan = $pesan;
        $this->jenis = $jenis;
        $this->idTransaksi = $idTransaksi;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'judul' => $this->judul,
            'pesan' => $this->pesan,
            'jenis' => $this->jenis,
            'id_transaksi' => $this->idTransaksi,
        ];
    }
}
