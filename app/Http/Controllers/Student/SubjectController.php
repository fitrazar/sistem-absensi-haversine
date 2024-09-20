<?php

namespace App\Http\Controllers\Student;

use App\Models\Schedule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $userGradeId = Auth::user()->student->grade->id;
        $userMajorId = Auth::user()->student->major->id;
        $userGroupId = Auth::user()->student->group->id;

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

        $schedules = Schedule::where(function ($query) use ($userGradeId, $userMajorId, $userGroupId) {
            $query->whereHas('major', function ($query) use ($userMajorId) {
                $query->where('id', $userMajorId);
            })->whereHas('grade', function ($query) use ($userGradeId) {
                $query->where('id', $userGradeId);
            })->whereHas('group', function ($query) use ($userGroupId) {
                $query->where('id', $userGroupId);
            });
        })
            ->where(function ($query) use ($search) {
                if (!empty($search)) {
                    $query->whereHas('subject', function ($query2) use ($search) {
                        $query2->where('name', 'like', '%' . $search . '%');
                    });
                }
            })
            ->where('day', $days[$today])
            ->with(['grade', 'group', 'major'])
            ->orderBy('time_in', 'asc')
            ->paginate(7);


        return view('student.subject.index', compact('schedules', 'search'));
    }
}
