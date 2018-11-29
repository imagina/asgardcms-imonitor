<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImonitorProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('imonitor__products', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('variable_id');
            $table->integer('user_id')->unsigned();
            //$table->integer('useru_id')->unsigned();
            $table->text('address');
            $table->text('options')->default('')->nullable();

            $table->foreign('user_id')->references('id')->on(config('auth.table', 'users'))->onDelete('restrict');
            // Your fields
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('imonitor__products', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        Schema::dropIfExists('imonitor__products');
    }
}
