<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MSemhas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_semhas', function (Blueprint $table) {
            $table->id('semhas_id');
            $table->unsignedBigInteger('prodi_id');
            $table->foreign('prodi_id')->references('prodi_id')->on('m_prodi');
            $table->string('judul_semhas');
            $table->tinyInteger('gelombang');
            $table->tinyInteger('kuota_bimbingan');
            $table->date('tanggal_mulai_pendaftaran');
            $table->date('tanggal_akhir_pendaftaran');
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
