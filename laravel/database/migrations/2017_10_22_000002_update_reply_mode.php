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
        DB::unprepared(<<<SQL
DELETE FROM `reply_mode` WHERE `id`>3;
SQL
    ); DB::unprepared(<<<SQL
INSERT INTO `reply_mode` VALUES
(10,'mcq','Multiple Choice',NULL,NULL),
(11,'order','Correct Order',NULL,NULL),
(12,'mcqms','Multiple Choice/Multiple Answers',NULL,NULL);
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
        DB::unprepared(<<<SQL
INSERT INTO `reply_mode` VALUES 
(4,'mcq3','Multiple Choice 3',NULL,NULL),
(5,'mcq4','Multiple Choice 4',NULL,NULL),
(6,'mcq5','Multiple Choice 5',NULL,NULL),
(7,'mcq6','Multiple Choice 6',NULL,NULL),
(8,'ascending','Ascending Order',NULL,NULL),
(9,'descending','Descending Order',NULL,NULL);
SQL
    );
    DB::unprepared(<<<SQL
DELETE FROM `reply_mode` WHERE `id`>9;
SQL
    );
    }
}
