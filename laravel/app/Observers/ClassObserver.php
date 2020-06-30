<?php

namespace App\Observers;

use App\ClassOfStudents;
use App\Mail\ClassInviteMail;
use App\Student;
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
            if (config('app.env') == 'production' && $class->subscription_type == 'invitation') {
                foreach (explode(',', $class->invitations) as $email) {
                    try {
                        $student = Student::where('email', trim($email))->first();
                        if ($student) {
                            $subscribed = DB::table('classes_students')->where('class_id', $class->id)
                                ->where('student_id', $student->id)->exists();
                            if (!$subscribed) {
                                Mail::to($student->email)->send(new ClassInviteMail($student, $class));
                            }
                        }
                    } catch (\Exception $e) { }
                }
            }
        } catch (\Exception $e) { }
    }
}
