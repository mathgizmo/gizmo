<?php

namespace App\API\APIControllers;

use App\API\Requests;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return array()
     */
    public function login(Request $request)
    {
        $json = $request->input();
        return $json;
    }
}
