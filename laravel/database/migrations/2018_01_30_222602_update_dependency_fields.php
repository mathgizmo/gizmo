<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateDependencyFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('level', function (Blueprint $table) {
            $table->boolean('dependency')->default(true);
        });
        DB::table('lesson')->where('dependency', 'Yes')->update(['dependency' => 1]);
        DB::table('lesson')->where('dependency', 'No')->update(['dependency' => 0]);
        Schema::table('lesson', function (Blueprint $table) {
            $table->boolean('dependency')->default(true)->change();
        });
        DB::table('topic')->where('dependency', 'Yes')->update(['dependency' => 1]);
        DB::table('topic')->where('dependency', 'No')->update(['dependency' => 0]);
        Schema::table('topic', function (Blueprint $table) {
            $table->boolean('dependency')->default(true)->change();
        });
        DB::table('unit')->where('dependency', 'Yes')->update(['dependency' => 1]);
        DB::table('unit')->where('dependency', 'No')->update(['dependency' => 0]);
        Schema::table('unit', function (Blueprint $table) {
            $table->boolean('dependency')->default(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('level', function (Blueprint $table) {
            $table->dropColumn('dependency');
        });
    }
}
