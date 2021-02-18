<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StudentTestAttempt extends Model
{
    protected $table = 'students_test_attempts';

    protected $fillable = ['attempt_no', 'test_student_id', 'questions_count', 'mark', 'start_at', 'end_at'];

    public function testStudent() {
        return $this->belongsTo('App\ClassApplicationStudent', 'test_student_id', 'id');
    }

    public $timestamps = false;
}
