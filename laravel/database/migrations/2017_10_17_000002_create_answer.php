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
SELECT id, answer, 1, 0, NOW(), NOW() FROM question where reply_mode IN ('TF', 'FB', 'general', 'ascending', 'descending');

INSERT into answer (`question_id`, `value`, `is_correct`, `answer_order`, `created_at`, `updated_at`)
SELECT id, answer2, 1, 1, NOW(), NOW() FROM question where reply_mode IN ('FB', 'ascending', 'descending') AND (answer2 IS NOT NULL AND answer2 <> '');

INSERT into answer (`question_id`, `value`, `is_correct`, `answer_order`, `created_at`, `updated_at`)
SELECT id, answer3, 1, 2, NOW(), NOW() FROM question where reply_mode IN ('FB', 'ascending', 'descending') AND (answer3 IS NOT NULL AND answer3 <> '');

INSERT into answer (`question_id`, `value`, `is_correct`, `answer_order`, `created_at`, `updated_at`)
SELECT id, answer4, 1, 3, NOW(), NOW() FROM question where reply_mode IN ('FB', 'ascending', 'descending') AND (answer4 IS NOT NULL AND answer4 <> '');

INSERT into answer (`question_id`, `value`, `is_correct`, `answer_order`, `created_at`, `updated_at`)
SELECT id, answer5, 1, 4, NOW(), NOW() FROM question where reply_mode IN ('FB', 'ascending', 'descending') AND (answer5 IS NOT NULL AND answer5 <> '');

INSERT into answer (`question_id`, `value`, `is_correct`, `answer_order`, `created_at`, `updated_at`)
SELECT id, answer6, 1, 5, NOW(), NOW() FROM question where reply_mode IN ('FB', 'ascending', 'descending') AND (answer6 IS NOT NULL AND answer6 <> '');

INSERT into answer (`question_id`, `value`, `is_correct`, `answer_order`, `created_at`, `updated_at`)
SELECT id, mcq1, IF(mcq1=answer OR mcq1=answer2 OR mcq1=answer3 OR mcq1=answer4 or mcq1=answer5 OR mcq1=answer6, 1, 0), 0, NOW(), NOW()
FROM question where reply_mode IN ('mcq4', 'mcq3', 'mcq6', 'mcq5') AND mcq1 IS NOT NULL AND mcq1<> ''
UNION ALL
SELECT id, mcq2, IF(mcq2=answer OR mcq2=answer2 OR mcq2=answer3 OR mcq2=answer4 or mcq2=answer5 OR mcq2=answer6, 1, 0), 1, NOW(), NOW()
FROM question where reply_mode IN ('mcq4', 'mcq3', 'mcq6', 'mcq5') AND mcq2 IS NOT NULL AND mcq2<> ''
UNION ALL
SELECT id, mcq3, IF(mcq3=answer OR mcq3=answer2 OR mcq3=answer3 OR mcq3=answer4 or mcq3=answer5 OR mcq3=answer6, 1, 0), 2, NOW(), NOW()
FROM question where reply_mode IN ('mcq4', 'mcq3', 'mcq6', 'mcq5') AND mcq3 IS NOT NULL AND mcq3<> ''
UNION ALL
SELECT id, mcq4, IF(mcq4=answer OR mcq4=answer2 OR mcq4=answer3 OR mcq4=answer4 or mcq4=answer5 OR mcq4=answer6, 1, 0), 3, NOW(), NOW()
FROM question where reply_mode IN ('mcq4', 'mcq3', 'mcq6', 'mcq5') AND mcq4 IS NOT NULL AND mcq4<> ''
UNION ALL
SELECT id, mcq5, IF(mcq5=answer OR mcq5=answer2 OR mcq5=answer3 OR mcq5=answer4 or mcq5=answer5 OR mcq5=answer6, 1, 0), 4, NOW(), NOW()
FROM question where reply_mode IN ('mcq4', 'mcq3', 'mcq6', 'mcq5') AND mcq5 IS NOT NULL AND mcq5<> ''
UNION ALL
SELECT id, mcq6, IF(mcq6=answer OR mcq6=answer2 OR mcq6=answer3 OR mcq6=answer4 or mcq6=answer5 OR mcq6=answer6, 1, 0), 5, NOW(), NOW()
FROM question where reply_mode IN ('mcq4', 'mcq3', 'mcq6', 'mcq5') AND mcq6 IS NOT NULL AND mcq6<> ''
;
SQL
            );

        //question field should have full question
        DB::unprepared(<<<SQL
UPDATE question
SET question = CONCAT_WS(' __BLANK__ ',
	IF(question_fp1 <> '', question_fp1, NULL),
    IF(question_fp2<> '', question_fp2, NULL),
    IF(question_fp3<> '', question_fp3, NULL),
    IF(question_fp4<> '', question_fp4, NULL),
    IF(question_fp5<> '', question_fp5, NULL),
    IF(question_fp6<> '', question_fp6, NULL),
    IF(question_fp7<> '', question_fp7, NULL)
)
WHERE question_fp1 IS NOT NULL AND question_fp1 <> ''
;
SQL
            );

        //drop unused fields
        DB::unprepared(<<<SQL
ALTER TABLE question
    DROP COLUMN answer,
    DROP COLUMN answer2,
    DROP COLUMN answer3,
    DROP COLUMN answer4,
    DROP COLUMN answer5,
    DROP COLUMN answer6,
    DROP COLUMN mcq1,
    DROP COLUMN mcq2,
    DROP COLUMN mcq3,
    DROP COLUMN mcq4,
    DROP COLUMN mcq5,
    DROP COLUMN mcq6,
    DROP COLUMN mandatoriness,
    DROP COLUMN question_fp1,
    DROP COLUMN question_fp2,
    DROP COLUMN question_fp3,
    DROP COLUMN question_fp4,
    DROP COLUMN question_fp5,
    DROP COLUMN question_fp6,
    DROP COLUMN question_fp7
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
        //add fields back
        DB::unprepared(<<<SQL
ALTER TABLE question
    ADD COLUMN `mcq1` varchar(255) DEFAULT NULL,
    ADD COLUMN `mcq2` varchar(255) DEFAULT NULL,
    ADD COLUMN `mcq3` varchar(255) DEFAULT NULL,
    ADD COLUMN `mcq4` varchar(255) DEFAULT NULL,
    ADD COLUMN `mcq5` varchar(255) DEFAULT NULL,
    ADD COLUMN `mcq6` varchar(255) DEFAULT NULL,
    ADD COLUMN `answer` varchar(255) DEFAULT NULL,
    ADD COLUMN `answer2` varchar(255) DEFAULT NULL,
    ADD COLUMN `answer3` varchar(255) DEFAULT NULL,
    ADD COLUMN `answer4` varchar(255) DEFAULT NULL,
    ADD COLUMN `answer5` varchar(255) DEFAULT NULL,
    ADD COLUMN `answer6` varchar(255) DEFAULT NULL,
    ADD COLUMN `mandatoriness` varchar(255) NOT NULL DEFAULT 'Yes',
    ADD COLUMN `question_fp1` varchar(500) DEFAULT NULL,
    ADD COLUMN `question_fp2` varchar(500) DEFAULT NULL,
    ADD COLUMN `question_fp3` varchar(500) DEFAULT NULL,
    ADD COLUMN `question_fp4` varchar(500) DEFAULT NULL,
    ADD COLUMN `question_fp5` varchar(500) DEFAULT NULL,
    ADD COLUMN `question_fp6` varchar(500) DEFAULT NULL,
    ADD COLUMN `question_fp7` varchar(500) DEFAULT NULL
;
SQL
            );

        //import data into answer table from question table
        DB::unprepared(<<<SQL
UPDATE question q
JOIN answer a ON a.question_id = q.id
SET q.mcq1 = a.value
WHERE q.reply_mode in ('mcq3', 'mcq4', 'mcq5', 'mcq6') AND a.answer_order = 0;

UPDATE question q
JOIN answer a ON a.question_id = q.id
SET q.mcq2 = a.value
WHERE q.reply_mode in ('mcq3', 'mcq4', 'mcq5', 'mcq6') AND a.answer_order = 1;

UPDATE question q
JOIN answer a ON a.question_id = q.id
SET q.mcq3 = a.value
WHERE q.reply_mode in ('mcq3', 'mcq4', 'mcq5', 'mcq6') AND a.answer_order = 2;

UPDATE question q
JOIN answer a ON a.question_id = q.id
SET q.mcq4 = a.value
WHERE q.reply_mode in ('mcq3', 'mcq4', 'mcq5', 'mcq6') AND a.answer_order = 3;

UPDATE question q
JOIN answer a ON a.question_id = q.id
SET q.mcq5 = a.value
WHERE q.reply_mode in ('mcq3', 'mcq4', 'mcq5', 'mcq6') AND a.answer_order = 4;

UPDATE question q
JOIN answer a ON a.question_id = q.id
SET q.mcq6 = a.value
WHERE q.reply_mode in ('mcq3', 'mcq4', 'mcq5', 'mcq6') AND a.answer_order = 5;

UPDATE question q
JOIN answer a ON a.question_id = q.id
SET q.answer = a.value
WHERE a.answer_order = 0 AND a.is_correct = 1;

UPDATE question q
JOIN answer a ON a.question_id = q.id
SET q.answer2 = a.value
WHERE a.answer_order = 1 AND a.is_correct = 1;

UPDATE question q
JOIN answer a ON a.question_id = q.id
SET q.answer3 = a.value
WHERE a.answer_order = 2 AND a.is_correct = 1;

UPDATE question q
JOIN answer a ON a.question_id = q.id
SET q.answer4 = a.value
WHERE a.answer_order = 3 AND a.is_correct = 1;

UPDATE question q
JOIN answer a ON a.question_id = q.id
SET q.answer5 = a.value
WHERE a.answer_order = 4 AND a.is_correct = 1;

UPDATE question q
JOIN answer a ON a.question_id = q.id
SET q.answer6 = a.value
WHERE a.answer_order = 5 AND a.is_correct = 1;

UPDATE question
SET answer = answer2, answer2 = NULL
WHERE answer IS NULL AND answer2 IS NOT NULL;

UPDATE question
SET answer2 = answer3, answer3 = NULL
WHERE answer2 IS NULL AND answer3 IS NOT NULL;

UPDATE question
SET answer3 = answer4, answer4 = NULL
WHERE answer3 IS NULL AND answer4 IS NOT NULL;

UPDATE question
SET answer4 = answer5, answer5 = NULL
WHERE answer4 IS NULL AND answer5 IS NOT NULL;

UPDATE question
SET answer5 = answer6, answer6 = NULL
WHERE answer5 IS NULL AND answer6 IS NOT NULL;

UPDATE question
SET answer = answer2, answer2 = NULL
WHERE answer IS NULL AND answer2 IS NOT NULL;

UPDATE question
SET answer2 = answer3, answer3 = NULL
WHERE answer2 IS NULL AND answer3 IS NOT NULL;

UPDATE question
SET answer3 = answer4, answer4 = NULL
WHERE answer3 IS NULL AND answer4 IS NOT NULL;

UPDATE question
SET answer4 = answer5, answer5 = NULL
WHERE answer4 IS NULL AND answer5 IS NOT NULL;

UPDATE question
SET answer = answer2, answer2 = NULL
WHERE answer IS NULL AND answer2 IS NOT NULL;

UPDATE question
SET answer2 = answer3, answer3 = NULL
WHERE answer2 IS NULL AND answer3 IS NOT NULL;

UPDATE question
SET answer3 = answer4, answer4 = NULL
WHERE answer3 IS NULL AND answer4 IS NOT NULL;

UPDATE question
SET answer = answer2, answer2 = NULL
WHERE answer IS NULL AND answer2 IS NOT NULL;

UPDATE question
SET answer2 = answer3, answer3 = NULL
WHERE answer2 IS NULL AND answer3 IS NOT NULL;

UPDATE question
SET answer = answer2, answer2 = NULL
WHERE answer IS NULL AND answer2 IS NOT NULL;
SQL
            );
        Schema::drop('answer');
    }
}
