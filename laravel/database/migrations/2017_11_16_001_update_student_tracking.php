<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateStudentTracking extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(<<<SQL
ALTER TABLE `students_tracking`
CHANGE COLUMN `user_id` `student_id` INT(11) NOT NULL ;
ALTER TABLE `students_tracking`
CHANGE COLUMN `start_datetime` `start_datetime` DATETIME NOT NULL ;
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
        DB::unprepared(<<<SQL
ALTER TABLE `students_tracking`
CHANGE COLUMN `student_id` `user_id` INT(11) NOT NULL ;
ALTER TABLE `students_tracking`
CHANGE COLUMN `start_datetime` `start_datetime` VARCHAR(255) NOT NULL ;
SQL
            );
    }
}
