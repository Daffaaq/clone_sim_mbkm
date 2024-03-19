<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class MMahasiswa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //make table m_mahasiswa
        Schema::create('m_mahasiswa', function (Blueprint $table) {
            $table->id('mahasiswa_id');
            $table->unsignedBigInteger('prodi_id')->index();
            $table->unsignedBigInteger('user_id')->index();
            $table->string('nim', 15)->unique();
            $table->string('nama_mahasiswa');
            $table->string('email_mahasiswa')->unique()->nullable();
            $table->string('no_hp', 15)->nullable();
            $table->integer('jenis_kelamin')->comment('0: Perempuan, 1: Laki-laki')->nullable();
            $table->string('kelas');
            $table->string('nama_ortu')->nullable();
            $table->string('hp_ortu', 15)->nullable();
            $table->dateTime('created_at')->nullable()->useCurrent();
            $table->integer('created_by')->nullable()->index();
            $table->dateTime('updated_at')->nullable();
            $table->integer('updated_by')->nullable()->index();
            $table->dateTime('deleted_at')->nullable()->index();
            $table->integer('deleted_by')->nullable()->index();

            $table->foreign('prodi_id')->references('prodi_id')->on('m_prodi');
            $table->foreign('user_id')->references('user_id')->on('s_user');
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
