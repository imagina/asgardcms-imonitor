<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddImonitorMaxMinProductVariableTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('imonitor_product_variable', function (Blueprint $table) {
            $table->double('max_value')->nullable()->after('product_id');
            $table->double('min_value')->nullable()->after('product_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('imonitor_product_variable', function (Blueprint $table) {
            $table->dropColumn('max_value');
            $table->dropColumn('min_value');
        });
    }
}
