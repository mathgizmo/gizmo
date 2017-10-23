<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateQuestionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //Table structure for table `answer`
        Schema::table('question', function (Blueprint $table) {
            $table->dropColumn(['option_text', 'option_size', 'size']);
        });
        DB::unprepared(<<<SQL
DELETE FROM `reply_mode` WHERE `id`>3;
SQL
        );
        DB::unprepared(<<<SQL
INSERT INTO `reply_mode` VALUES
(10,'mcq','Multiple Choice',NULL,NULL),
(11,'order','Correct Order',NULL,NULL),
(12,'mcqms','Multiple Choice/Multiple Answers',NULL,NULL);
SQL
    );
        DB::unprepared(<<<SQL
UPDATE question set reply_mode = 10 WHERE reply_mode in (4,5,6,7);
SQL
    );
        DB::unprepared(<<<SQL
UPDATE question set reply_mode = 11 WHERE reply_mode in (8,9);
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
        Schema::table('question', function (Blueprint $table) {
            $table->string('option_text')->nullable();
            $table->string('option_size')->nullable();
            $table->integer('size')->default(1);
        });
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
        DB::unprepared(<<<SQL
UPDATE question set reply_mode = 7 WHERE reply_mode = 10;
SQL
        );
        DB::unprepared(<<<SQL
UPDATE question set reply_mode = 8 WHERE reply_mode = 11;
SQL
        );
    }
}
