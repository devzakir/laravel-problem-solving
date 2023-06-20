<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductUserColumnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_user_columns', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('recipienter_id')->unsigned();
            $table->foreign('recipienter_id')->references('id')->on('recipienters')->onDelete('cascade');
            $table->bigInteger('product_column_id')->unsigned();
            $table->foreign('product_column_id')->references('id')->on('product_columns')->onDelete('cascade');
            $table->integer('order')->default(1);
            $table->tinyInteger('picked')->default(0);
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
        Schema::dropIfExists('product_user_columns');
    }
}
