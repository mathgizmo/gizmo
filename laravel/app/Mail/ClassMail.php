<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ClassMail extends Mailable
{
    use Queueable, SerializesModels;

    public $message;
    public $subject;
    public $user;

    public function __construct($subject, $body, $user, $class)
    {
        $this->subject = $subject;
        $this->message = 'Hey there, <br/>you have a new message from <a href="mailto:'.$user->email.'">'.$user->first_name.' '.$user->last_name.'</a> (class '.$class->name.'):<br/>'
            .$body;
        $this->user = $user;
    }

    public function build()
    {
        return $this->from($this->user->email)
            ->subject($this->subject)
            ->markdown('mails.default_template');
    }
}
