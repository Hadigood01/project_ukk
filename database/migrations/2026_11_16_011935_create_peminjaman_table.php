
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->increments('id_pinjam');

            $table->unsignedInteger('id_user');
            $table->unsignedInteger('disetujui_oleh')->nullable();

            $table->date('tnggl_pinjam');
            $table->date('tnggl_kembali');

            $table->enum('status', [
                'menunggu',
                'ditolak',
                'dipinjam'
            ])->default('menunggu');

            $table->timestamp('create_at')->useCurrent();

            $table->foreign('id_user')
                ->references('id_user')
                ->on('user')
                ->onDelete('restrict');

            $table->foreign('disetujui_oleh')
                ->references('id_user')
                ->on('user')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};
