<?php

namespace Database\Seeders;

use App\Models\Major;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MajorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'acronym' => 'TKJ',
                'name' => 'Teknik Komputer Jaringan',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'acronym' => 'DPIB',
                'name' => 'Desain Pemodelan dan Informasi Bangunan',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'acronym' => 'TBSM',
                'name' => 'Teknik dan Bisnis Sepeda Motor',
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        Major::insert($data);
    }
}
