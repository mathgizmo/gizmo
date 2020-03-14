<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateStudentsTable5 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->unsignedInteger('app_id')->nullable()->after('id');
            // $table->foreign('app_id')->references('id')->on('applications')->onDelete('cascade'); // Not works :(
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            // $table->dropForeign(['app_id']);
            $table->dropColumn('app_id');
        });
    }
}
