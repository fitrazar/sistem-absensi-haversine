<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Exports\AttendanceExport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // $sql = $attendances->toSql();

        if (request()->ajax()) {
            $attendances = Attendance::query();
            if ($user->roles->pluck('name')[0] == 'teacher') {
                $attendances->whereHas('schedule.subject', function ($query) use ($user) {
                    $query->where('teacher_id', $user->teacher->id);
                })->with('schedule.subject')->get();
            } else {
                $attendances->get();
            }

            if ($request->has('from') && $request->has('to')) {
                $from = Carbon::parse($request->input('from'))->startOfDay();
                $to = Carbon::parse($request->input('to'))->endOfDay();
                $attendances->whereBetween('created_at', [$from, $to]);
            }

            return DataTables::of($attendances)
                ->make();
        }
        return view('dashboard.attendance.index');
    }

    public function export(Request $request)
    {
        $from = $request->fromExport;
        $to = $request->toExport;
        return Excel::download(new AttendanceExport($from, $to), 'Data Absensi Siswa.xlsx');
    }

    public function show(Attendance $attendance)
    {
        return view('dashboard.attendance.show', compact('attendance'));
    }
}
