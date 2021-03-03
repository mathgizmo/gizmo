<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StudentTestAttempt extends Model
{
    protected $table = 'students_test_attempts';

    protected $fillable = ['attempt_no', 'test_student_id', 'questions_count', 'mark', 'start_at', 'end_at', 'is_error', 'error_emailed_at'];

    public function testStudent() {
        return $this->belongsTo('App\ClassApplicationStudent', 'test_student_id', 'id');
    }

    public function healthRequests() {
        return $this->hasMany('App\StudentTestAttemptHealthTracker', 'attempt_id', 'id');
    }

    public function isCorrupted() {
        if ($this->is_error) { return true; }
        if ($this->end_at) { return false; }
        $test_student = $this->testStudent;
        $test = $test_student ? $test_student->classApplication : null;
        $duration = $test ? $test->duration : 0;
        if ($duration) {
            $last_request = $this->healthRequests()->latest()->first();
            $last_action_time = $last_request ? $last_request->created_at : $this->start_at;
            return Carbon::now()->diffInSeconds(Carbon::parse($last_action_time)) > 180; // 3 min of inactive = corrupted
        }
        return false;
    }

    public $timestamps = false;
}
