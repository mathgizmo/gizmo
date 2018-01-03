<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateTfAnswers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(<<<SQL
UPDATE answer a JOIN question q on a.question_id = q.id
SET a.value = IF(a.value LIKE 't%', 'True', 'False')
WHERE q.reply_mode = 'TF';
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
    }
}
