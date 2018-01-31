<?php

namespace App\Http\APIControllers;

use App\Progress;
use App\StudentsTracking;
use App\Topic;
use App\Unit;
use JWTAuth;
use App\Lesson;
use App\Http\Requests;

class StudentsTrackingController extends Controller
{
    public function start($lesson)
    {
        if (($model = Lesson::find($lesson)) == null) {
            return $this->error('Invalid lesson.');
        }

        $student = JWTAuth::parseToken()->authenticate();

        /*if (StudentsTracking::where(['student_id' => $student->id, 'lesson_id' => $lesson])->first() != null) {
            return $this->error('You already start this lesson.');
        }*/
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

    public function done($lesson)
    {
        if (($model = Lesson::find($lesson)) == null) {
            return $this->error('Invalid lesson.');
        }

        $student = JWTAuth::parseToken()->authenticate();

        /*if (StudentsTracking::where(['student_id' => $student->id, 'lesson_id' => $lesson, 'action' => 'start'])->first() == null) {
            return $this->error('You can\'t done this lesson because you never start it.');
        }

        if (StudentsTracking::where(['student_id' => $student->id, 'lesson_id' => $lesson, 'action' => 'done'])->first() != null) {
            return $this->error('You already done this lesson.');
        }*/

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
            Progress::create($progress_data);
            $lessons = Lesson::where(['topic_id' => $model->topic_id, 'dependency' => 1])->get();
            $check = true;
            foreach ($lessons as $lesson) {
                $progress_data['entity_id'] = $lesson->id;
                $progress = Progress::where($progress_data)->get();
                if ($progress->count() == 0) {
                    $check = false;
                    break;
                }
            }
            if ($check == true) {
                $progress_data['entity_id'] = $model->topic_id;
                $progress_data['entity_type'] = 1;
                $progress = Progress::where($progress_data)->get();
                if ($progress->count() == 0) {
                    Progress::create($progress_data);
                }
            }

            $topic_model = Topic::find($model->topic_id)->first();
            $topics = Topic::where(['unit_id' => $topic_model->unit_id, 'dependency' => 1])->get();
            $check = true;
            foreach ($topics as $topic) {
                $progress_data['entity_id'] = $topic->id;
                $progress = Progress::where($progress_data)->get();
                if ($progress->count() == 0) {
                    $check = false;
                    break;
                }
            }
            if ($check == true) {
                $progress_data['entity_id'] = $topic_model->unit_id;
                $progress_data['entity_type'] = 2;
                $progress = Progress::where($progress_data)->get();
                if ($progress->count() == 0) {
                    Progress::create($progress_data);
                }
            }

            $unit_model = Unit::find($topic_model->unit_id)->first();
            $units = Unit::where(['level_id' => $unit_model->level_id, 'dependency' => 1])->get();
            $check = true;
            foreach ($units as $unit) {
                $progress_data['entity_id'] = $unit->id;
                $progress = Progress::where($progress_data)->get();
                if ($progress->count() == 0) {
                    $check = false;
                    break;
                }
            }
            if ($check == true) {
                $progress_data['entity_id'] = $unit_model->level_id;
                $progress_data['entity_type'] = 3;
                $progress = Progress::where($progress_data)->get();
                if ($progress->count() == 0) {
                    Progress::create($progress_data);
                }
            }

        }

        return $this->success('OK.');
    }
}
