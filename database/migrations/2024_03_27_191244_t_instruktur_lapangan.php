<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TInstrukturLapangan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_instruktur_lapangan', function (Blueprint $table) {
            $table->id('instruktur_lapangan_id');
            $table->unsignedBigInteger('magang_id')->index();
            $table->foreign('magang_id')->references('magang_id')->on('t_magang');
            $table->unsignedBigInteger('mahasiswa_id')->index();
            $table->foreign('mahasiswa_id')->references('mahasiswa_id')->on('m_mahasiswa');
            $table->unsignedBigInteger('instruktur_id')->index();
            $table->foreign('instruktur_id')->references('instruktur_id')->on('m_instruktur');
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
