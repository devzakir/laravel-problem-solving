<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterOrderFormMailTemaplatesTable7 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_form_mail_templates', function(Blueprint $table) {
            $table->tinyInteger('delivery')->default(0);
            $table->tinyInteger('invoiced')->default(0);
            $table->timestamp('invoice_at')->nullable();
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
            $table->dropColumn(['delivery', 'invoiced', 'invoice_at']);
        });
    }
}
