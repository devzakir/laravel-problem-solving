<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function(Blueprint $table) {
            $table->tinyInteger('tax_type')->default(1); // 1: 税抜, 2: 税込, 3: 非課税
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
        Schema::table('products', function(Blueprint $table) {
            $table->dropColumn(['tax_type', 'tax']);
        });
    }
}
