<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateQuestionTable3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("UPDATE question SET explanation = CONCAT(IFNULL(explanation, ''), ' ', IFNULL(feedback, ''))");
        Schema::table('question', function($table) {
            $table->dropColumn('feedback');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('question', function($table) {
            $table->string('feedback', 255);
        });
    }

}
