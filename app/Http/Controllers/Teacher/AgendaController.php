<?php

namespace App\Http\Controllers\Teacher;

use App\Models\Agenda;
use App\Models\Student;
use App\Models\Schedule;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class AgendaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        if (request()->ajax()) {
            $agendas = Agenda::whereHas('schedule', function ($query) {
                $query->whereHas('subject', function ($query2) {
                    $query2->where('teacher_id', Auth::user()->teacher->id);
                });
            });

            return DataTables::of($agendas)
                ->make();
        }
        return view('teacher.agenda.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $today = now()->dayOfWeek;
        $days = [
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
        ];

        return view('teacher.agenda.create', [
            'schedules' => Schedule::whereHas('subject', function ($query) {
                $query->where('teacher_id', Auth::user()->teacher->id);
            })->where('day', $days[$today])->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'schedule_id' => 'required',
            'note' => 'required|max:255|string',
        ]);

        Agenda::create([
            'schedule_id' => $validatedData['schedule_id'],
            'note' => $validatedData['note'],
        ]);

        return redirect('/guru/agenda')->with('success', 'Agenda Berhasil Ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Agenda $agenda)
    {
        $schedule = $agenda->schedule;

        $today = now()->dayOfWeek;
        $days = [
            0 => 'Minggu',
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
        ];

        $students = Student::where('grade_id', $schedule->grade_id)
            ->where('major_id', $schedule->major_id)
            ->where('group_id', $schedule->group_id)
            ->get();

        $attendances = Attendance::whereDate('created_at', now())
            ->whereHas('student', function ($query) use ($schedule) {
                $query->where('grade_id', $schedule->grade_id)
                    ->where('major_id', $schedule->major_id)
                    ->where('group_id', $schedule->group_id);
            })->get();

        $studentAttendance = $students->map(function ($student) use ($attendances) {
            $attendance = $attendances->firstWhere('student_id', $student->id);

            return [
                'id' => $student->id,
                'name' => $student->name,
                'status' => $attendance ? $attendance->status : 'Belum Absen',
                'created_at' => $attendance ? $attendance->created_at->format('Y-m-d H:i:s') : '-',
            ];
        });

        return view('teacher.agenda.edit', [
            'schedules' => Schedule::whereHas('subject', function ($query) {
                $query->where('teacher_id', Auth::user()->teacher->id);
            })->where('day', $days[$today])->get(),
            'studentAttendance' => $studentAttendance,
            'agenda' => $agenda
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Agenda $agenda)
    {
        $rules = [
            'schedule_id' => 'required',
            'note' => 'required|max:255|string',
        ];

        $validatedData = $request->validate($rules);

        Agenda::findOrFail($agenda->id)->update([
            'schedule_id' => $validatedData['schedule_id'],
            'note' => $validatedData['note'],
        ]);

        return redirect('/guru/agenda')->with('success', 'Agenda Berhasil Diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Agenda $agenda)
    {
        Agenda::destroy($agenda->id);

        return redirect('/guru/agenda')->with('success', 'Agenda Berhasil Dihapus!');
    }


    public function getClass(Request $request)
    {
        $scheduleId = $request->input('schedule_id');

        $schedule = Schedule::where('id', $scheduleId)->first();

        $students = Student::where('grade_id', $schedule->grade_id)
            ->where('major_id', $schedule->major_id)
            ->where('group_id', $schedule->group_id)
            ->get();

        $attendances = Attendance::whereDate('created_at', now())
            ->whereHas('student', function ($query) use ($schedule) {
                $query->where('grade_id', $schedule->grade_id)
                    ->where('major_id', $schedule->major_id)
                    ->where('group_id', $schedule->group_id);
            })->get();

        $result = $students->map(function ($student) use ($attendances) {

            $attendance = $attendances->firstWhere('student_id', $student->id);

            return [
                'id' => $student->id,
                'name' => $student->name,
                'status' => $attendance ? $attendance->status : 'Belum Absen',
                'created_at' => $attendance ? $attendance->created_at->format('Y-m-d H:i:s') : '-',
            ];
        });

        return response()->json($result, 200);

    }
}
