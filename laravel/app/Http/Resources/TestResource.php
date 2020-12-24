<?php

namespace App\Http\Resources;

use App\Question;
use Illuminate\Http\Resources\Json\JsonResource;

class TestResource extends JsonResource
{
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
        $app = $this->application ?: null;
        $test['name'] = $app ? $app->name : $this->app_id;
        // $test['icon'] = $app ? $app->icon() : '/images/default-icon.svg';
        $test['allow_any_order'] = $app ? ($app->allow_any_order ? true : false) : false;
        $questions = [];
        $lesson_ids = $app->getLessonsQuery()->orderBy('order_no', 'ASC')->orderBy('id', 'ASC')->pluck('id');
        foreach ($lesson_ids as $lesson_id) {
            $questions[] = Question::with('answers')->where('lesson_id', $lesson_id)->inRandomOrder()->first();
        }
        $test['questions'] = $questions;
        return $test;
    }
}
