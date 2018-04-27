<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlacementQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('placement_questions', function (Blueprint $table) {
            $table->increments('id')->index()->unsigned();
            $table->integer('order');
            $table->text('question');
            $table->integer('unit_id')->unsigned();
            $table->tinyInteger('is_active')->default(1);

            $table->foreign('unit_id')->references('id')->on('unit');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('placement_questions');
    }

}
