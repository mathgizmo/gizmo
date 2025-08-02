<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $table = 'tag';

    protected $fillable = ['order_no', 'name'];

    public function units()
    {
        return $this->belongsToMany('App\Unit', 'tag_unit', 'tag_id', 'unit_id');
    }

    public function participants()
    {
        return $this->belongsToMany('App\Student', 'participant_tag', 'tag_id', 'participant_id');
    }

    public function applications()
    {
        return $this->hasMany('App\Application', 'tag_id');
    }

    public function classes()
    {
        return $this->hasMany('App\Classes', 'tag_id');
    }
}
