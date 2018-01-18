<?php

namespace App\Http\APIControllers;

use App\StudentsTracking;
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
        ]);

        return $this->success('OK.');
    }
}
