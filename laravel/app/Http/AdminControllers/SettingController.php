<?php

namespace App\Http\Controllers;

use App\Setting;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::orderBy('id', 'desc')->get();
        return view('setting_views.index', compact('settings'));
    }

    public function update()
    {
        Setting::find(request('id'))->update([
            'value' => request('value'),
        ]);
        return back();
    }
}
