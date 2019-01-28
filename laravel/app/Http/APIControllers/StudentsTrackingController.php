<?php

namespace App\Http\APIControllers;

use App\Progress;
use App\StudentsTracking;
use App\Topic;
use App\Unit;
use JWTAuth;
use App\Lesson;
use Illuminate\Support\Facades\DB;

class StudentsTrackingController extends Controller
{
    /**
     * @param $lesson
     * @return mixed
     */
    public function start($lesson)
    {
        if (($model = Lesson::find($lesson)) == null) {
            return $this->error('Invalid lesson.');
        }
        $student = JWTAuth::parseToken()->authenticate();
        $now = date("Y-m-d H:i:s");
        StudentsTracking::create([
            'student_id' => $student->id,
            'lesson_id' => $lesson,
            'action' => 'start',
            'date' => $now,
            'start_datetime' => $now,
            'weak_questions' => json_encode([]),
            'ip' => request()->ip(),
            'user_agent' => request()->server('HTTP_USER_AGENT'),
        ]);
        return $this->success($now);
    }

    /**
     * @param $lesson
     * @return mixed
     */
    public function done($lesson)
    {
        if (($model = Lesson::find($lesson)) == null) {
            return $this->error('Invalid lesson.');
        }
        $student = JWTAuth::parseToken()->authenticate();
        StudentsTracking::create([
            'student_id' => $student->id,
            'lesson_id' => $lesson,
            'action' => 'done',
            'date' => date("Y-m-d H:i:s"),
            'start_datetime' => date("Y-m-d H:i:s", (request()->start_datetime ? strtotime(request()->start_datetime) : date('U'))),
            'weak_questions' => json_encode(request()->weak_questions ? request()->weak_questions : []),
            'ip' => request()->ip(),
            'user_agent' => request()->server('HTTP_USER_AGENT'),
        ]);
        $progress_data = [
            'student_id' => $student->id,
            'entity_type' => 0,
            'entity_id' => $lesson
        ];
        $progress = Progress::where($progress_data)->get();
        if ($progress->count() == 0) {
            DB::enableQueryLog();
            Progress::create($progress_data);
            //find all lessons from topic that are not done yet
            $lessons = DB::table('lesson')->leftJoin('progresses', function ($join) use ($student) {
                $join->on('progresses.student_id', '=', DB::raw($student->id))
                ->on('progresses.entity_type', '=', DB::raw(0))
                ->on('progresses.entity_id', '=', 'lesson.id');
                })
                ->where(['topic_id' => $model->topic_id, 'dependency' => 1])
                ->where('dev_mode', 0)
                ->whereNull('progresses.id')->get();
            //if all lessons done
            if (!count($lessons)) {
                self::topicProgressDone($model->topic_id, $student);
            }
        }
        return $this->success('OK.');
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
            $unit_model = Unit::where("id", $topic_model->unit_id)->first();
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
