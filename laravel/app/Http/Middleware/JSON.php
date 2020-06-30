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
        if (!($response instanceof \Illuminate\Http\JsonResponse)) {
            if ($response instanceof \Illuminate\Http\Response || $response instanceof \Dingo\Api\Http\Response) {
                if (isset($response->original['message']) && isset($response->original['status_code'])) {
                    $response = response()->json([
                        'success' => isset($response->original['success'])
                            ? $response->original['success']
                            : $response->original['status_code'] == 200,
                        'message' => $response->original['message'],
                        'status_code' => $response->original['status_code']
                    ], $response->original['status_code']);
                }
            } else {
                $response = response()->json([
                    'success' => false,
                    'message' => 'Something went wrong',
                    'status_code' => 500
                ], 500);
            }
        }
        $response->headers->set('Access-Control-Allow-Origin', '*');
        return $response;
    }
}
