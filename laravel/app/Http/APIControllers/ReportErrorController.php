<?php

namespace App\Http\APIControllers;

use App\ReportError;
use App\Setting;
use Illuminate\Support\Facades\Mail;
use JWTAuth;
use App\Question;

class ReportErrorController extends Controller
{

    public function report($question)
    {
        if (($model = Question::find($question)) == null) {
            return $this->error('Invalid question.');
        }

        $student = JWTAuth::parseToken()->authenticate();

        ReportError::create([
            'student_id' => $student->id,
            'question_id' => $question,
            'answer_id' => request('answer_id'),
            'options' => request('options'),
            'comment' => request('comment'),
        ]);

        Mail::send('emails.report_error', [], function ($m) {
            $m->from(Setting::getValueByKey('admin_email'), 'Gizmo');

            $m->to(Setting::getValueByKey('admin_email'))->subject('New error report!');
        });

        return $this->success('OK.');
    }
}