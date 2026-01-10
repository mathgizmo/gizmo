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

    public function getAdSettings()
    {
        auth()->shouldUse('api');
        $user = null;
        try {
            if ($token = \Tymon\JWTAuth\Facades\JWTAuth::getToken()) {
                $user = \Tymon\JWTAuth\Facades\JWTAuth::authenticate($token);
            }
        } catch (\Exception $e) {}
        
        $adCode = Setting::where('key', 'ad_code')->first();
        $adMessage = Setting::where('key', 'ad_message')->first();
        
        $classId = request()->input('class_id');
        $assignmentId = request()->input('assignment_id');
        
        // If assignment_id is provided, get its class_id
        if ($assignmentId && !$classId) {
            $classApplication = \App\ClassApplication::where('app_id', $assignmentId)->first();
            if ($classApplication) {
                $classId = $classApplication->class_id;
            }
        }
        
        return $this->success([
            'has_donated' => $user ? $user->isAdFree($classId) : false,
            'ad_code' => $adCode ? $adCode->value : '',
            'ad_message' => $adMessage ? $adMessage->value : ''
        ]);
    }
}
