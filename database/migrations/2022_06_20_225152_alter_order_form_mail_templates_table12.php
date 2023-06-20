<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterOrderFormMailTemplatesTable12 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_form_mail_templates', function(Blueprint $table) {
            $table->tinyInteger('delivery_card')->default(0);
            $table->timestamp('delivery_card_at')->nullable();
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
            $table->dropColumn(['delivery_card', 'delivery_card_at']);
        });
    }
}
