<?php

namespace App\Http\APIControllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Dingo\Api\Routing\Helpers;

class Controller extends BaseController
{
    use \Dingo\Api\Routing\Helpers;

    protected function success($message)
    {
        return $this->response->array(['success' => true, 'message' => $message, 'status_code' => 200]);
    }
    protected function error($message)
    {
        return $this->response->array(['success' => false, 'message' => $message, 'status_code' => 200]);
    }
}
