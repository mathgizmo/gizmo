<?php

namespace App\Http\APIControllers;

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
                'email' => 'email|max:255|unique:students, email,' . $student->id,
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
}
