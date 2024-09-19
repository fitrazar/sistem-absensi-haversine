<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Exports\TeacherExport;
use App\Imports\TeacherImport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $teacher = Teacher::all();

            return DataTables::of($teacher)
                ->make();
        }
        return view('dashboard.teacher.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.teacher.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nip' => 'required|numeric|unique:teachers,nip',
            'name' => 'required|string',
            'gender' => 'required|in:Laki - Laki,Perempuan',
            'phone' => 'nullable|numeric',
            'photo' => 'nullable|image|max:4098',
        ]);

        $fileFilename = NULL;
        if ($request->hasFile('photo')) {
            $fileFilename = time() . '.' . $request->file('photo')->getClientOriginalExtension();
            $photoPath = $request->file('photo')->storeAs('teacher/photo', $fileFilename);
        }

        $user = new User([
            'username' => $request->nip,
            'password' => bcrypt($request->nip),
        ]);
        $user->assignRole('teacher');
        $user->save();

        Teacher::create([
            'user_id' => $user->id,
            'nip' => $validatedData['nip'],
            'name' => $validatedData['name'],
            'gender' => $validatedData['gender'],
            'phone' => $validatedData['phone'],
            'photo' => $fileFilename ?? NULL,
        ]);

        return redirect('/teacher')->with('success', 'Guru Berhasil Ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Teacher $teacher)
    {
        return view('dashboard.teacher.edit', compact('teacher'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Teacher $teacher)
    {
        $rules = [
            'name' => 'required|string',
            'gender' => 'required|in:Laki - Laki,Perempuan',
            'phone' => 'nullable|numeric',
            'photo' => 'sometimes|image|max:4098',
        ];

        $validatedData = $request->validate($rules);
        $validatedData['photo'] = $request->oldImage;
        if ($request->file('photo')) {
            $path = 'teacher/photo';
            if ($request->oldImage) {
                Storage::delete($path . '/' . $request->oldImage);
            }
            $validatedData['photo'] = time() . '.' . $request->file('photo')->getClientOriginalExtension();
            $photoPath = $request->file('photo')->storeAs('teacher/photo', $validatedData['photo']);
        }

        Teacher::findOrFail($teacher->id)->update([
            'name' => $validatedData['name'],
            'gender' => $validatedData['gender'],
            'phone' => $validatedData['phone'],
            'photo' => $validatedData['photo']
        ]);

        return redirect('/teacher')->with('success', 'Guru Berhasil Diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Teacher $teacher)
    {
        if ($teacher->photo) {
            Storage::delete('teacher/photo/' . $teacher->photo);
        }
        $teacher->user->delete();
        Teacher::destroy($teacher->id);

        return redirect('/teacher')->with('success', 'Guru Berhasil Dihapus!');
    }

    public function import(Request $request)
    {
        $validator = $request->validate([
            'file' => 'file|mimes:csv,xls,xlsx'
        ]);
        $file = $request->file('file');
        $validator['file'] = $file->store('files');
        Excel::import(new TeacherImport, request()->file('file'));

        return redirect()->back()->with('success', 'Data Berhasil di Import');
    }

    public function export(Request $request)
    {
        return Excel::download(new TeacherExport(), 'Data Guru.xlsx');
    }
}
