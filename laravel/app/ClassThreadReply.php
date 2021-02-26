<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ClassThreadReply extends Model
{
    protected $table = 'class_thread_replies';

    protected $fillable = ['thread_id', 'student_id', 'parent_id', 'message'];

    public function thread() {
        return $this->belongsTo('App\ClassThread', 'thread_id', 'id');
    }

    public function student() {
        return $this->belongsTo('App\Student', 'student_id', 'id');
    }

    public function parent() {
        return $this->belongsTo('App\ClassThreadReply', 'parent_id', 'id');
    }

    public $timestamps = true;
}
