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

        $user = JWTAuth::parseToken()->authenticate();

        if (StudentsTracking::where(['user_id' => $user->id, 'lesson_id' => $lesson])->first() != null) {
            return $this->error('You already start this lesson.');
        }

        StudentsTracking::create([
            'user_id' => $user->id,
            'lesson_id' => $lesson,
            'action' => 'start',
            'start_datetime' => request()->start_datetime ? request()->start_datetime : '',
            'weak_questions' => request()->weak_questions ? request()->weak_questions : '',
        ]);

        return $this->success('OK.');
    }

    public function done($lesson)
    {
        if (($model = Lesson::find($lesson)) == null) {
            return $this->error('Invalid lesson.');
        }

        $user = JWTAuth::parseToken()->authenticate();

        if (StudentsTracking::where(['user_id' => $user->id, 'lesson_id' => $lesson, 'action' => 'start'])->first() == null) {
            return $this->error('You can\'t done this lesson because you never start it.');
        }

        if (StudentsTracking::where(['user_id' => $user->id, 'lesson_id' => $lesson, 'action' => 'done'])->first() != null) {
            return $this->error('You already done this lesson.');
        }

        StudentsTracking::create([
            'user_id' => $user->id,
            'lesson_id' => $lesson,
            'action' => 'done',
            'start_datetime' => request()->start_datetime ? request()->start_datetime : '',
            'weak_questions' => request()->weak_questions ? request()->weak_questions : '',
        ]);

        return $this->success('OK.');
    }
}
