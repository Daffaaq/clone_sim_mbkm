<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('s_user', function (Blueprint $table) {
            $table->id('user_id');
            $table->unsignedBigInteger('group_id')->index()->nullable();
            $table->unsignedBigInteger('prodi_id')->index()->nullable();
            $table->string('username', 100)->unique();
            $table->string('email', 100)->nullable()->unique();
            $table->string('name', 100)->nullable();
            $table->string('password');
            $table->string('avatar_url')->nullable();
            $table->string('avatar_dir')->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->dateTime('created_at')->nullable()->useCurrent();
            $table->integer('created_by')->nullable()->index();
            $table->dateTime('updated_at')->nullable();
            $table->integer('updated_by')->nullable()->index();
            $table->dateTime('deleted_at')->nullable()->index();
            $table->integer('deleted_by')->nullable()->index();

            $table->foreign('group_id')->references('group_id')->on('s_group');
            $table->foreign('prodi_id')->references('prodi_id')->on('m_prodi');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('s_user');
    }
}
