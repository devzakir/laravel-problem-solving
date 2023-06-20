<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterOrderFormMailTemplatesTable20 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_form_mail_templates', function(Blueprint $table) {
            $table->timestamp('pay_expire')->nullable(); // 1: 10%, 2: 8%
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
            $table->dropColumn(['pay_expire']);
        });
    }
}
