<?php

namespace App\Http\Middleware;

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
                'message' => $response->original,
                'status_code' => 200
            ], 200);
        }
        return response()->json([
            'message' => 'Something went wrong',
            'status_code' => 500
        ], 500);
    }
}
