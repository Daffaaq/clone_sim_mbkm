<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MNilaiPembahasDosen extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_nilai_pembahas_dosen', function (Blueprint $table) {
            $table->id('nilai_pembahas_dosen_id');
            $table->longText('name_kriteria_pembahas_dosen');
            $table->decimal('bobot')->nullable();
            $table->unsignedBigInteger('parent_id')->index()->nullable();
            $table->foreign('parent_id')->references('nilai_pembahas_dosen_id')->on('m_nilai_pembahas_dosen');
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
