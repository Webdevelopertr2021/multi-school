<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware("auth");
    }

    public function getSettingData()
    {
        $setting = GeneralSetting::findOrFail(1);
        return response()->json($setting);
    }

    public function updateData(Request $req)
    {
        $setting = GeneralSetting::findOrFail(1);

        $setting->bonus_per_star = $req->per_star??0;
        $setting->bonus_no_leave = $req->no_leave??0;
        $setting->save();

        return [
            "status" => "ok",
            "msg" => "Data updated successfully",
        ];
    }
}
