<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentTestQuestion extends Model
{
    protected $table = 'students_test_questions';

    protected $fillable = ['student_id', 'question_id', 'topic_id', 'unit_id', 'level_id',
        'class_app_id', 'attempt_id', 'is_answered', 'is_right_answer', 'order_no'];

    public function student() {
        return $this->belongsTo('App\Student', 'student_id');
    }

    public function question() {
        return $this->belongsTo('App\Question', 'question_id');
    }

    public function topic() {
        return $this->belongsTo('App\Topic', 'topic_id');
    }

    public function unit() {
        return $this->belongsTo('App\Unit', 'unit_id');
    }

    public function level() {
        return $this->belongsTo('App\Level', 'level_id');
    }

    public function classApplication() {
        return $this->belongsTo('App\ClassApplication', 'class_app_id');
    }

    public function attempt() {
        return $this->belongsTo('App\StudentTestAttempt', 'attempt_id');
    }

    public $timestamps = true;
}
