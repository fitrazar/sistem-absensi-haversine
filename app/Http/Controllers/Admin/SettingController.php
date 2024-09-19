<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $setting = Setting::first();
        return view('dashboard.setting.index', compact('setting'));
    }

    public function store(Request $request)
    {
        $setting = Setting::first();
        if ($setting) {
            if ($request->file('logo')) {
                if ($request->oldImage) {
                    Storage::delete($request->oldImage);
                }
                $logo = $request->file('logo')->store('setting-images');
            }

            $setting->update([
                'name' => $request->name,
                'coordinate' => $request->coordinate,
                'logo' => $logo,
            ]);
            return redirect()->back()->with('success', 'Setting Tersimpan');
        } else {
            $logo = NULL;
            if ($request->file('logo')) {
                $logo = $request->file('logo')->store('setting-images');
            }
            Setting::create([
                'name' => $request->name,
                'coordinate' => $request->coordinate,
                'logo' => $logo,
            ]);

            return redirect()->back()->with('success', 'Pengaturan Tersimpan');
        }
    }
}
