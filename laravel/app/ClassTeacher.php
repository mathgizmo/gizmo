<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClassTeacher extends Model
{
    protected $table = 'classes_teachers';

    protected $fillable = ['class_id', 'student_id', 'is_researcher', 'receive_emails_from_students'];

    public function teacher() {
        return $this->belongsTo('App\Student', 'student_id', 'id');
    }

    public function classOfStudents() {
        return $this->belongsTo('App\ClassOfStudents', 'class_id', 'id');
    }

    public $timestamps = false;
}
