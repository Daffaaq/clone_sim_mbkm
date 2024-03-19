<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TDokumenMagang extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_dokumen_magang', function (Blueprint $table) {
            $table->id('dokumen_magang_id');
            $table->unsignedBigInteger('mahasiswa_id');
            $table->foreign('mahasiswa_id')->references('mahasiswa_id')->on('m_mahasiswa');
            $table->unsignedBigInteger('magang_id');
            $table->foreign('magang_id')->references('magang_id')->on('t_magang');
            $table->string('dokumen_magang_nama');
            $table->tinyInteger('dokumen_magang_tipe')->comment('1=diterima,2=ditolak|untuk surat balasan');
            $table->string('dokumen_magang_file');
            $table->tinyInteger('dokumen_magang_status')->nullable();
            $table->text('dokumen_magang_keterangan')->nullable();
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
        Schema::dropIfExists('t_dokumen_magang');
    }
}
