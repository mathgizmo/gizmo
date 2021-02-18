<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ClassApplicationStudent extends Model
{
    protected $table = 'classes_applications_students';

    protected $fillable = ['class_app_id', 'student_id', 'is_revealed', 'attempts_count', 'resets_count'];

    public function student() {
        return $this->belongsTo('App\Student', 'student_id', 'id');
    }

    public function classApplication() {
        return $this->belongsTo('App\ClassApplication', 'class_app_id', 'id');
    }

    public $timestamps = false;
}
