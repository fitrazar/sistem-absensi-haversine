<?php

namespace App\Http\Controllers\Student;

use Carbon\Carbon;
use App\Models\Time;
use Carbon\CarbonPeriod;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class HistoryController extends Controller
{
    public function index(Request $request)
    {
        $student = Auth::user()->student;

        $dateStart = Carbon::createFromDate(2024, 9, 1);
        $dateEnd = $dateStart->copy()->addMonths(3)->endOfMonth();

        $periodDays = CarbonPeriod::create($dateStart, '1 day', $dateEnd);
        $dates = [];
        foreach ($periodDays as $date) {
            $dates[] = ['key' => $date->format('d-m-Y'), 'value' => $date->isoFormat('D MMMM YYYY')];
        }
        if ($request->ajax()) {
            $attendances = Attendance::query();
            $attendances->where('student_id', $student->id)->latest()->get();

            if ($request->has('from') && $request->has('to')) {
                if ($request->input('from') != 'All' && $request->input('from') != NULL && $request->input('to') != 'All' && $request->input('to') != NULL) {
                    $from = Carbon::parse($request->input('from'))->startOfDay();
                    $to = Carbon::parse($request->input('to'))->endOfDay();

                    if ($to->lt($from) || $from->gt($to)) {
                        return response()->json(['message' => 'Tanggal tidak sinkron'], 400);
                    }
                    $attendances->whereBetween('created_at', [$from, $to]);
                }
            }

            return DataTables::of($attendances)
                ->make();
        }

        return view('student.history.index', compact('dates'));
    }

    public function show(Attendance $attendance)
    {
        if ($attendance->student->id != Auth::user()->student->id) {
            abort(403);
        }
        $waktuAbsen = Time::first();
        return view('student.history.show', compact('attendance', 'waktuAbsen'));
    }

    public function destroy(Attendance $attendance)
    {
        Attendance::destroy($attendance->id);

        return redirect('/siswa/history')->with('success', 'Absen Berhasil Dihapus, Kamu Bisa Melakukan Absen Ulang.');
    }
}
