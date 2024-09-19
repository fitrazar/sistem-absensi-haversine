<?php

namespace App\Imports;

use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SubjectImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $teacher = Teacher::where("name", "like", "%" . $row[2] . "%")->first();

        return new Subject([
            'name' => $row[1],
            'description' => $row[1] ?? '-',
            'teacher_id' => $teacher->id,
        ]);
    }

    public function headingRow(): int
    {
        return 2;
    }
}
