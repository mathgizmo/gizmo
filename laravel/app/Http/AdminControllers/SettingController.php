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
        $settings = Setting::where('key', 'NOT LIKE', 'Home%')->orderBy('id', 'desc')->get();
        $welcome_texts = Setting::where('key', 'LIKE', 'Home%')->orderBy('id', 'asc')->get();
        return view('settings.index', compact('settings', 'welcome_texts'));
    }

    public function update()
    {
        $this->checkAccess(auth()->user()->isSuperAdmin());
        Setting::find(request('id'))->update([
            'value' => request('value'),
        ]);
        return back();
    }
}
