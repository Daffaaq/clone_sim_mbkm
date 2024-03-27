<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TambahTglPelaksanaanTMagang extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_magang', function (Blueprint $table) {
            $table->date('magang_tgl_awal_pelaksanaan')->nullable()->after('status');
            $table->date('magang_tgl_akhir_pelaksanaan')->nullable()->after('magang_tgl_awal_pelaksanaan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_magang', function (Blueprint $table) {
            $table->dropColumn('magang_tgl_awal_pelaksanaan');
            $table->dropColumn('magang_tgl_akhir_pelaksanaan');
        });
    }
}
