<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ErrorReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $student;
    public $error;

    public function __construct($student, $error)
    {
        $this->student = $student;
        $this->error = $error;
    }

    public function build()
    {
        return $this->subject('New error report!')
            ->markdown('mails.report_error');
    }
}
