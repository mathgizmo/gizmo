<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestErrorMail extends Mailable
{
    use Queueable, SerializesModels;

    public $message;
    public $subject;

    public function __construct($student, $class, $test = null)
    {
        $this->subject = 'Test Error';
        $app = $test ? $test->test : null;
        $this->message = 'Hey there, <br/>there has been an error in a test '.($app ? $app->name : '').' written by <a href="mailto:'.$student->email.'">'.$student->first_name.' '.$student->last_name.'</a> (class '.$class->name.')!';
    }

    public function build()
    {
        return $this->subject($this->subject)
            ->markdown('mails.default_template');
    }
}
