<?php

namespace App\Http\AdminControllers;

use App\Setting;

class SettingController extends Controller
{
    public function __construct()
    {
        // $this->authorizeResource(Setting::class);
    }

    public function index()
    {
        $this->checkAccess(auth()->user()->isSuperAdmin());
        $settings = Setting::orderBy('key', 'ASC')->get();
        return view('settings.index', compact('settings'));
    }

    public function update()
    {
        $this->checkAccess(auth()->user()->isSuperAdmin());
        Setting::find(request('id'))->update([
            'value' => trim(request('value')),
        ]);
        return back();
    }
}
