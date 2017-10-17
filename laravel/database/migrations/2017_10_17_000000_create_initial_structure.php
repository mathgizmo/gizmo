<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateInitialStructure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //for existing DB we should do
        //TRUNCATE TABLE migrations;
        //INSERT INTO migrations (migration, batch) VALUES ('2017_10_17_000000_create_initial_structure', 1);
        DB::unprepared(file_get_contents(dirname(__FILE__) .'/initial_structure.sql'));
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }
}
