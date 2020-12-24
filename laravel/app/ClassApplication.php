<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ClassApplication extends Model
{
    protected $table = 'classes_applications';

    protected $fillable = ['class_id', 'app_id', 'start_date', 'start_time',
        'due_date', 'due_time', 'color', 'duration', 'password', 'is_for_selected_students'];

    public function classOfStudents() {
        return $this->belongsTo('App\ClassOfStudents', 'class_id', 'id');
    }

    public function application() {
        return $this->belongsTo('App\Application', 'app_id', 'id');
    }

    public function assignment() {
        return $this->belongsTo('App\Application', 'app_id', 'id')->where('type', 'assignment');
    }

    public function test() {
        return $this->belongsTo('App\Application', 'app_id', 'id')->where('type', 'test');
    }

    public function students() {
        return $this->belongsToMany('App\Student', 'classes_applications_students', 'class_app_id', 'student_id');
    }

    public $timestamps = false;
}
