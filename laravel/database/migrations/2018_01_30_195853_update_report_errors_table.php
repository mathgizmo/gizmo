<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateReportErrorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('report_errors', function ($table) {
            $table->dropColumn('comment');
        });
        Schema::table('report_errors', function ($table) {
            $table->text('comment');
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
            $table->dropColumn('comment');
        });
        Schema::table('report_errors', function ($table) {
            $table->string('comment');
        });
    }
}
