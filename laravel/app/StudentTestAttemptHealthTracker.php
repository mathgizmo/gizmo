<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class StudentTestAttemptHealthTracker extends Model
{
    protected $table = 'students_test_attempt_health_trackers';

    protected $fillable = ['attempt_id'];

    public function attempt() {
        return $this->belongsTo('App\StudentTestAttempt', 'attempt_id', 'id');
    }

    public $timestamps = true;
}
