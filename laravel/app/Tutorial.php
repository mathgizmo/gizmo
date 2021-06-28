<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tutorial extends Model
{
    protected $table = 'tutorials';

    protected $fillable = ['order_no', 'title', 'data', 'is_for_student', 'is_for_teacher'];

    public $timestamps = true;
}
