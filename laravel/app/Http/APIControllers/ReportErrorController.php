<?php

namespace App\Http\APIControllers;

use App\Mail\ErrorReportMail;
use App\ReportError;
use App\Setting;
use Illuminate\Support\Facades\Mail;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Question;

class ReportErrorController extends Controller
{

    private $user;

    public function __construct()
    {
        try {
            $this->user = JWTAuth::parseToken()->authenticate();
            if (!$this->user) {
                abort(401, 'Unauthorized!');
            }
        } catch (\Exception $e) {
            abort(401, 'Unauthorized!');
        }
    }

    public function report($question)
    {
        if (($model = Question::find($question)) == null) {
            return $this->error('Invalid question.');
        }
        $student = $this->user;
        if(request('is_feedback')) {
            $answers = '';
            $options = 'Feedback';
        } else {
            $options = request('options');
            $answers = request('answers');
            if (!is_array($answers)) {
                $answers = [$answers];
            }
            $answers = implode(';', $answers);
        }
        $error = ReportError::create([
            'student_id' => $student->id,
            'question_id' => $question,
            'answers' => $answers,
            'options' => $options,
            'comment' => request('comment'),
            'is_feedback' => request('is_feedback'),
        ]);
        if (Setting::getValueByKey('admin_email')) {
            try {
                Mail::to(Setting::getValueByKey('admin_email'))->send(new ErrorReportMail($student, $error));
            } catch (\Exception $e) {

            }
        }
        return $this->success('OK.');
    }
}
