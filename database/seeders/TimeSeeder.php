<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TimeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('times')->insert([
            'time_in_early' => Carbon::createFromTime(6, 45, 0),
            'time_in_lately' => Carbon::createFromTime(7, 15, 0),
            'time_out_early' => Carbon::createFromTime(15, 15, 0),
            'time_out_lately' => Carbon::createFromTime(16, 45, 0),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
