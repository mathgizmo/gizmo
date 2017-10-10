<?php

namespace App\Http\APIControllers;

use Illuminate\Http\Request;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function authenticate(Request $request)
    {
        // grab credentials from the request
        $credentials = $request->only('email', 'password');

        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                throw new \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException('','invalid_credentials');
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            throw new \Symfony\Component\HttpKernel\Exception\HttpException(500, 'could_not_create_token');
        }

        // all good so return the token
        return $this->success(compact('token'));
    }
}
