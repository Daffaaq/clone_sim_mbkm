<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SMenu extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('s_menu', function (Blueprint $table) {
            $table->id('menu_id');
            $table->string('menu_scope', 30)->nullable();
            $table->string('menu_code', 50)->unique();
            $table->string('menu_name', 100);
            $table->string('menu_url', 100)->nullable();
            $table->unsignedTinyInteger('menu_level')->default(1);
            $table->unsignedSmallInteger('order_no')->default(1);
            $table->unsignedBigInteger('parent_id')->index()->nullable();
            $table->string('class_tag', 50)->nullable();
            $table->string('icon', 50)->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->dateTime('created_at')->nullable()->useCurrent();
            $table->integer('created_by')->nullable()->index();
            $table->dateTime('updated_at')->nullable();
            $table->integer('updated_by')->nullable()->index();
            $table->dateTime('deleted_at')->nullable()->index();
            $table->integer('deleted_by')->nullable()->index();

            $table->foreign('parent_id')->references('menu_id')->on('s_menu');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('s_menu');
    }
}
