<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TLogBimbingan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_log_bimbingan', function (Blueprint $table) {
            $table->id('log_bimbingan_id');
            $table->unsignedBigInteger('pembimbing_dosen_id')->index();
            $table->foreign('pembimbing_dosen_id')->references('pembimbing_dosen_id')->on('t_pembimbing_dosen');
            $table->unsignedBigInteger('instruktur_lapangan_id')->index();
            $table->foreign('instruktur_lapangan_id')->references('instruktur_lapangan_id')->on('t_instruktur_lapangan');
            $table->tinyInteger('status1')->comment('0: Menunggu, 1: Diterima, 2: Ditolak');
            $table->tinyInteger('status2')->comment('0: Menunggu, 1: Diterima, 2: Ditolak');
            $table->date('tanggal');
            $table->longText('topik_bimbingan');
            $table->string('foto');
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->decimal('nilai_pembimbing_dosen')->nullable();
            $table->decimal('nilai_instruktur_lapangan')->nullable();;
            $table->date('tanggal_status_dosen')->nullable();;
            $table->date('tanggal_status_instruktur')->nullable();;
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
