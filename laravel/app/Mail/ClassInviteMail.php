<?php

namespace App\Mail;

use App\ClassOfStudents;
use App\MailTemplate;
use App\Helpers\MailVariablesHelper;
use App\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Blade;

class ClassInviteMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $message;
    public $variables;

    public function __construct(Student $student, ClassOfStudents $class)
    {
        $variableHelper = new MailVariablesHelper();
        $this->variables = array_merge(
            $variableHelper->getStudentVariables($student),
            $variableHelper->getClassVariables($class)
        );
        $template = MailTemplate::where('mail_type', (new \ReflectionClass($this))->getName())->first();
        $this->subject = $template->subject;
        $this->message = $this->compileString($template->body, $this->variables);
    }

    public function build()
    {
        return $this->subject($this->subject)
            ->markdown('mails.default_template');
    }

    public static function getAvailableVariables() {
        $variableHelper = new MailVariablesHelper();
        return array_merge(
            $variableHelper->getStudentVariablesKeys(),
            $variableHelper->getClassVariablesKeys()
        );
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
