<?php

namespace App\Observers;

use App\ClassOfStudents;
use App\Mail\ClassInviteMail;
use App\MailsHistory;
use App\Student;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ClassObserver
{

    public function created(ClassOfStudents $class)
    {
        $this->sendInvites($class);
    }

    public function updated(ClassOfStudents $class)
    {
        $this->sendInvites($class);
    }

    private function sendInvites(ClassOfStudents $class) {
        try {
            $emails = explode(',', strtolower(str_replace(' ', '', preg_replace( "/;|\n/", ',', $class->invitations))));
            if (config('app.env') == 'production' && $class->subscription_type == 'invitation') {
                foreach (array_filter($emails) as $email) {
                    try {
                        $student = Student::where('email', trim($email))->first();
                        if ($student) {
                            $subscribed = DB::table('classes_students')->where('class_id', $class->id)
                                ->where('student_id', $student->id)->exists();
                            $emailed = DB::table('mails_history')->where('mail_type', 'App\Mail\ClassInviteMail')
                                ->where('student_id', $student->id)->where('class_id', $class->id)->exists();
                            if (!$subscribed && !$emailed) {
                                Mail::to($student->email)->send(new ClassInviteMail($student, $class));
                                DB::table('mails_history')->insert([
                                    'mail_type' => 'App\Mail\ClassInviteMail',
                                    'student_id' => $student->id,
                                    'class_id' => $class->id,
                                    'created_at' => Carbon::now()->toDateTimeString()
                                ]);
                            }
                        }
                    } catch (\Exception $e) { }
                }
            }
        } catch (\Exception $e) { }
    }
}
