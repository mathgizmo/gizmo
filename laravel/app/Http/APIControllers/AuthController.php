<?php

namespace App\Http\APIControllers;

use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\DB;
use Validator;
use App\Student;
use App\PasswordResets;
use Mail;

class AuthController extends Controller
{
    public function authenticate(Request $request)
    {
        auth()->shouldUse('api');
        // grab credentials from the request
        $credentials = $request->only('email', 'password');

        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return $this->error('invalid_credentials');
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            throw new \Symfony\Component\HttpKernel\Exception\HttpException(500, 'could_not_create_token');
        }
        //if user is admin update corresponding field
        $student_id = JWTAuth::getPayload($token)->get('sub');
        DB::unprepared("UPDATE students s LEFT JOIN users u ON s.email = u.email SET s.is_admin = IF(u.id, 1, 0) WHERE s.id = ".$student_id);
        $student = Student::find($student_id);
        $question_num = $student->question_num?:5;

        // all good so return the token
        return $this->success(compact('token', 'question_num'));
    }

    public function register(Request $request)
    {
        $fields = ['email', 'password', 'name'];
        // grab credentials from the request
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
        if ($validator->fails())
        {
            return $this->error($validator->messages());
        }

        $result = Student::create([
            'name' => $credentials['name'],
            'email' => $credentials['email'],
            'password' => bcrypt($credentials['password']),
        ]);

        if($result) {
            return $this->success($result);
        }

        return $this->success($error);
    }

    public function passwordResetEmail(Request $request) {
        $fields = ['email', 'url'];
        // grab credentials from the request
        $credentials = $request->only($fields);
        foreach($fields as $field) {
            $credentials[$field] = trim($credentials[$field]);
        }

        $validator = Validator::make(
            $credentials,
            [
                'email' => 'required|email|max:255',
                'url' => 'required'
            ]
            );
        if ($validator->fails())
        {
            return $this->error($validator->messages());
        }

        $email = $credentials['email'];

        $student = Student::where('email', '=' , $email)->first();
        if(!$student) {
            return $this->error('We can not find email you provided in our database! You can register a new account with this email.');
        }

        // delete existings resets if exists
        PasswordResets::where('email', $email)->delete();

        $url = $credentials['url'];
        $token = str_random(64);
        $result = PasswordResets::create([
            'email' => $email,
            'token' => $token
        ]);

        if($result) {
            $data = array('token'=>$token, 'email' => $email, 'url' => $url);
            Mail::queue('mail', $data, function($message) use ($data) {
               $message->to($data['email'], $data['email'])->subject
                  ('Password Reset at MathGizmo');
               $message->from('mathgizmo01@gmail.com','Gizmo Support');
            });
            return $this->success('The mail has been sent successfully!');
        }
        return $this->success($error);
    }

    public function resetPassword(Request $request) {
        $fields = ['password', 'token'];
        // grab credentials from the request
        $credentials = $request->only($fields);
        foreach($fields as $field) {
            $credentials[$field] = trim($credentials[$field]);
        }

        $validator = Validator::make(
            $credentials,
            [
                'password' => 'required|min:6',
                'token' => 'required'
            ]
            );
        if ($validator->fails())
        {
            return $this->error($validator->messages());
        }

        $token = $credentials['token'];
        $pr = PasswordResets::where('token', $token)->first(['email', 'created_at']);
        $email = $pr['email'];
        if(!$email) {
            return $this->error('Invalid reset password link!');
        }

        $dateCreated = strtotime($pr['created_at']);
        $expireInterval = 86400; // token expire interval in seconds (24 h)
        $currentTime = time();

        if($currentTime  - $dateCreated > $expireInterval) {
            return $this->error('The time to reset password has expired!');
        }

        $password = bcrypt($credentials['password']);
        $updatedRows = Student::where('email', $email)->update(['password' => $password]);
        if($updatedRows > 0) {
            PasswordResets::where('token', $token)->delete();
            return $this->success('The password has been changed successfully!');
        }
        return $this->success($error);
    }
}
