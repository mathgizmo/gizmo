<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentsTrackingQuestion extends Model
{
    protected $table = 'students_tracking_questions';

    protected $fillable = [
        'student_id', 'question_id', 'app_id', 'is_right_answer',
    ];

    public $timestamps = true;

    public function student() {
        return $this->belongsTo('App\Student', 'student_id');
    }

    public function question() {
        return $this->belongsTo('App\Question', 'question_id');
    }

    public function application() {
        return $this->belongsTo('App\Application', 'app_id');
    }
}
