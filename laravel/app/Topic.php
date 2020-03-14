<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    protected $table = 'topic';

    public function lessons()
    {
        return $this->hasMany('App\Lesson', 'topic_id', 'id');
    }

    public function unit()
    {
        return $this->belongsTo('App\Unit', 'unit_id');
    }
}
