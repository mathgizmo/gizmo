<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateReportErrorsTable3 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('report_errors', function ($table) {
            $table->boolean('is_feedback')->default(false);
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
            $table->dropColumn('is_feedback');
        });
    }
}
