<?php

namespace App\Http\APIControllers;

use App\Country;
use App\Setting;

class HomeController extends Controller
{

    public function getWelcomeTexts()
    {
        $welcome_texts = Setting::where('key', 'LIKE', 'Home%')->orderBy('id', 'asc')->get();
        return $this->success($welcome_texts);
    }

    public function getCountries()
    {
        $countries = Country::orderBy('id', 'asc')->get();
        return $this->success($countries);
    }
}
