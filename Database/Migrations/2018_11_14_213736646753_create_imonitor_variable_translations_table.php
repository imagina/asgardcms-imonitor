<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImonitorVariableTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('imonitor__variable_translations', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('title');
            $table->text('description')->nullable();
            // Your translatable fields

            $table->integer('variable_id')->unsigned();
            $table->string('locale')->index();
            $table->unique(['variable_id', 'locale']);
            $table->foreign('variable_id')->references('id')->on('imonitor__variables')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('imonitor__variable_translations', function (Blueprint $table) {
            $table->dropForeign(['variable_id']);
        });
        Schema::dropIfExists('imonitor__variable_translations');
    }
}
