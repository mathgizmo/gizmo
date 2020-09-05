<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $table = 'unit';

    public function topics()
    {
        return $this->hasMany('App\Topic', 'unit_id', 'id');
    }

    public function level()
    {
        return $this->belongsTo('App\Level', 'level_id');
    }
}
