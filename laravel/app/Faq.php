<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $table = 'faqs';

    protected $fillable = ['order_no', 'title', 'data', 'is_for_student', 'is_for_teacher'];

    public $timestamps = true;
}
