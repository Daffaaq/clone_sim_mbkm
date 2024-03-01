<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TMagang extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_magang', function (Blueprint $table) {
            $table->id('magang_id');
            $table->unsignedBigInteger('mahasiswa_id');
            $table->foreign('mahasiswa_id')->references('mahasiswa_id')->on('m_mahasiswa');
            $table->unsignedBigInteger('mitra_id');
            $table->foreign('mitra_id')->references('mitra_id')->on('d_mitra');
            $table->unsignedBigInteger('periode_id');
            $table->foreign('periode_id')->references('periode_id')->on('m_periode');
            $table->unsignedBigInteger('prodi_id');
            $table->foreign('prodi_id')->references('prodi_id')->on('m_prodi');
            $table->string('magang_kode');
            $table->string('magang_skema');
            $table->tinyInteger('magang_tipe')->comment('0: Ketua, 1: Anggota, 2:Individu');
            $table->tinyInteger('is_accept')->comment('0: Menunggu, 1: Menerima ajakan, 2: Menolak ajakan')->nullable()->default(0);
            $table->tinyInteger('status')->comment('0: Menunggu, 1: Diterima, 2: Ditolak');
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
        Schema::dropIfExists('t_magang');
    }
}
