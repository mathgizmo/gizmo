<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dashboard extends Model
{
    protected $table = 'dashboards';

    protected $fillable = ['order_no', 'title', 'data'];

    public $timestamps = false;
}
