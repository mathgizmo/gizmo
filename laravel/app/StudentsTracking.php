<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentsTracking extends Model
{
    protected $table = 'students_tracking';

    protected $guarded = [];

    public $timestamps = false;

    public function student() {
        return $this->belongsTo('App\Student', 'student_id');
    }

    public function lesson() {
        return $this->belongsTo('App\Lesson', 'lesson_id');
    }

    public function application() {
        return $this->belongsTo('App\Application', 'app_id');
    }
}
