<?php

namespace App\Http\APIControllers;

use App\Setting;

class HomeController extends Controller
{
    
    public function index()
    {
        return $this->success('this is API');
    }

    public function getWelcomeTexts()
    {
        $welcome_texts = Setting::where('key', 'LIKE', 'Home%')->orderBy('id', 'asc')->get();
        return $this->success($welcome_texts);
    }
}
