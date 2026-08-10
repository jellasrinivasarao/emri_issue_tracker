<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WorkingScheduleSeederLatest extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $schedules = [
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
        ];

        foreach ($schedules as $day) {
            DB::table('mst_working_schedule')->insert([
                'calendar_id' => 1,
                'day_of_week' => $day,
                'shift_no' => 1,
                'start_time' => '09:00:00',
                'end_time' => '18:00:00',
                'break_start' => '13:00:00',
                'break_end' => '14:00:00',
                'is_active' => 1,
                'created_at' => $now,
            ]);
        }
    }
}