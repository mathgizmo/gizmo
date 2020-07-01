<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MailsHistory extends Model
{
    protected $table = 'mails_history';

    protected $fillable = ['id', 'mail_type', 'student_id', 'class_id'];

    public $timestamps = true;

}
