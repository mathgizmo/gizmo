<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class JSON
{
    use \Dingo\Api\Routing\Helpers;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $response = $next($request);
        error_log(print_r($response, true));
        if ($response instanceof \Illuminate\Http\JsonResponse) {
            return $response;
        }
        else if ($response instanceof \Dingo\Api\Http\Response) {
            if (isset($response->original['message']) && isset($response->original['status_code'])) {
                return $response->original;
            }
            return response()->json([
                'success' => true,
                'message' => $response->original,
                'status_code' => 200
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong',
            'status_code' => 500
        ], 500);
    }
}
