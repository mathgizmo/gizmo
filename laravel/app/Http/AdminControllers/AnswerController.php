<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Question;
use App\Answer;
use App\Http\Requests;

class AnswerController extends Controller
{

    public function index(Request $request) {
    }

    public function create() {
    }

    public function store(Request $request) {
    }

    public function show($id) {
    }

    public function edit($id) {
    }

    public function update(Request $request, $id) {
    }

    public function destroy(Request $request, $id) {
    }


    /**
     * Insert answer from question table
     */
    public function insertAnswerFromQuestions() {
        //Insert Answer for (True or False) or general from question table
        DB::insert("INSERT into answers(question_id, value, is_correct, answer_order, created_at, updated_at) SELECT q.id, q.answer, 1, 0, NOW(), NOW() FROM question as q WHERE q.reply_mode IN ('TF', 'general')");
        //Insert Answer for (Fill In The Blank) from question table
        DB::insert("INSERT into answers(question_id, value, is_correct, answer_order, created_at, updated_at) (
                SELECT q.id, q.answer, 1, 0, NOW(), NOW() FROM question as q WHERE q.reply_mode IN ('FB', 'ascending', 'descending') AND q.answer <> '')
                UNION ALL (SELECT q.id, q.answer2, 1, 1, NOW(), NOW() FROM question as q WHERE q.reply_mode IN ('FB', 'ascending', 'descending') AND q.answer2 <> '')
                UNION ALL (SELECT q.id, q.answer3, 1, 2, NOW(), NOW() FROM question as q WHERE q.reply_mode IN ('FB', 'ascending', 'descending') AND q.answer3 <> '')
                UNION ALL (SELECT q.id, q.answer4, 1, 3, NOW(), NOW() FROM question as q WHERE q.reply_mode IN ('FB', 'ascending', 'descending') AND q.answer4 <> '')
                UNION ALL (SELECT q.id, q.answer5, 1, 4, NOW(), NOW() FROM question as q WHERE q.reply_mode IN ('FB', 'ascending', 'descending') AND q.answer5 <> '')
                UNION ALL (SELECT q.id, q.answer6, 1, 5, NOW(), NOW() FROM question as q WHERE q.reply_mode IN ('FB', 'ascending', 'descending') AND q.answer6 <> '')
                ");
        //Insert Answer for Multiple choices from question table
        DB::insert("INSERT into answers(question_id, value, is_correct, answer_order, created_at, updated_at) (
                SELECT q.id, q.mcq1, IF(q.mcq1=q.answer, 1, 0), 0, NOW(), NOW() FROM question as q where q.reply_mode IN ('mcq3', 'mcq4', 'mcq5', 'mcq6') AND q.mcq1 <> '')
            UNION ALL (SELECT q.id, q.mcq2, IF(q.mcq2=q.answer, 1, 0), 0, NOW(), NOW() FROM question as q where q.reply_mode IN ('mcq3', 'mcq4', 'mcq5', 'mcq6') AND q.mcq2 <> '')
            UNION ALL (SELECT q.id, q.mcq3, IF(q.mcq3=q.answer, 1, 0), 0, NOW(), NOW() FROM question as q where q.reply_mode IN ('mcq3', 'mcq4', 'mcq5', 'mcq6') AND q.mcq3 <> '')
            UNION ALL (SELECT q.id, q.mcq4, IF(q.mcq4=q.answer, 1, 0), 0, NOW(), NOW() FROM question as q where q.reply_mode IN ('mcq3', 'mcq4', 'mcq5', 'mcq6') AND q.mcq4 <> '')
            UNION ALL (SELECT q.id, q.mcq5, IF(q.mcq5=q.answer, 1, 0), 0, NOW(), NOW() FROM question as q where q.reply_mode IN ('mcq3', 'mcq4', 'mcq5', 'mcq6') AND q.mcq5 <> '')
            UNION ALL (SELECT q.id, q.mcq6, IF(q.mcq6=q.answer, 1, 0), 0, NOW(), NOW() FROM question as q where q.reply_mode IN ('mcq3', 'mcq4', 'mcq5', 'mcq6') AND q.mcq6 <> '')");
    }

}

