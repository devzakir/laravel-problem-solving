<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProductsUserColumnsTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_user_columns', function(Blueprint $table) {
            $table->tinyInteger('tax')->default(1); // 1: 10%, 2: 8%
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('product_user_columns', function(Blueprint $table) {
            $table->dropColumn(['tax']);
        });
    }
}
