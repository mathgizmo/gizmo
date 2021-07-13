<?php

namespace App\Http\APIControllers;

use App\Application;
use App\Http\Resources\AuthStudentResource;
use App\Mail\PasswordResetMail;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
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

    public function __construct()
    {
        $this->middleware('throttle:6,1')->only('verifyEmail', 'resendVerificationEmail', 'checkEmail');
        $this->middleware('signed')->only('verifyEmail');
    }

    public function login(Request $request)
    {
        auth()->shouldUse('api');
        if ($request->filled('ignore-captcha-key') && request('ignore-captcha-key') == config('auth.recaptcha.key')) {
            $validator = Validator::make(
                $request->only(['email', 'password']),
                [
                    'email' => 'required|email|max:255',
                    'password' => 'required',
                ]
            );
        } else {
            $validator = Validator::make(
                $request->only(['email', 'password', 'g-recaptcha-response']),
                [
                    'email' => 'required|email|max:255',
                    'password' => 'required',
                    'g-recaptcha-response' => 'required|recaptcha',
                ]
            );
        }
        if ($validator->fails()) {
            return $this->error($validator->messages(), 400);
        }
        $credentials = $request->only('email', 'password');
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return $this->error('Email or password is incorrect!', 401);
            }
        } catch (JWTException $e) {
            return $this->error('Could Not Create Token!', 500);
        }
        $student = auth()->user();
        if (!$student->hasVerifiedEmail()) {
            return $this->error('Your email address is not verified!', 420);
        }
        DB::unprepared("UPDATE students s LEFT JOIN users u ON s.email = u.email SET s.is_admin = IF(u.id, 1, 0) WHERE s.id = ".$student->id);

        $app = Application::where('id', $student->app_id)->first();
        if (!$app) {
            $app = Application::whereDoesntHave('teacher')->first();
            $student->app_id = $app ? ($app->id ?: null) : null;
            $student->save();
        }
        return $this->success([
            'app_id' => $student->app_id,
            'token' => $token,
            'user' => new AuthStudentResource($student)
        ], 200);
    }

    public function loginByToken(Request $request)
    {
        auth()->shouldUse('api');
        if ($request->filled('token')) {
            try {
                $token = request('token');
                $student = JWTAuth::parseToken()->authenticate();
            } catch (\Exception $e) {
                return $this->error($e->getMessage(), 401);
            }
        } else {
            return $this->error('Token is Required!', 401);
        }
        $app = Application::where('id', $student->app_id)->first();
        if (!$app) {
            $app = Application::whereDoesntHave('teacher')->first();
            $student->app_id = $app ? ($app->id ?: null) : null;
            $student->save();
        }
        return $this->success([
            'app_id' => $student->app_id,
            'token' => $token,
            'user' => new AuthStudentResource($student)
        ], 200);
    }

    public function register(Request $request)
    {
        $fields = ['email', 'password'];
        $credentials = $request->only($fields);
        foreach ($fields as $field) {
            $credentials[$field] = trim($credentials[$field]);
        }
        $student = Student::where('email', $credentials['email'])
            ->where('is_registered', false)->first();
        if ($student) {
            if ($request->filled('ignore-captcha-key') && request('ignore-captcha-key') == config('auth.recaptcha.key')) {
                $validator = Validator::make(
                    $request->only(['password', 'g-recaptcha-response']),
                    [
                        'password' => 'required|min:6',
                    ]
                );
            } else {
                $validator = Validator::make(
                    $request->only(['password', 'g-recaptcha-response']),
                    [
                        'password' => 'required|min:6',
                        'g-recaptcha-response' => 'required|recaptcha',
                    ]
                );
            }
            if ($validator->fails()) {
                return $this->error($validator->messages(), 400);
            }
            $student = $student->update([
                'first_name' => request('first_name') ?: null,
                'last_name' => request('last_name') ?: null,
                'password' => bcrypt($credentials['password']),
                'country_id' => request('country_id') ? intval(request('country_id')): 1,
                'is_teacher' => request('role') == 'teacher',
                'is_self_study' => request('role') == 'self_study',
                'is_super' => request('role') == 'self_study',
                'is_registered' => true
            ]);
        } else {
            if ($request->filled('ignore-captcha-key') && request('ignore-captcha-key') == config('auth.recaptcha.key')) {
                $validator = Validator::make(
                    $request->only(['email', 'password', 'g-recaptcha-response']),
                    [
                        'email' => 'required|email|max:255|unique:students',
                        'password' => 'required|min:6'
                    ]
                );
            } else {
                $validator = Validator::make(
                    $request->only(['email', 'password', 'g-recaptcha-response']),
                    [
                        'email' => 'required|email|max:255|unique:students',
                        'password' => 'required|min:6',
                        'g-recaptcha-response' => 'required|recaptcha',
                    ]
                );
            }
            if ($validator->fails()) {
                return $this->error($validator->messages(), 400);
            }
            $student = Student::create([
                'first_name' => request('first_name') ?: null,
                'last_name' => request('last_name') ?: null,
                'email' => strtolower($credentials['email']),
                'password' => bcrypt($credentials['password']),
                'country_id' => request('country_id') ? intval(request('country_id')): 1,
                'is_teacher' => request('role') == 'teacher',
                'is_self_study' => request('role') == 'self_study',
                'is_super' => request('role') == 'self_study',
                'is_admin' => false,
                'is_registered' => true
            ]);
        }
        if ($student) {
            try {
                $this->resendVerificationEmail($request);
            } catch (\Exception $e) { }
            return $this->success($student);
        }
        return $this->error('Something went wrong!', 500);
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
            return $this->error($validator->messages(), 400);
        }
        $email = $credentials['email'];
        $student = Student::where('email', '=' , $email)->first();
        if(!$student) {
            return $this->error('We can not find email you provided in our database! You can register a new account with this email.', 404);
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
        return $this->error('Something went wrong!', 500);
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
            return $this->error($validator->messages(), 400);
        }
        $token = $credentials['token'];
        $pr = PasswordResets::where('token', $token)->first(['email', 'created_at']);
        $email = $pr['email'];
        if (!$email) {
            return $this->error('Invalid link!', 400);
        }
        $dateCreated = strtotime($pr['created_at']);
        $expireInterval = 86400; // token expire interval in seconds (24 h)
        $currentTime = time();
        if ($currentTime - $dateCreated > $expireInterval) {
            return $this->error('The time to reset password has expired!', 400);
        }
        $password = bcrypt($credentials['password']);
        $updatedRows = Student::where('email', $email)->update(['password' => $password]);
        if ($updatedRows > 0) {
            PasswordResets::where('token', $token)->delete();
            return $this->success('The password has been changed successfully!');
        }
        return $this->error('Something went wrong!', 500);
    }

    public function logout(Request $request) {
        JWTAuth::invalidate(JWTAuth::getToken());
        return $this->success('Logged Out!', 200);
    }

    public function verifyEmail(Request $request)
    {
        $student = Student::where('id', $request->route('id'))->first();
        if (!$student || ($request['hash'] != sha1($student->email) && $request['hash'] != sha1($student->email_new))) {
            return $this->error('User not found!', 404);
        }
        if ($student->markEmailAsVerified()) {
            event(new Verified($request->user()));
            if ($student->email_new) {
                $student->email = $student->email_new;
                $student->email_new = null;
                $student->save();
            }
            try {
                $message = (object) [
                    'message' => 'Email verified!',
                    'redirectUrl' => URL::to(Config::get('app.login_as_student_url'))
                        .'?token='.JWTAuth::fromUser($student)
                ];
                return $this->success($message, 200);
            } catch (\Exception $e) {
                return $this->success('Email verified!', 200);
            }
        }
        return $this->error('Wrong verification link!', 400);
    }

    public function resendVerificationEmail(Request $request)
    {
        if ($request->filled('email')) {
            $email = trim(strtolower(request('email')));
            $student = Student::where('email', $email)->orWhere('email_new', $email)->first();
            if ($student) {
                if (!$student->email_new && $student->hasVerifiedEmail()) {
                    return $this->error('User already have verified email!', 422);
                }
                $student->sendEmailVerificationNotification();
                return $this->success('We sent email verification link to your email address!', 201);
            }
        }
        return $this->error('User with email you provided not found!', 404);
    }

    public function checkEmail(Request $request)
    {
        $validator = Validator::make(
            $request->only(['email']), ['email' => 'required|email|max:255']
        );
        if ($validator->fails()) {
            return $this->error($validator->messages(), 400);
        }
        $user = Student::where('email', $request['email'])->first();
        return $this->success([
            'is_registered' => $user && $user->is_registered ? true : false
        ], 200);
    }

}
