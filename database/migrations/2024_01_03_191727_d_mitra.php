<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DMitra extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('d_mitra', function (Blueprint $table) {
            $table->id('mitra_id');
            $table->unsignedBigInteger('kegiatan_id');
            $table->foreign('kegiatan_id')->references('kegiatan_id')->on('m_kegiatan');
            $table->unsignedBigInteger('periode_id');
            $table->foreign('periode_id')->references('periode_id')->on('m_periode');
            $table->string('mitra_prodi');
            $table->string('mitra_nama', 100);
            $table->string('mitra_alamat');
            $table->tinyInteger('mitra_durasi');
            $table->integer('provinsi_id');
            $table->integer('kota_id');
            $table->string('mitra_website', 100)->nullable();
            $table->text('mitra_deskripsi');
            $table->string('mitra_flyer')->nullable();
            $table->text('mitra_skema');
            $table->tinyInteger('status')->default(0)->comment('0: Pending, 1: Diterima, 2: Ditolak');
            $table->text('mitra_keterangan_ditolak')->nullable();
            $table->date('mitra_batas_pendaftaran');
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
        Schema::dropIfExists('d_mitra');
    }
}
