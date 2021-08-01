<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;


class VerifyJWTToken
{

    public function handle($request, Closure $next)
    {
        try {
            JWTAuth::toUser($request->input('token'));
            // if (!$user = JWTAuth::parseToken()->authenticate()) {
            //     return response()->json(['user_not_found'], 401);
            // }
            // $request->merge(['auth_user' => $user]);
        } catch (JWTException $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return response()->json(['token_expired'], $e->getStatusCode());
            } else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json(['token_invalid'], $e->getStatusCode());
            } else {
                return response()->json(['error' => 'Token is required'], 401);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unauthorized!'], 401);
        }
        return $next($request);
    }
}
