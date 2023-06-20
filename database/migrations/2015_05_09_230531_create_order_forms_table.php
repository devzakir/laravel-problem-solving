<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderFormsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_forms', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('condition')->nullable();
            $table->tinyInteger('has_deadline')->default(1);
            $table->tinyInteger('payment_method')->default(0);
            $table->text('memo')->nullable();
            $table->tinyInteger('is_public')->default(1);
            $table->bigInteger('recipienter_id')->unsigned();
            $table->foreign('recipienter_id')->references('id')->on('recipienters')->onDelete('cascade');
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
        Schema::dropIfExists('order_forms');
    }
}
