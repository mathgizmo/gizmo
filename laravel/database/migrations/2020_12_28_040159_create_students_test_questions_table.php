<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTestQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students_test_questions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('student_id');
            $table->unsignedInteger('question_id');
            $table->unsignedInteger('topic_id')->nullable();
            $table->unsignedInteger('unit_id')->nullable();
            $table->unsignedInteger('level_id')->nullable();
            $table->unsignedInteger('class_app_id');
            $table->boolean('is_right_answer')->default(false);
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('class_app_id')->references('id')->on('classes_applications')->onDelete('cascade');
            $table->foreign('question_id')->references('id')->on('question')->onDelete('set null');
            $table->foreign('topic_id')->references('id')->on('topic')->onDelete('set null');
            $table->foreign('unit_id')->references('id')->on('unit')->onDelete('set null');
            $table->foreign('level_id')->references('id')->on('level')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('students_test_questions');
    }
}
