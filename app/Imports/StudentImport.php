<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Grade;
use App\Models\Major;
use App\Models\Student;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $parts = explode(' ', $row[4]);

        $grade = Grade::where('name', $parts[0])->first();
        $major = Major::where('acronym', $parts[1])->first();

        $user = new User([
            'username' => $row[3],
            'password' => bcrypt($row[3]),
        ]);
        $user->assignRole('student');
        $user->save();

        return new Student([
            'name' => Str::title($row[1]),
            'gender' => $row[2] == 'L' ? 'Laki - Laki' : 'Perempuan',
            'nisn' => $row[3],
            'phone' => $row[5] ?? NULL,
            'grade_id' => $grade->id,
            'major_id' => $major->id,
            'group_id' => $parts[2],
            'user_id' => $user->id,
            'point' => 100,
        ]);
    }

    public function headingRow(): int
    {
        return 2;
    }
}
