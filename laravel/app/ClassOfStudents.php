<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ClassOfStudents extends Model
{
    protected $table = 'classes';

    protected $fillable = ['id', 'name', 'teacher_id', 'class_type', 'subscription_type', 'invitations'];

    public function teacher() {
        return $this->belongsTo('App\Student', 'teacher_id');
    }

    public function students() {
        return $this->belongsToMany('App\Student', 'classes_students', 'class_id', 'student_id');
    }

    public function applications() {
        return $this->belongsToMany('App\Application', 'classes_applications', 'class_id', 'app_id');
    }

    public function delete()
    {
        DB::table('classes_applications')->where('class_id', $this->id)->delete();
        DB::table('classes_students')->where('class_id', $this->id)->delete();
        return parent::delete();
    }

    public $timestamps = false;
}
