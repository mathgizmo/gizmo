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
        foreach (\App\Question::where('question', 'LIKE', '%\%\%chart%')->get() as $item) {
            $question = $item->question;
            $main_color_pos = strpos($question, 'main-color:');
            if ($main_color_pos) {
                $main_color = substr($question, $main_color_pos, 20);
                $question = str_replace($main_color, '', $question);
            }
            $selected_color_pos = strpos($question, 'selected-color:');
            if ($selected_color_pos) {
                $selected_color = substr($question, $selected_color_pos, 24);
                $question = str_replace($selected_color, '', $question);
            }
            $stroke_color_pos = strpos($question, 'stroke-color:');
            if ($stroke_color_pos) {
                $stroke_color = substr($question, $stroke_color_pos, 22);
                $question = str_replace($stroke_color, '', $question);
            }
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
