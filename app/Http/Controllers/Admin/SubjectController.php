<?php

namespace App\Http\Controllers\Admin;

use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use App\Exports\SubjectExport;
use App\Imports\SubjectImport;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class SubjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
            $subject = Subject::all();

            return DataTables::of($subject)
                ->make();
        }
        return view('dashboard.subject.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('dashboard.subject.create', [
            'teachers' => Teacher::orderBy('name')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'teacher_id' => 'required',
            'name' => 'required|max:100|string',
            'description' => 'required|string',
        ]);

        Subject::create([
            'teacher_id' => $validatedData['teacher_id'],
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
        ]);

        return redirect('/subject')->with('success', 'Mapel Berhasil Ditambahkan!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Subject $subject)
    {
        return view('dashboard.subject.edit', [
            'teachers' => Teacher::orderBy('name')->get(),
            'subject' => $subject
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Subject $subject)
    {
        $rules = [
            'teacher_id' => 'required',
            'name' => 'required|max:100|string',
            'description' => 'required|string',
        ];

        $validatedData = $request->validate($rules);

        Subject::findOrFail($subject->id)->update([
            'teacher_id' => $validatedData['teacher_id'],
            'name' => $validatedData['name'],
            'description' => $validatedData['description'],
        ]);

        return redirect('/subject')->with('success', 'Mapel Berhasil Diupdate');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Subject $subject)
    {
        Subject::destroy($subject->id);

        return redirect('/subject')->with('success', 'Mapel Berhasil Dihapus!');
    }

    public function import(Request $request)
    {
        $validator = $request->validate([
            'file' => 'file|mimes:csv,xls,xlsx'
        ]);
        $file = $request->file('file');
        $validator['file'] = $file->store('files');
        Excel::import(new SubjectImport, request()->file('file'));

        return redirect()->back()->with('success', 'Data Berhasil di Import');
    }

    public function export(Request $request)
    {
        return Excel::download(new SubjectExport(), 'Data Mapel.xlsx');
    }
}
