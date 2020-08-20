<?php

namespace App\Http\APIControllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Dingo\Api\Routing\Helpers;

class Controller extends BaseController
{
    use \Dingo\Api\Routing\Helpers;

    protected function success($message, $code = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'status_code' => $code
        ], $code, [], JSON_NUMERIC_CHECK);
    }

    protected function error($message, $code = 500)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'status_code' => $code
        ], $code, [], JSON_NUMERIC_CHECK);
    }
}
