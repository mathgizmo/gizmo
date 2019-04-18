<?php

namespace App\Http\APIControllers;

use App\Setting;

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

     /**
     * return welcome texts.
     *
     * @return array
     */
    public function getWelcomeTexts()
    {
        $welcome_texts = Setting::where('key', 'LIKE', 'Home%')->orderBy('id', 'asc')->get();
        return $this->success($welcome_texts);
    }
}
