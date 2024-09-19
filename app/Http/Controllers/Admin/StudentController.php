<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Grade;
use App\Models\Group;
use App\Models\Major;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Exports\StudentExport;
use App\Imports\StudentImport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $students = Student::query();

            if ($request->has('jurusan') && $request->input('jurusan') != 'All' && $request->input('jurusan') != NULL) {
                $major = $request->input('jurusan');
                $students->whereHas('major', function ($query) use ($major) {
                    $query->where('id', $major);
                });
            }
            if ($request->has('kelas') && $request->input('kelas') != 'All' && $request->input('kelas') != NULL) {
                $grade = $request->input('kelas');
                $students->whereHas('grade', function ($query) use ($grade) {
                    $query->where('name', $grade);
                });
            }


            return DataTables::of($students)->make();
        }

        return view('dashboard.student.index', [
            'majors' => Major::where('status', 1)->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $grades = Grade::where('status', 1)->pluck('name', 'id');
        $majors = Major::where('status', 1)->pluck('acronym', 'id');
        $groups = Group::where('status', 1)->pluck('number', 'id');

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
        return view('dashboard.student.create', compact('rombels'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nisn' => 'required|numeric|unique:students,nisn',
            'name' => 'required|string',
            'gender' => 'required|in:Laki - Laki,Perempuan',
            'phone' => 'required|numeric',
            'rombel' => 'required',
            'photo' => 'nullable|image|max:4098',
        ]);

        list($gradeId, $majorId, $groupId) = explode(' ', $validatedData['rombel']);
        $fileFilename = NULL;
        if ($request->hasFile('photo')) {
            $fileFilename = time() . '.' . $request->file('photo')->getClientOriginalExtension();
            $photoPath = $request->file('photo')->storeAs('student/photo', $fileFilename);
        }

        $user = new User([
            'username' => $request->nisn,
            'password' => bcrypt($request->nisn),
        ]);
        $user->assignRole('student');
        $user->save();

        Student::create([
            'grade_id' => $gradeId,
            'major_id' => $majorId,
            'group_id' => $groupId,
            'user_id' => $user->id,
            'nisn' => $validatedData['nisn'],
            'name' => $validatedData['name'],
            'gender' => $validatedData['gender'],
            'phone' => $validatedData['phone'],
            'photo' => $fileFilename ?? NULL,
            'point' => 100
        ]);

        return redirect('/student')->with('success', 'Siswa Berhasil Ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student)
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
        return view('dashboard.student.edit', compact('student', 'rombels'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        $rules = [
            'name' => 'required|string',
            'gender' => 'required|in:Laki - Laki,Perempuan',
            'phone' => 'required|numeric',
            'point' => 'required|numeric',
            'rombel' => 'required',
            'photo' => 'sometimes|image|max:4098',
        ];

        $validatedData = $request->validate($rules);
        $validatedData['photo'] = $request->oldImage;
        if ($request->file('photo')) {
            $path = 'student/photo';
            if ($request->oldImage) {
                Storage::delete($path . '/' . $request->oldImage);
            }
            $validatedData['photo'] = time() . '.' . $request->file('photo')->getClientOriginalExtension();
            $photoPath = $request->file('photo')->storeAs('student/photo', $validatedData['photo']);
        }

        list($gradeId, $majorId, $groupId) = explode(' ', $validatedData['rombel']);

        Student::findOrFail($student->id)->update([
            'grade_id' => $gradeId,
            'major_id' => $majorId,
            'group_id' => $groupId,
            'name' => $validatedData['name'],
            'gender' => $validatedData['gender'],
            'phone' => $validatedData['phone'],
            'photo' => $validatedData['photo'],
            'point' => $validatedData['point']
        ]);

        return redirect('/student')->with('success', 'Siswa Berhasil Diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        if ($student->photo) {
            Storage::delete('student/photo/' . $student->photo);
        }
        $student->user->delete();
        Student::destroy($student->id);

        return redirect('/student')->with('success', 'Siswa Berhasil Dihapus!');
    }

    public function import(Request $request): RedirectResponse
    {
        $validator = $request->validate([
            'file' => 'file|mimes:csv,xls,xlsx'
        ]);
        $file = $request->file('file');
        // upload ke folder file_siswa di dalam folder public
        $validator['file'] = $file->store('files');
        Excel::import(new StudentImport, request()->file('file'));

        return redirect()->back()->with('success', 'Data Berhasil di Import');
    }

    public function export(Request $request): BinaryFileResponse
    {
        $major = $request->jurusanExport;
        $grade = $request->kelasExport;
        return Excel::download(new StudentExport($major, $grade), 'Data Siswa.xlsx');
    }
}
