<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TRevisiInstrukturLapangan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_revisi_instruktur_lapangan', function (Blueprint $table) {
            $table->id('revisi_instruktur_lapangan_id');
            $table->longText('saran_instruktur_lapangan');
            $table->longText('catatan_instruktur_lapangan');
            $table->unsignedBigInteger('periode_id');
            $table->foreign('periode_id')->references('periode_id')->on('m_periode');
            $table->unsignedBigInteger('semhas_daftar_id')->index();
            $table->foreign('semhas_daftar_id')->references('semhas_daftar_id')->on('t_semhas_daftar');
            $table->dateTime('created_at')->nullable()->useCurrent();
            $table->integer('created_by')->nullable()->index();
            $table->dateTime('updated_at')->nullable();
            $table->integer('updated_by')->nullable()->index();
            $table->dateTime('deleted_at')->nullable()->index();
            $table->integer('deleted_by')->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
