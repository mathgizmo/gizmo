<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateQuestionTable1 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('question', 'conversion') && !Schema::hasColumn('question', 'rounding')) {
        	Schema::table('question', function (Blueprint $table) {
	            $table->boolean('conversion')->default(false);
	            $table->boolean('rounding')->default(false);
	        });
        }   
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('level', function (Blueprint $table) {
            $table->dropColumn('conversion');
            $table->dropColumn('rounding');
        });
    }
}
