<?php

namespace App\Http\APIControllers;

use App\Application;
use App\ClassOfStudents;
use App\Progress;
use App\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProfileController extends Controller
{

    public function get()
    {
        $student = JWTAuth::parseToken()->authenticate();
        return $this->success([
            'name' => $student->name,
            'first_name' => $student->first_name,
            'last_name' => $student->last_name,
            'email' => $student->email,
            'question_num' => $student->question_num,
        ]);
    }

    public function update()
    {
        $student = JWTAuth::parseToken()->authenticate();
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
        $student = JWTAuth::parseToken()->authenticate();
        $items = Application::whereHas('classes', function ($q1) use ($student) {
            $q1->whereHas('students', function ($q2) use ($student) {
                $q2->where('students.id', $student->id);
            });
        })->get();
        foreach ($items as $item) {
            $item->icon = $item->icon();
            $item->is_completed = Progress::where('entity_type', 'application')->where('entity_id', $item->id)
                    ->where('student_id', $student->id)->count() > 0;
            $item->classes = $item->classes()->whereHas('students', function ($q) use ($student) {
                $q->where('students.id', $student->id);
            })->get();
            $item->class = null;
            $item->due_date = null;
            if ($item->classes) {
                foreach ($item->classes as $class) {
                    $class->due_date = $item->getDueDate($class->id);
                    if (!$item->due_date || $class->due_date < $item->due_date) {
                        $item->due_date = $class->due_date;
                        $item->completed_at = $item->getCompletedDate($student->id);
                        $item->class = $class;
                    }
                }
            }
        }
        return $this->success([
            'items' => array_values($items->sortBy('due_date')->toArray())
        ]);
    }

    public function updateToDos() {
        $student = JWTAuth::parseToken()->authenticate();
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
        $student = JWTAuth::parseToken()->authenticate();
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

    public function subscribeClass($class_id) {
        $student = JWTAuth::parseToken()->authenticate();
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
        $student = JWTAuth::parseToken()->authenticate();
        DB::table('classes_students')->where('class_id', $class_id)->where('student_id', $student->id)->delete();
        return $this->success('OK.');
    }
}
