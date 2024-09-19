<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TeacherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('teachers')->insert([
            'user_id' => 2,
            'name' => 'Firman Syahrani',
            'nip' => 1000,
            'gender' => 'Laki - Laki',
            'phone' => '6281385931773',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
