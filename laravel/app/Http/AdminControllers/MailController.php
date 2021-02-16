<?php

namespace App\Http\AdminControllers;

use App\ClassOfStudents;
use App\Mail\CustomMail;
use App\MailTemplate;
use App\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class MailController extends Controller
{
    public function __construct()
    {
        // $this->authorizeResource(MailTemplate::class);
    }

    public function index(MailTemplate $model)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin());
        return view('mail_templates.index', ['mails' => $model->all()]);
    }

    public function edit(MailTemplate $mail)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin());
        return view('mail_templates.edit', [
            'mail' => $mail,
            'available_variables' => $mail->mail_type::getAvailableVariables()
        ]);
    }

    public function update(Request $request, MailTemplate $mail)
    {
        $this->checkAccess(auth()->user()->isSuperAdmin());
        $mail->update([
            'subject' => $request['subject'],
            'body' => $request['body']
        ]);
        return redirect()->route('mails.index')->withStatus(__('settings.mail_template_updated_message'));
    }

    public function newMail() {
        $this->checkAccess(auth()->user()->isSuperAdmin());
        return view('mail_templates.new', [
            'available_variables' => \App\Mail\CustomMail::getAvailableVariables(),
            'available_class_variables' => \App\Mail\CustomMail::getAvailableClassVariables(),
        ]);
    }

    public function sendMail(Request $request) {
        $this->checkAccess(auth()->user()->isSuperAdmin());
        $this->validate($request, [
            'subject' => 'required',
            'body' => 'required',
        ]);
        if (config('app.env') == 'production') {
            try {
                if ($request['send_to'] == 'class') {
                    $class_ids = array_map(function ($item) { return intval($item); }, array_values($request['class']));
                    $classes = ClassOfStudents::whereIn('id', $class_ids)->get();
                    foreach ($classes as $class) {
                        foreach ($class->students()->get() as $student) {
                            try {
                                Mail::to($student->email)->send(new CustomMail($request['subject'], $request['body'], $student, $class));
                            } catch (\Exception $e) { }
                        }
                    }
                } else {
                    if ($request['for_all_students']) {
                        $students = Student::all();
                    } else {
                        $student_ids = array_map(function ($item) { return intval($item); }, array_values($request['student']));
                        $students = Student::whereIn('id', $student_ids)->get();
                    }
                    foreach ($students as $student) {
                        try {
                            Mail::to($student->email)->send(new CustomMail($request['subject'], $request['body'], $student, null));
                        } catch (\Exception $e) { }
                    }
                }
            } catch (\Exception $e) {
                return redirect()->route('mails.index')->withErrors(__('settings.mail_not_sent'));
            }
        }
        return redirect()->route('mails.index')->withStatus(__('settings.mail_sent_successfully'));
    }

}
