<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateProgressesTable2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('progresses', function (Blueprint $table) {
        	$table->unsignedInteger('app_id')->nullable();
        });
        Schema::table('progresses', function (Blueprint $table) {
        	$table->dropUnique(['student_id', 'entity_type', 'entity_id']);
            $table->renameColumn('entity_type', 'entity_type_old');
        });
        Schema::table('progresses', function (Blueprint $table) {
            $table->enum('entity_type', ['lesson', 'topic', 'unit', 'level', 'application'])->after('student_id');
        });
        DB::table('progresses')->where('entity_type_old', 0)->update(['entity_type' => 'lesson']);
        DB::table('progresses')->where('entity_type_old', 1)->update(['entity_type' => 'topic']);
        DB::table('progresses')->where('entity_type_old', 2)->update(['entity_type' => 'unit']);
        DB::table('progresses')->where('entity_type_old', 3)->update(['entity_type' => 'level']);
        Schema::table('progresses', function (Blueprint $table) {
            $table->dropColumn('entity_type_old');
            $table->unique(['student_id', 'entity_type', 'entity_id', 'app_id']);
        });
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
