<?php

namespace App\Mail;

use App\ClassOfStudents;
use App\Helpers\MailVariablesHelper;
use App\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Blade;

class CustomMail extends Mailable
{
    use Queueable, SerializesModels;

    public $message;
    public $subject;
    public $variables;

    public function __construct($subject, $body, Student $student, ClassOfStudents $class = null)
    {
        $variableHelper = new MailVariablesHelper();
        $this->variables = array_merge(
            $variableHelper->getStudentVariables($student),
            $variableHelper->getClassVariables($class)
        );
        $this->subject = $subject;
        $this->message = $this->compileString($body, $this->variables);
    }

    public function build()
    {
        return $this->subject($this->subject)
            ->markdown('mails.default_template');
    }

    public static function getAvailableVariables() {
        $variableHelper = new MailVariablesHelper();
        return $variableHelper->getStudentVariablesKeys();
    }

    public static function getAvailableClassVariables() {
        $variableHelper = new MailVariablesHelper();
        return $variableHelper->getClassVariablesKeys();
    }

    private function compileString($value, array $args = array()) {
        $generated = Blade::compileString($value);
        ob_start() and extract($args, EXTR_SKIP);
        try {
            eval('?>'.$generated);
        }  catch (\Exception $e) {
            ob_get_clean(); return null;
        }
        $content = ob_get_clean();
        return $content;
    }
}
