<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MailTemplate extends Model
{
    protected $table = 'mail_templates';

    protected $fillable = ['id', 'mail_type', 'subject', 'body'];

    public $timestamps = false;

}
