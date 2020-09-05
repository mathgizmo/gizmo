<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTestoutAttemptsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students_testout_attempts', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('student_id');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->unsignedInteger('topic_id');
            $table->foreign('topic_id')->references('id')->on('topic')->onDelete('cascade');
            $table->integer('app_id')->unsigned()->nullable();
            // $table->foreign('app_id')->references('id')->on('applications')->onDelete('cascade'); // not works :(
            $table->unsignedInteger('attempts')->default(0);

            $table->unique(['student_id', 'topic_id', 'app_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('students_testout_attempts');
    }
}
