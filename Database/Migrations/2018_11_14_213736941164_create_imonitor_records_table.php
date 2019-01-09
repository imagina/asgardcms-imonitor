<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImonitorRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('imonitor__records', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('variable_id')->unsigned();
            $table->integer('product_id')->unsigned();
            $table->double('value');
            $table->foreign('variable_id')->references('id')->on('imonitor__variables')->onDelete('restrict');
            $table->foreign('product_id')->references('id')->on('imonitor__products')->onDelete('restrict');
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
        Schema::table('', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['variable_id']);
        });
        Schema::dropIfExists('imonitor__records');
    }
}
