<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterMailHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mail_histories', function(Blueprint $table) {
            $table->bigInteger('recipienter_id')->unsigned()->nullable();
            $table->foreign('recipienter_id')->references('id')->on('recipienters')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mail_histories', function(Blueprint $table) {
            $table->dropColumn(['recipienter_id']);
        });
    }
}
