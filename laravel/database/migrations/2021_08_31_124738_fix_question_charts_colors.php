<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixQuestionChartsColors extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (\App\Question::where('question', 'LIKE', '%%%chart%')->get() as $item) {
            $question = $item->question;
            $main_color = substr($question,strpos($question, 'main-color:'), 20);
            $selected_color = substr($question,strpos($question, 'selected-color:'), 24);
            $stroke_color = substr($question,strpos($question, 'stroke-color:'), 22);
            $question = str_replace($main_color, '', $question);
            $question = str_replace($selected_color, '', $question);
            $question = str_replace($stroke_color, '', $question);
            $item->question = $question;
            $item->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
