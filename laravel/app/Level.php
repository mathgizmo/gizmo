<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    protected $table = 'level';

    public function units()
    {
        return $this->hasMany('App\Unit', 'level_id', 'id');
    }
}
