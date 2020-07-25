<?php

namespace App\Http\APIControllers;

use App\Application;
use App\ClassOfStudents;
use App\Progress;
use App\Student;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProfileController extends Controller
{

    private $user;

    public function __construct()
    {
        try {
            $this->user = JWTAuth::parseToken()->authenticate();
            if (!$this->user) {
                abort(401, 'Unauthorized!');
            }
        } catch (\Exception $e) {
            abort(401, 'Unauthorized!');
        }
    }
    
    public function get()
    {
        $student = $this->user;
        return $this->success([
            'name' => $student->name,
            'first_name' => $student->first_name,
            'last_name' => $student->last_name,
            'email' => $student->email,
            'question_num' => $student->question_num,
            'app_id' => $student->app_id,
            'applications' => Application::whereDoesntHave('teacher')->get()
        ]);
    }

    public function update()
    {
        $student = $this->user;
        $update = [];
        if (request()->has('name')) {
            $update['name'] = request('name');
        }
        if (request()->has('first_name')) {
            $update['first_name'] = request('first_name');
        }
        if (request()->has('last_name')) {
            $update['last_name'] = request('last_name');
        }
        if (request()->has('email')) {
            $update['email'] = request('email');
        }
        if (request()->has('password')) {
            $update['password'] = request('password');
        }
        $validator = Validator::make(
            $update,
            [
                'name' => 'max:255',
                'email' => 'email|max:255|unique:students,email,'.$student->id,
                'password' => 'min:6',
            ]
        );
        if ($validator->fails()) {
            return $this->error($validator->messages());
        }
        if (request()->has('password')) {
            $update['password'] = bcrypt(request('password'));
        }
        if (request()->has('question_num')) {
            $question_num = request('question_num');
            if (!is_numeric($question_num)) {
                return $this->error('question_num must be an integer');
            }
            if ($question_num < 0) {
                $question_num = 0;
            }
            $update['question_num'] = (int)$question_num;
        }
        Student::find($student->id)->update($update);
        return $this->success('OK.');
    }

    public function getToDos() {
        $student = $this->user;
        $items = [];
        foreach (DB::table('classes_applications')->whereIn('class_id', $student->classes()->get()->pluck('id')->toArray())->get() as $row) {
            $item = Application::where('id', $row->app_id)->first();
            $item->class = ClassOfStudents::where('id', $row->class_id)->first();
            $item->icon = $item->icon();
            $item->is_completed = Progress::where('entity_type', 'application')->where('entity_id', $item->id)
                    ->where('student_id', $student->id)->count() > 0;
            if ($row->due_date) {
                $due_at = $row->due_time ? $row->due_date.' '.$row->due_time : $row->due_date.' 00:00:00';
            } else {
                $due_at = null;
            }
            $item->start_time = $row->start_time ?: '00:00:00';
            $item->start_date = $row->start_date;
            $item->due_time = $row->due_time ?: '00:00:00';
            $item->due_date = $row->due_date;
            $item->due_at = $due_at ? Carbon::parse($due_at)->format('Y-m-d g:i A') : null;
            $now = Carbon::now()->toDateTimeString();
            if ($row->start_date) {
                $start_at = $row->start_time ? $row->start_date.' '.$row->start_time : $row->start_date.' 00:00:00';
            } else {
                $start_at = null;
            }
            $item->is_blocked = ($start_at && $now < $start_at) || ($due_at && $now > $due_at);
            $item->start_at = $start_at && $now < $start_at ? Carbon::parse($start_at)->format('Y-m-d g:i A') : null;
            $completed_at = $item->getCompletedDate($student->id);
            $item->completed_at = $completed_at ? Carbon::parse($completed_at)->format('Y-m-d g:i A') : null;
            array_push($items, $item);
        }
        return $this->success([
            'items' => array_values(collect($items)->sortBy('due_at')->toArray())
        ]);
    }

    public function changeApplication() {
        $student = $this->user;
        if (request()->has('app_id')) {
            $user = Student::find($student->id);
            $user->app_id = request('app_id');
            $user->save();
            return $this->success('OK.');
        } else {
            return $this->error('Error.');
        }
    }

    public function getClasses() {
        $student = $this->user;
        $my_classes = ClassOfStudents::whereHas('students', function ($q) use ($student) {
            $q->where('students.id', $student->id);
        })->orderBy('name')->get();
        foreach ($my_classes as $item) {
            $teacher = Student::where('id', $item->teacher_id)->first();
            $item->teacher = $teacher ? $teacher->first_name.' '.$teacher->last_name : '';
        }
        $available_classes = ClassOfStudents::where(function ($q1) use ($student) {
            $q1->where('subscription_type', 'open')->orWhere(function ($q2) use ($student) {
                $q2->where('subscription_type', 'invitation')->where('invitations', 'LIKE', '%'.$student->email.'%');
            });
        })->whereNotIn('id', $my_classes->pluck('id')->toArray())->orderBy('name')->get();
        foreach ($available_classes as $item) {
            $teacher = Student::where('id', $item->teacher_id)->first();
            $item->teacher = $teacher ? $teacher->first_name.' '.$teacher->last_name : '';
        }
        return $this->success([
            'my_classes' => array_values($my_classes->toArray()),
            'available_classes' => array_values($available_classes->toArray()),
        ]);
    }

    public function getClassInvitations() {
        $student = $this->user;
        $my_classes = ClassOfStudents::whereHas('students', function ($q) use ($student) {
            $q->where('students.id', $student->id);
        })->orderBy('name')->get();
        $class_invitations = ClassOfStudents::where(function ($q1) use ($student) {
            $q1->where('subscription_type', 'invitation')->where('invitations', 'LIKE', '%'.$student->email.'%');
        })->whereNotIn('id', $my_classes->pluck('id')->toArray())->orderBy('name')->get();
        foreach ($class_invitations as $item) {
            $teacher = Student::where('id', $item->teacher_id)->first();
            $item->teacher = $teacher ? $teacher->first_name.' '.$teacher->last_name : '';
        }
        return $this->success([
            'items' => array_values($class_invitations->toArray()),
        ]);
    }

    public function subscribeClass($class_id) {
        $student = $this->user;
        $class = ClassOfStudents::where('id', $class_id)->first();
        $exists = DB::table('classes_students')->where('class_id', $class_id)->where('student_id', $student->id)->first();
        if ($class && !$exists) {
            DB::table('classes_students')->insert([
                'class_id' => $class_id,
                'student_id' => $student->id
            ]);
            return $this->success('OK.');
        } else {
            return $this->error('Error.');
        }
    }

    public function unsubscribeClass($class_id) {
        $student = $this->user;
        DB::table('classes_students')->where('class_id', $class_id)->where('student_id', $student->id)->delete();
        return $this->success('OK.');
    }
}
