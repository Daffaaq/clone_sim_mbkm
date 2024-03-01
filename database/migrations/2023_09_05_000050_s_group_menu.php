<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SGroupMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('s_group_menu', function (Blueprint $table) {
            $table->id('group_menu_id');
            $table->unsignedBigInteger('group_id')->index();
            $table->unsignedBigInteger('menu_id')->index();
            $table->tinyInteger('c')->default(0);
            $table->tinyInteger('r')->default(0);
            $table->tinyInteger('u')->default(0);
            $table->tinyInteger('d')->default(0);
            $table->dateTime('created_at')->nullable()->useCurrent();
            $table->integer('created_by')->nullable()->index();
            $table->dateTime('updated_at')->nullable();
            $table->integer('updated_by')->nullable()->index();
            $table->dateTime('deleted_at')->nullable()->index();
            $table->integer('deleted_by')->nullable()->index();

            $table->foreign('group_id')->references('group_id')->on('s_group');
            $table->foreign('menu_id')->references('menu_id')->on('s_menu');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('s_group_menu');
    }
}
