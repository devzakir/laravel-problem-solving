<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderFormMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_form_messages', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('recipienter_id')->unsigned()->nullable();
            $table->foreign('recipienter_id')->references('id')->on('recipienters')->onDelete('cascade');
            $table->bigInteger('client_id')->unsigned()->nullable();
            $table->foreign('client_id')->references('id')->on('users')->onDelete('cascade');
            $table->bigInteger('order_form_id')->unsigned()->nullable();
            $table->foreign('order_form_id')->references('id')->on('order_forms')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->text('message')->nullable();
            $table->text('address')->nullable();
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
        Schema::dropIfExists('order_form_messages');
    }
}
