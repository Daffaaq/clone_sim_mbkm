<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TSemhasDaftar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_semhas_daftar', function (Blueprint $table) {
            $table->id('semhas_daftar_id');
            $table->unsignedBigInteger('semhas_id')->index()->nullable();
            $table->foreign('semhas_id')->references('semhas_id')->on('m_semhas');
            $table->unsignedBigInteger('magang_id')->index()->nullable();
            $table->foreign('magang_id')->references('magang_id')->on('t_magang');
            $table->unsignedBigInteger('pembimbing_dosen_id')->index()->nullable();
            $table->foreign('pembimbing_dosen_id')->references('pembimbing_dosen_id')->on('t_pembimbing_dosen');
            $table->unsignedBigInteger('instruktur_lapangan_id')->index()->nullable();
            $table->foreign('instruktur_lapangan_id')->references('instruktur_lapangan_id')->on('t_instruktur_lapangan');
            $table->unsignedBigInteger('dosen_pembahas_id')->index()->nullable();
            $table->foreign('dosen_pembahas_id')->references('dosen_id')->on('m_dosen');
            $table->string('Judul');
            $table->date('tanggal_daftar');
            $table->string('link_github');
            $table->string('link_laporan');
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
