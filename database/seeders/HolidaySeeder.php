<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HolidaySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('mst_calendar_holiday')->truncate();

        DB::table('mst_calendar_holiday')->insert([

            [
                'calendar_id'=>1,
                'holiday_name'=>'Republic Day',
                'holiday_date'=>'2026-01-26'
            ],

            [
                'calendar_id'=>1,
                'holiday_name'=>'Independence Day',
                'holiday_date'=>'2026-08-15'
            ],

            [
                'calendar_id'=>1,
                'holiday_name'=>'Gandhi Jayanti',
                'holiday_date'=>'2026-10-02'
            ],

            [
                'calendar_id'=>1,
                'holiday_name'=>'Christmas',
                'holiday_date'=>'2026-12-25'
            ]

        ]);
    }
}