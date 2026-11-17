
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_peminjaman', function (Blueprint $table) {
            $table->increments('id_detail');

            $table->unsignedInteger('id_pinjam');
            $table->unsignedInteger('id_alat');

            $table->unsignedInteger('jumlah');

            $table->foreign('id_pinjam')
                ->references('id_pinjam')
                ->on('peminjaman')
                ->onDelete('cascade');

            $table->foreign('id_alat')
                ->references('id_alat')
                ->on('alat')
                ->onDelete('restrict');

            $table->unique(['id_pinjam', 'id_alat']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_peminjaman');
    }
};
