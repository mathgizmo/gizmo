<?php

namespace App\Http\APIControllers;

use App\Http\Requests;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return array
     */
    public function index()
    {
        return $this->success('this is API');
    }
}
