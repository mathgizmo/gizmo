<?php

namespace App\Http\APIControllers;

use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\DB;
use Validator;
use App\Student;

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

        // all good so return the token
        return $this->success(compact('token'));
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
}
