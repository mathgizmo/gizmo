<?php

namespace App\Http\APIControllers;

use App\Application;
use App\ClassOfStudents;
use App\Mail\PasswordResetMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\DB;
use App\Student;
use App\PasswordResets;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{

    public function authenticate(Request $request)
    {
        auth()->shouldUse('api');
        $credentials = $request->only('email', 'password');
        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return $this->error('invalid_credentials');
            }
        } catch (JWTException $e) {
            throw new \Symfony\Component\HttpKernel\Exception\HttpException(500, 'Could Not Create Token');
        }
        $student = auth()->user();
        DB::unprepared("UPDATE students s LEFT JOIN users u ON s.email = u.email SET s.is_admin = IF(u.id, 1, 0) WHERE s.id = ".$student->id);

        $student->question_num = $student->question_num ?: 5;
        $app = Application::where('id', $student->app_id)->first();
        if (!$app) {
            $app = Application::whereDoesntHave('teacher')->first();
            $student->app_id = $app->id ?: null;
            $student->save();
        }
        $app_id = $student->app_id;
        $role = 'student';
        if ($student->is_teacher) {
            $role = 'teacher';
        }
        $user = json_encode([
            'user_id' => $student->id,
            'username' => $student->name,
            'first_name' => $student->first_name,
            'last_name' => $student->last_name,
            'email' => $student->email,
            'role' => $role,
            'question_num' => $student->question_num
        ]);
        return $this->success(compact('token', 'app_id', 'user'));
    }

    public function register(Request $request)
    {
        $fields = ['email', 'password', 'name'];
        $credentials = $request->only($fields);
        foreach($fields as $field) {
            $credentials[$field] = trim($credentials[$field]);
        }
        $validator = Validator::make(
            $credentials,
            [
                'name' => 'required|max:255',
                'email' => 'required|email|max:255|unique:students',
                'password' => 'required|min:6',
            ]
            );
        if ($validator->fails()) {
            return $this->error($validator->messages());
        }
        $result = Student::create([
            'name' => $credentials['name'],
            'first_name' => $request['first_name'] ?: null,
            'last_name' => $request['last_name'] ?: null,
            'email' => $credentials['email'],
            'password' => bcrypt($credentials['password']),
            'is_super' => false,
            'is_teacher' => false,
            'is_admin' => false
        ]);
        if ($result) {
            return $this->success($result);
        }
        return $this->error('Something went wrong!');
    }

    public function passwordResetEmail(Request $request) {
        $fields = ['email', 'url'];
        $credentials = $request->only($fields);
        foreach($fields as $field) {
            $credentials[$field] = trim($credentials[$field]);
        }
        $validator = Validator::make($credentials,
            [ 'email' => 'required|email|max:255', 'url' => 'required' ]
        );
        if ($validator->fails()) {
            return $this->error($validator->messages());
        }
        $email = $credentials['email'];
        $student = Student::where('email', '=' , $email)->first();
        if(!$student) {
            return $this->error('We can not find email you provided in our database! You can register a new account with this email.');
        }
        PasswordResets::where('email', $email)->delete();
        $token = Str::random(64);
        $result = PasswordResets::create([
            'email' => $email,
            'token' => $token
        ]);
        if ($result) {
            Mail::to($email)->send(new PasswordResetMail($credentials['url'] . '/' . $token));
            return $this->success('The mail has been sent successfully!');
        }
        return $this->error('Something went wrong!');
    }

    public function resetPassword(Request $request) {
        $fields = ['password', 'token'];
        $credentials = $request->only($fields);
        foreach($fields as $field) {
            $credentials[$field] = trim($credentials[$field]);
        }
        $validator = Validator::make($credentials,
            [ 'password' => 'required|min:6', 'token' => 'required' ]
        );
        if ($validator->fails()) {
            return $this->error($validator->messages());
        }
        $token = $credentials['token'];
        $pr = PasswordResets::where('token', $token)->first(['email', 'created_at']);
        $email = $pr['email'];
        if (!$email) {
            return $this->error('Invalid reset password link!');
        }
        $dateCreated = strtotime($pr['created_at']);
        $expireInterval = 86400; // token expire interval in seconds (24 h)
        $currentTime = time();
        if ($currentTime - $dateCreated > $expireInterval) {
            return $this->error('The time to reset password has expired!');
        }
        $password = bcrypt($credentials['password']);
        $updatedRows = Student::where('email', $email)->update(['password' => $password]);
        if ($updatedRows > 0) {
            PasswordResets::where('token', $token)->delete();
            return $this->success('The password has been changed successfully!');
        }
        return $this->error('Something went wrong!');
    }
}
