<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TJadwalSemhas extends Migration
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
            $table->date('tanggal_sidang');
            $table->time('jam_sidang_mulai');
            $table->time('jam_sidang_selesai');
            $table->string('lokasi')->nullable(); // Misalnya untuk link Zoom
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
