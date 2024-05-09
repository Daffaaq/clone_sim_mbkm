<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TJadwalSidangMagang extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_jadwal_sidang_magang', function (Blueprint $table) {
            $table->id('jadwal_sidang_magang_id');
            $table->unsignedBigInteger('semhas_daftar_id')->index();
            $table->foreign('semhas_daftar_id')->references('semhas_daftar_id')->on('t_semhas_daftar');
            $table->unsignedBigInteger('periode_id');
            $table->foreign('periode_id')->references('periode_id')->on('m_periode');
            $table->date('tanggal_sidang');
            $table->time('jam_sidang_mulai');
            $table->time('jam_sidang_selesai');
            $table->enum('jenis_sidang', ['online', 'offline']);
            $table->string('tempat')->nullable(); // Misalnya untuk link Zoom
            $table->string('gedung')->nullable(); // Misalnya untuk link Zoom
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
