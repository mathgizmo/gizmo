<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateReportErrorsTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('report_errors', function ($table) {
            $table->dropColumn('answer_id');
        });
        Schema::table('report_errors', function ($table) {
            $table->text('answers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('report_errors', function ($table) {
            $table->dropColumn('answers');
        });
        Schema::table('report_errors', function ($table) {
            $table->integer('answer_id');
        });
    }
}
