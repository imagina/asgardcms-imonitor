<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImonitorAlertsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('imonitor__alerts', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('product_id')->unsigned();
            $table->integer('record_id')->unsigned();
            $table->integer('status')->default(0);
            $table->integer('user_id')->unsigned();
            $table->foreign('product_id')->references('id')->on('imonitor__products')->onDelete('cascade');
            $table->foreign('record_id')->references('id')->on('imonitor__records')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on(config('auth.table', 'users'))->onDelete('restrict');
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
        Schema::table('imonitor__records', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['record_id']);
            $table->dropForeign(['user_id']);
        });
        Schema::dropIfExists('imonitor__alerts');
    }
}
