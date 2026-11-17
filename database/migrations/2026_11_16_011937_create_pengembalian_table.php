
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengembalian', function (Blueprint $table) {
            $table->increments('id_kembali');

            $table->unsignedInteger('id_pinjam')->unique();

            $table->date('tanggal_kembali');
            $table->string('kondisi_alat', 100);
            $table->unsignedInteger('denda')->default(0);

            $table->unsignedInteger('diterima_oleh');

            $table->foreign('id_pinjam')
                ->references('id_pinjam')
                ->on('peminjaman')
                ->onDelete('restrict');

            $table->foreign('diterima_oleh')
                ->references('id_user')
                ->on('user')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengembalian');
    }
};
