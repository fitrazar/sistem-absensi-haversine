<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Teacher;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TeacherImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        $user = new User([
            'username' => $row[3],
            'password' => bcrypt($row[3]),
        ]);
        $user->assignRole('teacher');
        $user->save();

        return new Teacher([
            'name' => Str::title($row[1]),
            'gender' => $row[2] == 'L' ? 'Laki - Laki' : 'Perempuan',
            'nip' => $row[3],
            'phone' => $row[4] ?? NULL,
            'user_id' => $user->id,
        ]);
    }

    public function headingRow(): int
    {
        return 2;
    }
}
