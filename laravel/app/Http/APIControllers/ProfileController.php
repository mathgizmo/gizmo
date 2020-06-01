<?php

namespace App\Http\APIControllers;

use App\Application;
use App\Progress;
use App\Student;
use Validator;
use JWTAuth;

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
                'email' => 'email|max:255|unique:students,email,'.$student->id, // DO NOT ADD SPACES TO THIS STRING!!!
                'password' => 'min:6',
            ]
        );
        if ($validator->fails())
        {
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

    public function getApplications() {
        $student = JWTAuth::parseToken()->authenticate();
        $items = Application::all();
        foreach ($items as $item) {
            $item->icon = $item->icon();
            $item->is_completed = Progress::where('entity_type', 'application')->where('entity_id', $item->id)->where('student_id', $student->id)->count() > 0;
            $item->due_date = null;
        }
        return $this->success([
            'items' => $items->sortBy('due_date')
        ]);
    }

    public function updateApplication() {
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
}
