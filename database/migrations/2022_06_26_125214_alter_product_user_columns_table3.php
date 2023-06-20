<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProductUserColumnsTable3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('product_user_columns', function(Blueprint $table) {
            $table->tinyInteger('tax_type')->default(1); // 1: 税抜, 2: 税込, 3: 非課税
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
            $table->dropColumn(['tax_type']);
        });
    }
}
