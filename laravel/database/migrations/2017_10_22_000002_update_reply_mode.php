<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateReplyMode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Table structure for table `answer`
        DB::unprepared(<<<SQL
UPDATE `reply_mode` SET `mode`='Numeric/text response' WHERE `id`='1';
SQL
            );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //add fields back
        DB::unprepared(<<<SQL
UPDATE `reply_mode` SET `mode`='General' WHERE `id`='1';
SQL
    );
    }
}
