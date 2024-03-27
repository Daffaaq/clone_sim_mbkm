<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class HapusKolomSuratPengantar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // hapus kolom surat pengantar
        Schema::table('t_surat_pengantar', function (Blueprint $table) {
            $table->dropColumn('surat_pengantar_alamat_mitra');
            $table->dropColumn('surat_pengantar_awal_pelaksanaan');
            $table->dropColumn('surat_pengantar_akhir_pelaksanaan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // tambah kolom surat pengantar
        Schema::table('t_surat_pengantar', function (Blueprint $table) {
            $table->text('surat_pengantar_alamat_mitra');
            $table->string('surat_pengantar_awal_pelaksanaan', 50);
            $table->string('surat_pengantar_akhir_pelaksanaan', 50);
        });
    }
}
