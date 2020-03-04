<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    protected $table = 'lesson';

    public function topic()
    {
        return $this->belongsTo('App\Topic', 'topic_id');
    }
}
