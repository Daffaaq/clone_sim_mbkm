<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TNilaiInstrukturLapangan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_nilai_instruktur_lapangan', function (Blueprint $table) {
            $table->id('t_nilai_instruktur_lapangan_id');
            $table->decimal('nilai')->nullable();
            $table->unsignedBigInteger('periode_id');
            $table->foreign('periode_id')->references('periode_id')->on('m_periode');
            $table->unsignedBigInteger('nilai_instruktur_lapangan_id');
            $table->foreign('nilai_instruktur_lapangan_id')->references('nilai_instruktur_lapangan_id')->on('m_nilai_instruktur_lapangan');
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
