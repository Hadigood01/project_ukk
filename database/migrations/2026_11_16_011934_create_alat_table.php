
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alat', function (Blueprint $table) {
            $table->increments('id_alat');
            $table->string('nama_alat', 100);

            $table->unsignedInteger('id_kategori');

            $table->unsignedInteger('stok');
            $table->string('kondisi', 100);
            $table->string('lokasi', 100);

            $table->timestamps();

            $table->foreign('id_kategori')
                ->references('id_kategori')
                ->on('kategori')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alat');
    }
};
