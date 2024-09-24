<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Attendance;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        $hadir = Attendance::whereDate('created_at', $today)->where('status', 'like', '%terlambat%')->where('status', 'like', 'Masuk')->count();
        $sakit = Attendance::whereDate('created_at', $today)->where('status', 'like', 'Sakit')->count();
        $izin = Attendance::whereDate('created_at', $today)->where('status', 'like', 'Izin')->count();
        return view('dashboard', compact('hadir', 'sakit', 'izin'));
    }
}
