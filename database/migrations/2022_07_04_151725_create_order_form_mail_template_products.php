<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderFormMailTemplateProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_form_mail_template_products', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('template_id')->unsigned()->nullable();
            $table->foreign('template_id')->references('id')->on('order_form_mail_templates')->onDelete('cascade');
            $table->bigInteger('product_id')->unsigned()->nullable();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->integer('price')->nullable();
            $table->integer('amount')->nullable();
            $table->integer('tax_type')->nullable();
            $table->integer('tax')->nullable();
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
        Schema::dropIfExists('order_form_mail_template_products');
    }
}
