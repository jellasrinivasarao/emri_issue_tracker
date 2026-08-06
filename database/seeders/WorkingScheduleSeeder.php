<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkingScheduleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('mst_working_schedule')->truncate();

        $rows = [];

        /*
        |--------------------------------------------------------------------------
        | Calendar 1 : Head Office
        |--------------------------------------------------------------------------
        */

        foreach ([
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday'
        ] as $day) {

            $rows[] = [

                'calendar_id' => 1,

                'day_of_week' => $day,

                'shift_no' => 1,

                'shift_name' => 'General Shift',

                'start_time' => '09:00:00',

                'end_time' => '18:00:00',

                'break_start' => '13:00:00',

                'break_end' => '14:00:00',

                'is_working_day' => 1,

            ];

        }

        $rows[] = [

            'calendar_id' => 1,

            'day_of_week' => 'Saturday',

            'shift_no' => 1,

            'shift_name' => 'Half Day',

            'start_time' => '09:00:00',

            'end_time' => '13:00:00',

            'break_start' => null,

            'break_end' => null,

            'is_working_day' => 1,

        ];

        /*
        |--------------------------------------------------------------------------
        | Calendar 2 : 24x7 NOC
        |--------------------------------------------------------------------------
        */

        foreach ([
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday',
            'Saturday',
            'Sunday'
        ] as $day) {

            $rows[] = [

                'calendar_id' => 2,

                'day_of_week' => $day,

                'shift_no' => 1,

                'shift_name' => 'Morning',

                'start_time' => '06:00:00',

                'end_time' => '14:00:00',

                'break_start' => null,

                'break_end' => null,

                'is_working_day' => 1,

            ];

            $rows[] = [

                'calendar_id' => 2,

                'day_of_week' => $day,

                'shift_no' => 2,

                'shift_name' => 'Evening',

                'start_time' => '14:00:00',

                'end_time' => '22:00:00',

                'break_start' => null,

                'break_end' => null,

                'is_working_day' => 1,

            ];

            $rows[] = [

                'calendar_id' => 2,

                'day_of_week' => $day,

                'shift_no' => 3,

                'shift_name' => 'Night',

                'start_time' => '22:00:00',

                'end_time' => '06:00:00',

                'break_start' => null,

                'break_end' => null,

                'is_working_day' => 1,

            ];

        }

        /*
        |--------------------------------------------------------------------------
        | Calendar 3 : Vendor Support
        |--------------------------------------------------------------------------
        */

        foreach ([
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday'
        ] as $day) {

            $rows[] = [

                'calendar_id' => 3,

                'day_of_week' => $day,

                'shift_no' => 1,

                'shift_name' => 'Vendor Shift',

                'start_time' => '10:00:00',

                'end_time' => '19:00:00',

                'break_start' => '14:00:00',

                'break_end' => '15:00:00',

                'is_working_day' => 1,

            ];

        }

        /*
        |--------------------------------------------------------------------------
        | Calendar 4 : Emergency Support
        |--------------------------------------------------------------------------
        */

        foreach ([
            'Monday',
            'Tuesday',
            'Wednesday',
            'Thursday',
            'Friday'
        ] as $day) {

            $rows[] = [

                'calendar_id' => 4,

                'day_of_week' => $day,

                'shift_no' => 1,

                'shift_name' => 'Emergency Shift',

                'start_time' => '08:00:00',

                'end_time' => '20:00:00',

                'break_start' => '13:00:00',

                'break_end' => '14:00:00',

                'is_working_day' => 1,

            ];

        }

        $rows[] = [

            'calendar_id' => 4,

            'day_of_week' => 'Saturday',

            'shift_no' => 1,

            'shift_name' => 'Emergency Half Day',

            'start_time' => '08:00:00',

            'end_time' => '16:00:00',

            'break_start' => null,

            'break_end' => null,

            'is_working_day' => 1,

        ];

        DB::table('mst_working_schedule')->insert($rows);
    }
}