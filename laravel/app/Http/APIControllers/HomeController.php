<?php

namespace App\Http\APIControllers;

use App\Country;
use App\Setting;
use Illuminate\Support\Facades\Request;

class HomeController extends Controller
{

    public function getSetting(Request $request, $key)
    {
        $setting = Setting::where('key', $key)->first();
        return $this->success($setting);
    }


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
