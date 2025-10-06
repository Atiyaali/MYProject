<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Resources\SettingResource;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function getSettings()
    {
        $setting = Setting::latest()->first();
        $settingResource = new SettingResource($setting);
        return response()->json(array('data' => $settingResource));
    }
}
