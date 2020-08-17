<?php

use App\Application;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateStudentsTrackingTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('students_tracking', function (Blueprint $table) {
            $table->integer('app_id')->unsigned()->nullable()->after('lesson_id');
            // $table->foreign('app_id')->references('id')->on('applications')->onDelete('cascade'); // not works :(
        });

        $app = Application::whereDoesntHave('teacher')->first();
        $app_id = $app ? $app->id : 1;
        DB::table('students_tracking')->update(['app_id' => $app_id]);
        DB::table('progresses')->whereNull('app_id')->update(['app_id' => $app_id]);
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
