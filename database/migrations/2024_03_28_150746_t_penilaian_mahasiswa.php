<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TPenilaianMahasiswa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_penilaian_mahasiswa', function (Blueprint $table) {
            $table->id('penilaian_mahasiswa_id');
            $table->unsignedBigInteger('mahasiswa_id')->index()->nullable();
            $table->foreign('mahasiswa_id')->references('mahasiswa_id')->on('m_mahasiswa');
            $table->unsignedBigInteger('pembimbing_dosen_id')->index()->nullable();
            $table->foreign('pembimbing_dosen_id')->references('pembimbing_dosen_id')->on('t_pembimbing_dosen');
            $table->unsignedBigInteger('instruktur_lapangan_id')->index()->nullable();
            $table->foreign('instruktur_lapangan_id')->references('instruktur_lapangan_id')->on('t_instruktur_lapangan');
            $table->unsignedBigInteger('periode_id');
            $table->foreign('periode_id')->references('periode_id')->on('m_periode');
            $table->longText('komentar_dosen_pembimbing')->nullable();
            $table->longText('komentar_instruktur_lapangan')->nullable();
            $table->enum('nilai_dosen_pembimbing', ['Baik Sekali', 'Baik', 'Cukup', 'Kurang', 'Kurang Sekali'])->nullable();
            $table->enum('nilai_instruktur_lapangan', ['Baik Sekali', 'Baik', 'Cukup', 'Kurang', 'Kurang Sekali'])->nullable();
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
