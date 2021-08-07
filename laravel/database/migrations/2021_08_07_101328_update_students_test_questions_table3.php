<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateStudentsTestQuestionsTable3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('students_test_questions', function (Blueprint $table) {
            $table->string('answer', 1000)->nullable()->after('is_right_answer');
            $table->string('correct_answer', 1000)->nullable()->after('answer');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('students_test_questions', function (Blueprint $table) {
            $table->dropColumn('answer');
            $table->dropColumn('correct_answer');
        });
    }
}
