<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateAnswer extends Migration
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
CREATE TABLE `answer` (
    `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
    `question_id` int(11) NOT NULL,
    `value` varchar(255) CHARACTER SET utf8 NOT NULL,
    `is_correct` tinyint(1) NOT NULL,
    `answer_order` int(11) NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `question_id` (`question_id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
SQL
);
        //import data into answer table from question table
        DB::unprepared(<<<SQL
INSERT into answer (`question_id`, `value`, `is_correct`, `answer_order`, `created_at`, `updated_at`)
SELECT id, answer, 1, 0, NOW(), NOW() FROM question where reply_mode IN ('TF', 'FB');

INSERT into answer (`question_id`, `value`, `is_correct`, `answer_order`, `created_at`, `updated_at`)
SELECT id, mcq1, IF(mcq1=answer OR mcq1=answer2 OR mcq1=answer3 OR mcq1=answer4 or mcq1=answer5 OR mcq1=answer6, 1, 0), 0, NOW(), NOW() FROM question where reply_mode IN ('mcq4', 'mcq3', 'mcq6') AND mcq1<> ''
UNION ALL
SELECT id, mcq2, IF(mcq2=answer OR mcq2=answer2 OR mcq2=answer3 OR mcq2=answer4 or mcq2=answer5 OR mcq2=answer6, 1, 0), 0, NOW(), NOW() FROM question where reply_mode IN ('mcq4', 'mcq3', 'mcq6') AND mcq2<> ''
UNION ALL
SELECT id, mcq3, IF(mcq3=answer OR mcq3=answer2 OR mcq3=answer3 OR mcq3=answer4 or mcq3=answer5 OR mcq3=answer6, 1, 0), 0, NOW(), NOW() FROM question where reply_mode IN ('mcq4', 'mcq3', 'mcq6') AND mcq3<> ''
UNION ALL
SELECT id, mcq4, IF(mcq4=answer OR mcq4=answer2 OR mcq4=answer3 OR mcq4=answer4 or mcq4=answer5 OR mcq4=answer6, 1, 0), 0, NOW(), NOW() FROM question where reply_mode IN ('mcq4', 'mcq3', 'mcq6') AND mcq4<> ''
UNION ALL
SELECT id, mcq5, IF(mcq5=answer OR mcq5=answer2 OR mcq5=answer3 OR mcq5=answer4 or mcq5=answer5 OR mcq5=answer6, 1, 0), 0, NOW(), NOW() FROM question where reply_mode IN ('mcq4', 'mcq3', 'mcq6') AND mcq5<> ''
UNION ALL
SELECT id, mcq6, IF(mcq6=answer OR mcq6=answer2 OR mcq6=answer3 OR mcq6=answer4 or mcq6=answer5 OR mcq6=answer6, 1, 0), 0, NOW(), NOW() FROM question where reply_mode IN ('mcq4', 'mcq3', 'mcq6') AND mcq6<> ''
;
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
        Schema::drop('answer');
    }
}
