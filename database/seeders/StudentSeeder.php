<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('students')->insert([
            'user_id' => 3,
            'grade_id' => 1,
            'major_id' => 1,
            'group_id' => 1,
            'name' => 'Muhammad Fitra Fajar Rusamsi',
            'nisn' => 1000,
            'gender' => 'Laki - Laki',
            'phone' => '6281385931773',
            'point' => 100,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
