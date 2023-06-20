<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->id();
            $table->string('subject_title');
            $table->timestamp('publish_date')->nullable();
            $table->string('email');
            $table->string('com_name');
            $table->string('department_name')->nullable();
            $table->string('name');
            $table->bigInteger('recipienter_id')->unsigned()->nullable();
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
        Schema::dropIfExists('quotes');
    }
}
