<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Progress extends Model
{
    protected $table = 'progresses';

    protected $guarded = [];

    public function application() {
        return $this->belongsTo('App\Application', 'app_id', 'id');
    }

    public function student() {
        return $this->belongsTo('App\Student', 'student_id', 'id');
    }

    public function lesson() {
        return $this->belongsTo('App\Lesson', 'entity_id', 'id')->where('entity_type', 'lesson');
    }

    public function topic() {
        return $this->belongsTo('App\Topic', 'entity_id', 'id')->where('entity_type', 'topic');
    }

    public function unit() {
        return $this->belongsTo('App\Unit', 'entity_id', 'id')->where('entity_type', 'unit');
    }

    public function level() {
        return $this->belongsTo('App\Level', 'entity_id', 'id')->where('entity_type', 'level');
    }

    public function assignment() {
        return $this->belongsTo('App\Application', 'entity_id', 'id')->where('entity_type', 'application');
    }

    public $timestamps = false;
}
