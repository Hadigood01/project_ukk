
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pengembalian', function (Blueprint $table) {
            $table->string('status', 20)
                ->default('diterima')
                ->after('denda');

            $table->date('tanggal_kembali')
                ->nullable()
                ->change();

            $table->string('kondisi_alat', 100)
                ->nullable()
                ->change();

            $table->unsignedInteger('diterima_oleh')
                ->nullable()
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('pengembalian', function (Blueprint $table) {
            $table->dropColumn('status');

            $table->date('tanggal_kembali')
                ->nullable(false)
                ->change();

            $table->string('kondisi_alat', 100)
                ->nullable(false)
                ->change();

            $table->unsignedInteger('diterima_oleh')
                ->nullable(false)
                ->change();
        });
    }
};
