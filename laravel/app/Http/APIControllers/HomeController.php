<?php

namespace App\Http\APIControllers;

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
