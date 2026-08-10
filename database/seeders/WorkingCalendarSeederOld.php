<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkingCalendarSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('mst_working_calendar')->truncate();

        DB::table('mst_working_calendar')->insert([

            [

                'calendar_id'=>1,
                'calendar_code'=>'CAL-HO',
                'calendar_name'=>'Head Office Calendar',
                'organisation_id'=>1,
                'timezone'=>'Asia/Kolkata',
                'is_active'=>1

            ],

            [

                'calendar_id'=>2,
                'calendar_code'=>'CAL-NOC',
                'calendar_name'=>'24x7 NOC Calendar',
                'organisation_id'=>1,
                'timezone'=>'Asia/Kolkata',
                'is_active'=>1

            ],

            [

                'calendar_id'=>3,
                'calendar_code'=>'CAL-VENDOR',
                'calendar_name'=>'Vendor Calendar',
                'organisation_id'=>2,
                'timezone'=>'Asia/Kolkata',
                'is_active'=>1

            ],

            [

                'calendar_id'=>4,
                'calendar_code'=>'CAL-EMS',
                'calendar_name'=>'Emergency Support',
                'organisation_id'=>1,
                'timezone'=>'Asia/Kolkata',
                'is_active'=>1

            ]

        ]);
    }
}