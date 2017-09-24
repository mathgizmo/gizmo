<?php

namespace App\API\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class JSON
{
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
        if ($response instanceof \Illuminate\Http\JsonResponse) {
            return $response;
        }
        else if ($response instanceof \Illuminate\Http\Response) {
            return response()->json([
                'response' => $response->original,
                'status' => 200,
                'API_version' => '1.0'
            ], 200);
        }
        return response()->json([
            'response' => 'Something went wrong',
            'status' => 500,
            'API_version' => '1.0'
        ], 500);
    }
}
