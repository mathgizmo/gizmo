<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class FixUncompletedTestThatShowsAsCompleted extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('classes_applications_students')
        ->whereNull('start_at')->whereNotNull('end_at')
        ->update([
        	'questions_count' => NULL,
        	'mark' => NULL,
        	'start_at' => NULL,
        	'end_at' => NULL
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
