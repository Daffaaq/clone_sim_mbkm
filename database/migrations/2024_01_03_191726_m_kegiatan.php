<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MKegiatan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_kegiatan', function (Blueprint $table) {
            $table->id('kegiatan_id');
            $table->unsignedBigInteger('program_id');
            $table->foreign('program_id')->references('program_id')->on('m_program');
            $table->string('kegiatan_kode', 10)->unique();
            $table->string('kegiatan_nama', 100);
            $table->enum('kegiatan_skema', ['S', 'C', 'M']);
            $table->text('kegiatan_deskripsi');
            $table->tinyInteger('is_kuota')->comment('0: tidak ada kuota/bebas, 1: berkuota, detail quota ada di tabel d_kegiatan_kuota');
            $table->tinyInteger('is_mandiri')->default(0)->comment('0: Bukan pengajuan mandiri, 1: Pengajuan mandiri');
            $table->tinyInteger('is_submit_proposal')->default(0)->comment('0: tidak perlu submit proposal, 1: perlu submit proposal');

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
        Schema::dropIfExists('m_kegiatan');
    }
}
