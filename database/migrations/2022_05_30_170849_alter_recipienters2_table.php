<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterRecipienters2Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('recipienters', function(Blueprint $table) {
            $table->string('zipcode')->nullable();
            $table->string('prefecture')->nullable();
            $table->string('city')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('recipienters', function(Blueprint $table) {
            $table->dropColumn(['zipcode', 'prefecture', 'city']);
        });
    }
}
