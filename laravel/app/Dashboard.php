<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dashboard extends Model
{
    protected $table = 'dashboards';

    protected $fillable = ['order_no', 'title', 'data', 'is_for_student', 'is_for_teacher'];

    public $timestamps = false;
}
