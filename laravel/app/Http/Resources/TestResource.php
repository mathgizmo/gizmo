<?php

namespace App\Http\Resources;

use App\Question;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class TestResource extends JsonResource
{
    protected $student;

    public function __construct($resource, $student = null)
    {
        parent::__construct($resource);
        $this->resource = $resource;
        $this->student = $student;
    }

    public function toArray($request)
    {
        $test = [
            'id' => $this->id,
            'class_id' => $this->class_id,
            'app_id' => $this->app_id,
            'start_date' => $this->start_date,
            'start_time' => $this->start_time,
            'due_date' => $this->due_date,
            'due_time' => $this->due_time,
            'duration' => $this->duration,
            'has_password' => $this->password ? true : false,
        ];
        if ($this->student) {
            $class_student = DB::table('classes_students')
                ->where('class_id', $this->class_id)
                ->where('student_id', $this->student->id)->first();
            $duration = $this->duration && $class_student
                ? ($this->duration * $class_student->test_duration_multiply_by)
                : ($this->duration ?: null);
            $test['duration'] = $duration;
        }
        $app = $this->application ?: null;
        $test['name'] = $app ? $app->name : $this->app_id;
        $test['allow_any_order'] = $app ? ($app->allow_any_order ? true : false) : false;
        $test['allow_back_tracking'] = $app ? ($app->allow_back_tracking ? true : false) : false;
        $questions = [];
        $lesson_ids = $app->getLessonsQuery()->orderBy('order_no', 'ASC')->orderBy('id', 'ASC')->pluck('id');
        foreach ($lesson_ids as $lesson_id) {
            $questions[] = Question::with('answers')->where('lesson_id', $lesson_id)->inRandomOrder()->first();
        }
        $test['questions'] = $questions;
        return $test;
    }
}
