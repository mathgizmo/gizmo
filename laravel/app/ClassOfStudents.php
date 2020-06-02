<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ClassOfStudents extends Model
{
    protected $table = 'classes';

    protected $fillable = ['id', 'name', 'teacher_id', 'subscription_type', 'invitations'];

    public function teacher() {
        return $this->belongsTo('App\Student', 'teacher_id');
    }

    public function students() {
        return $this->belongsToMany('App\Student', 'classes_students', 'class_id', 'student_id');
    }

    public function applications() {
        return $this->belongsToMany('App\Application', 'classes_applications', 'class_id', 'app_id');
    }

    public $timestamps = false;
}
