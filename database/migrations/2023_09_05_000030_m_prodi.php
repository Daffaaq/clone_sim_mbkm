<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MProdi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_prodi', function (Blueprint $table) {
            $table->id('prodi_id');
            $table->unsignedBigInteger('jurusan_id')->index();
            $table->string('prodi_code', 10)->unique();
            $table->string('prodi_name', 100)->unique();
            $table->tinyInteger('is_active')->default(0);
            $table->dateTime('created_at')->nullable()->useCurrent();
            $table->integer('created_by')->nullable()->index();
            $table->dateTime('updated_at')->nullable();
            $table->integer('updated_by')->nullable()->index();
            $table->dateTime('deleted_at')->nullable()->index();
            $table->integer('deleted_by')->nullable()->index();

            $table->foreign('jurusan_id')->references('jurusan_id')->on('m_jurusan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('m_prodi');
    }
}
