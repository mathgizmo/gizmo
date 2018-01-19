<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReportErrorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_errors', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('student_id');
            $table->integer('question_id');
            $table->integer('answer_id');
            $table->string('options');
            $table->string('comment');
            $table->boolean('declined')->default(false);
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
        Schema::drop('report_errors');
    }
}
