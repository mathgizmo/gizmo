<?php

namespace App\Http\APIControllers;

use App\ReportError;
use App\Setting;
use Illuminate\Support\Facades\Mail;
use JWTAuth;
use App\Question;

class ReportErrorController extends Controller
{

    /**
     * @param $question
     * @return mixed
     */
    public function report($question)
    {
        if (($model = Question::find($question)) == null) {
            return $this->error('Invalid question.');
        }
        $student = JWTAuth::parseToken()->authenticate();
        if(request('is_feedback')) {
            $answers = "";
            $options = "Feedback";
        } else {
            $options = request('options');
            $answers = request('answers');
            if (!is_array($answers)) {
                $answers = [$answers];
            }
            $answers = implode(";", $answers);
        }
        ReportError::create([
            'student_id' => $student->id,
            'question_id' => $question,
            'answers' => $answers,
            'options' => $options,
            'comment' => request('comment'),
            'is_feedback' => request('is_feedback'),
        ]);
        if (Setting::getValueByKey('admin_email')) {
            try {
                Mail::send('emails.report_error', [], function ($m) {
                    $m->from(Setting::getValueByKey('admin_email'), 'Gizmo');

                    $m->to(Setting::getValueByKey('admin_email'))->subject('New error report!');
                });
            } catch (\Exception $e) {

            }
        }
        return $this->success('OK.');
    }
}
