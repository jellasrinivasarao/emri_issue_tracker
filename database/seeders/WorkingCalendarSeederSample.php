<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorkingCalendar;
use App\Models\WorkingSchedule;

class WorkingCalendarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // For each calendar that has no schedules, create sensible defaults
        $calendars = WorkingCalendar::query()->doesntHave('schedules')->get();

        foreach ($calendars as $calendar) {
            // Monday (1) .. Friday (5): 09:00 - 18:00 working
            for ($d = 1; $d <= 5; $d++) {
                WorkingSchedule::create([
                    'calendar_id' => $calendar->calendar_id,
                    'day_of_week' => $d,
                    'start_time' => '09:00:00',
                    'end_time' => '18:00:00',
                    'is_working_day' => true,
                    'is_active' => true,
                ]);
            }

            // Saturday (6): working by default (2nd and 4th Saturdays handled by engine)
            WorkingSchedule::create([
                'calendar_id' => $calendar->calendar_id,
                'day_of_week' => 6,
                'start_time' => '09:00:00',
                'end_time' => '18:00:00',
                'is_working_day' => true,
                'is_active' => true,
            ]);

            // Sunday (7): non-working
            WorkingSchedule::create([
                'calendar_id' => $calendar->calendar_id,
                'day_of_week' => 7,
                'start_time' => null,
                'end_time' => null,
                'is_working_day' => false,
                'is_active' => true,
            ]);
        }
    }
}