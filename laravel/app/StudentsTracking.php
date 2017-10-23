<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StudentsTracking extends Model
{
    protected $table = 'students_tracking';

    protected $guarded = [];

    public $timestamps = false;
}
