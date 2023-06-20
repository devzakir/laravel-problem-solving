<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterOrderFormMailTemaplatesTable4 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_form_mail_templates', function(Blueprint $table) {
            $table->tinyInteger('payment_method')->default(1);  //1: Bank, 2: Credit Card
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_form_mail_templates', function(Blueprint $table) {
            $table->dropColumn(['payment_method']);
        });
    }
}
