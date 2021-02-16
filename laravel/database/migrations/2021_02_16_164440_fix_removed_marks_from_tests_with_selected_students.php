<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class FixRemovedMarksFromTestsWithSelectedStudents extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach (DB::table('classes_applications_students')->whereNull('mark')->get() as $row) {
            try {
                $answers_statistics = DB::table('students_test_questions')
                    ->select(
                        DB::raw('SUM(1) as total'),
                        DB::raw('SUM(IF(is_right_answer, 1, 0)) as complete')
                    )
                    ->where('class_app_id', $row->class_app_id)
                    ->where('student_id', $row->student_id)
                    ->first();
                if ($answers_statistics && $answers_statistics->total > 0) {
                    if ($row->questions_count && $row->questions_count > 0) {
                        $correct_question_rate = $answers_statistics->complete / $row->questions_count;
                        DB::table('classes_applications_students')
                            ->where('class_app_id', $row->class_app_id)
                            ->where('student_id', $row->student_id)
                            ->update([
                                'mark' => $correct_question_rate
                            ]);
                    } else {
                        $correct_question_rate = $answers_statistics->total > 0
                            ? $answers_statistics->complete / $answers_statistics->total : 1;
                        DB::table('classes_applications_students')
                            ->where('class_app_id', $row->class_app_id)
                            ->where('student_id', $row->student_id)
                            ->update([
                                'mark' => $correct_question_rate,
                                'questions_count' => $answers_statistics->total
                            ]);
                    }
                }
            } catch (\Exception $e) { }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() { }
}
