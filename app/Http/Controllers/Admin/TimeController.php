<?php

namespace App\Http\Controllers\Admin;

use App\Models\Time;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TimeController extends Controller
{
    public function index()
    {
        $time = Time::first();
        return view('dashboard.time.index', compact('time'));
    }

    public function store(Request $request)
    {
        Time::first()->update([
            'time_in_early' => $request->time_in_early,
            'time_in_lately' => $request->time_in_lately,
            'time_out_early' => $request->time_out_early,
            'time_out_lately' => $request->time_out_lately,
        ]);
        return redirect()->back()->with('success', 'Waktu Tersimpan');

    }
}
