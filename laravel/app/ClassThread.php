<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClassThread extends Model
{
    protected $table = 'class_threads';

    protected $fillable = ['class_id', 'student_id', 'title', 'message'];

    public function classOfStudents() {
        return $this->belongsTo('App\ClassOfStudents', 'class_id', 'id');
    }

    public function student() {
        return $this->belongsTo('App\Student', 'student_id', 'id');
    }

    public function replies() {
        return $this->hasMany('App\ClassThreadReply', 'thread_id', 'id');
    }

    public function getRepliesCount() {
        return $this->replies()->count();
    }

    public $timestamps = true;
}
