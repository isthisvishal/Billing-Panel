<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return AdminSetting::all();
    }

    public function show($key)
    {
        return AdminSetting::where('key', $key)->firstOrFail();
    }

    public function update(Request $request, $key)
    {
        $this->authorize('manage-settings');

        $value = $request->input('value');
        $encrypted = (bool)$request->input('encrypted', false);

        $setting = AdminSetting::updateOrCreate(['key' => $key], ['value' => $encrypted ? encrypt($value) : $value, 'encrypted' => $encrypted]);

        return $setting;
    }
}
