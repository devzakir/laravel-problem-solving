<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterOrderFormMailTemaplatesTable8 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_form_mail_templates', function(Blueprint $table) {
            $table->timestamp('delivery_at')->nullable();
            $table->string('delivery_hash')->nullable();
            $table->string('invoice_hash')->nullable();
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
            $table->dropColumn(['delivery_at', 'delivery_hash', 'invoice_hash']);
        });
    }
}
