<?php

namespace App\Http\Controllers\Admin;

use App\Models\Grade;
use App\Models\Group;
use App\Models\Major;
use App\Models\Subject;
use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $schedule = Schedule::all();

            return DataTables::of($schedule)
                ->make();
        }
        return view('dashboard.schedule.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $grades = Grade::pluck('name', 'id');
        $majors = Major::pluck('acronym', 'id');
        $groups = Group::pluck('number', 'id');

        $rombels = [];
        foreach ($grades as $gradeId => $gradeName) {
            foreach ($majors as $majorId => $majorName) {
                foreach ($groups as $groupId => $groupName) {
                    $rombels[] = [
                        'id' => "{$gradeId} {$majorId} {$groupId}",
                        'name' => "{$gradeName} {$majorName} {$groupName}"
                    ];
                }
            }
        }

        return view('dashboard.schedule.create', [
            'subjects' => Subject::orderBy('name')->get(),
            'rombels' => $rombels
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'subject_id' => 'required',
            'day' => 'required|max:100|string',
            'time_in' => 'required',
            'time_out' => 'required',
            'rombel' => 'required',
        ]);
        list($gradeId, $majorId, $groupId) = explode(' ', $validatedData['rombel']);

        Schedule::create([
            'subject_id' => $validatedData['subject_id'],
            'grade_id' => $gradeId,
            'major_id' => $majorId,
            'group_id' => $groupId,
            'day' => $validatedData['day'],
            'time_in' => $validatedData['time_in'],
            'time_out' => $validatedData['time_out'],
        ]);

        return redirect('/schedule')->with('success', 'Jadwal Mapel Berhasil Ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Schedule $schedule)
    {
        $grades = Grade::pluck('name', 'id');
        $majors = Major::pluck('acronym', 'id');
        $groups = Group::pluck('number', 'id');

        $rombels = [];
        foreach ($grades as $gradeId => $gradeName) {
            foreach ($majors as $majorId => $majorName) {
                foreach ($groups as $groupId => $groupName) {
                    $rombels[] = [
                        'id' => "{$gradeId} {$majorId} {$groupId}",
                        'name' => "{$gradeName} {$majorName} {$groupName}"
                    ];
                }
            }
        }

        return view('dashboard.schedule.edit', [
            'subjects' => Subject::orderBy('name')->get(),
            'schedule' => $schedule,
            'rombels' => $rombels
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Schedule $schedule)
    {
        $rules = [
            'subject_id' => 'required',
            'day' => 'required|max:100|string',
            'time_in' => 'required',
            'time_out' => 'required',
            'rombel' => 'required',
        ];

        $validatedData = $request->validate($rules);

        list($gradeId, $majorId, $groupId) = explode(' ', $validatedData['rombel']);

        Schedule::findOrFail($schedule->id)->update([
            'subject_id' => $validatedData['subject_id'],
            'grade_id' => $gradeId,
            'major_id' => $majorId,
            'group_id' => $groupId,
            'day' => $validatedData['day'],
            'time_in' => $validatedData['time_in'],
            'time_out' => $validatedData['time_out'],
        ]);

        return redirect('/schedule')->with('success', 'Jadwal Mapel Berhasil Diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Schedule $schedule)
    {
        Schedule::destroy($schedule->id);

        return redirect('/schedule')->with('success', 'Jadwal Mapel Berhasil Dihapus!');
    }
}
