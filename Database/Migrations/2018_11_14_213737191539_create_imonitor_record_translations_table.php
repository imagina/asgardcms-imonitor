<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImonitorRecordTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('imonitor__record_translations', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            // Your translatable fields

            $table->integer('record_id')->unsigned();
            $table->string('locale')->index();
            $table->unique(['record_id', 'locale']);
            $table->foreign('record_id')->references('id')->on('imonitor__records')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('imonitor__record_translations', function (Blueprint $table) {
            $table->dropForeign(['record_id']);
        });
        Schema::dropIfExists('imonitor__record_translations');
    }
}
