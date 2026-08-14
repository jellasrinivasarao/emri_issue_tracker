<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkingScheduleSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $days = [
            ['MONDAY',    true],
            ['TUESDAY',   true],
            ['WEDNESDAY', true],
            ['THURSDAY',  true],
            ['FRIDAY',    true],
            ['SATURDAY',  false],
            ['SUNDAY',    false],
        ];

        $rows = [];

        foreach ($days as [$day, $isWorkingDay]) {
            $rows[] = [
                'calendar_id'    => 1,
                'day_of_week'    => $day,
                'schedule_name'  => $isWorkingDay
                    ? 'General Shift'
                    : 'Weekly Off',

                'start_time'     => $isWorkingDay
                    ? '09:00:00'
                    : null,

                'end_time'       => $isWorkingDay
                    ? '18:00:00'
                    : null,

                'is_working_day' => $isWorkingDay ? 1 : 0,
                'is_24_hours'    => 0,
                'sequence_no'    => 1,

                'effective_from' => '2026-01-01',
                'effective_to'   => null,

                'is_active'      => 1,

                'created_by'     => 1,
                'created_at'     => $now,

                'updated_by'     => null,
                'updated_at'    => null,

                'deleted_by'     => null,
                'deleted_at'    => null,

                'break_start'    => $isWorkingDay
                    ? '13:00:00'
                    : null,

                'break_end'      => $isWorkingDay
                    ? '14:00:00'
                    : null,

                'shift_no'       => 1,
                'shift_name'     => 'General',
            ];
        }

        DB::table('mst_working_schedule')->insert($rows);
    }
}