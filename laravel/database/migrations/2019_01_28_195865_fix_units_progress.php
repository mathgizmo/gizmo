<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Progress;
use App\StudentsTracking;
use App\Topic;
use App\Unit;
use App\Lesson;
use Illuminate\Support\Facades\DB;

class FixUnitsProgress extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $students = DB::table('students')->select('id')->get();
        foreach ($students as $student) {
            $topics = DB::table('topic')->select('id')->get(); 
            foreach ($topics as $topic) {
                //find all lessons from topic that are not done yet
                $lessons = DB::table('lesson')->leftJoin('progresses', function ($join) use ($student) {
                    $join->on('progresses.student_id', '=', DB::raw($student->id))
                    ->on('progresses.entity_type', '=', DB::raw(0))
                    ->on('progresses.entity_id', '=', 'lesson.id');
                    })
                    ->where(['topic_id' => $topic->id, 'dependency' => 1])
                    ->where('dev_mode', 0)
                    ->whereNull('progresses.id')->get();
                //if all lessons done
                if (!count($lessons)) {
                    self::topicProgressDone($topic->id, $student);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }

    /**
     * @param $topic_id
     * @param $student
     */
    public static function topicProgressDone($topic_id, $student)
    {
        //mark topic as done
        $progress_data = [
            'student_id' => $student->id,
            'entity_type' => 1,
            'entity_id' => $topic_id
        ];
        DB::insert('INSERT IGNORE INTO progresses(student_id, entity_type, entity_id) '.
            'values (?, ?, ?)', array_values($progress_data));
        //find all topics from unit that are not done yet
        $topic_model = Topic::where("id", $topic_id)->first();
        if(!$topic_model) { return; }
        $topics = DB::table('topic')->leftJoin('progresses', function ($join) use ($student) {
            $join->on('progresses.student_id', '=', DB::raw($student->id))
            ->on('progresses.entity_type', '=', DB::raw(1))
            ->on('progresses.entity_id', '=', 'topic.id');
        })
        ->where(['unit_id' => $topic_model->unit_id, 'dependency' => 1])
        ->whereNull('progresses.id')->get();
        //if all topics are done, mark unit as done
        if (!count($topics)) {
            $progress_data['entity_type'] = 2;
            $progress_data['entity_id'] = $topic_model->unit_id;
            DB::insert('INSERT IGNORE INTO progresses(student_id, entity_type, entity_id) '.
                'values (?, ?, ?)', array_values($progress_data));
            //find all units from level that are not done yet
            $unit_model = Unit::where('id', $topic_model->unit_id)->first();
            if(!$unit_model) { return; }
            $units = DB::table('unit')->leftJoin('progresses', function ($join) use ($student) {
                $join->on('progresses.student_id', '=', DB::raw($student->id))
                ->on('progresses.entity_type', '=', DB::raw(2))
                ->on('progresses.entity_id', '=', 'unit.id');
            })
            ->where(['level_id' => $unit_model->level_id, 'dependency' => 1])
            ->whereNull('progresses.id')->get();
            //if all units are done, mark level as done
            if (!count($units)) {
                $progress_data['entity_type'] = 3;
                $progress_data['entity_id'] = $unit_model->level_id;
                DB::insert('INSERT IGNORE INTO progresses(student_id, entity_type, entity_id) '.
                    'values (?, ?, ?)', array_values($progress_data));
            }
        }
    }

}
