<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    const UPDATED_AT = 'modified_at';
    protected $guarded = [];
    protected $table = 'lesson';

    public function topic()
    {
        return $this->belongsTo('App\Topic', 'topic_id');
    }
}
