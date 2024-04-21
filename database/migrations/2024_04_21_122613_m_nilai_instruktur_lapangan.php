<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MNilaiInstrukturLapangan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_nilai_instruktur_lapangan', function (Blueprint $table) {
            $table->id('nilai_instruktur_lapangan_id');
            $table->longText('name_kriteria_instruktur_lapangan');
            $table->decimal('bobot')->nullable();
            $table->unsignedBigInteger('parent_id')->index()->nullable();
            $table->foreign('parent_id')->references('nilai_instruktur_lapangan_id')->on('m_nilai_instruktur_lapangan');
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
