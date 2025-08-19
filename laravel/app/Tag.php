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
        // legacy one-to-one column
        return $this->hasMany('App\Application', 'tag_id');
    }

    public function classes()
    {
        // legacy one-to-one column
        return $this->hasMany('App\Classes', 'tag_id');
    }

    // New many-to-many relations
    public function applicationMany()
    {
        return $this->belongsToMany('App\Application', 'application_tag', 'tag_id', 'app_id');
    }

    public function classMany()
    {
        return $this->belongsToMany('App\ClassOfStudents', 'class_tag', 'tag_id', 'class_id');
    }
}
