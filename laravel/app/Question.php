<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    const UPDATED_AT = 'modified_at';

    protected $table = 'question';

    protected $guarded = [];

    public function answers()
    {
        return $this->hasMany(Answer::class, 'question_id', 'id');
    }

    public function lesson()
    {
        return $this->belongsTo('App\Lesson', 'lesson_id');
    }
}
