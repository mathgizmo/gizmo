<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSharesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shares', function (Blueprint $table) {
            $table->increments('id');
            $table->enum('type', ['test', 'assignment', 'classroom']);
            $table->integer('item_id');
            $table->integer('sender_id');
            $table->integer('receiver_id');
            $table->dateTime('date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->tinyInteger('accepted');
            $table->dateTime('accepted_date')->nullable();
            $table->tinyInteger('declined');
            $table->dateTime('declined_date')->nullable();

            $table->foreign('sender_id')->references('id')->on('students');
            $table->foreign('receiver_id')->references('id')->on('students');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shares');
    }
}
