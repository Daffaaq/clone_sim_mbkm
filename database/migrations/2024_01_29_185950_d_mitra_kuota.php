<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DMitraKuota extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('d_mitra_kuota', function (Blueprint $table) {
            $table->id('mitra_kuota_id');
            $table->unsignedBigInteger('mitra_id');
            $table->foreign('mitra_id')->references('mitra_id')->on('d_mitra');
            $table->unsignedBigInteger('prodi_id');
            $table->foreign('prodi_id')->references('prodi_id')->on('m_prodi');
            $table->tinyInteger('kuota');
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
        Schema::dropIfExists('d_mitra_kuota');
    }
}
