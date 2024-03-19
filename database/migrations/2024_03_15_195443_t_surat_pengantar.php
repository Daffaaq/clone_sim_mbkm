<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class TSuratPengantar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_surat_pengantar', function (Blueprint $table) {
            $table->id('surat_pengantar_id');
            $table->string('surat_pengantar_no', 50);
            $table->string('magang_kode', 50);
            $table->text('surat_pengantar_alamat_mitra');
            $table->string('surat_pengantar_awal_pelaksanaan', 50);
            $table->string('surat_pengantar_akhir_pelaksanaan', 50);
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
        Schema::dropIfExists('t_surat_pengantar');
    }
}
