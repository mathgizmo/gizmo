<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared(<<<SQL
UPDATE topic t JOIN (
SELECT MIN(id) as start, unit_id FROM topic GROUP BY unit_id) t1 ON t.unit_id = t1.unit_id
SET t.order_no = t.id - t1.start;
SQL
        );
        DB::unprepared(<<<SQL
UPDATE lesson t JOIN (
SELECT MIN(id) as start, topic_id FROM lesson GROUP BY topic_id) t1 ON t.topic_id = t1.topic_id
SET t.order_no = t.id - t1.start;
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
