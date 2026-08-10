<?php

namespace App\Services\IssueRouting;

use App\Models\SupportTeamCalendar;
use App\Models\WorkingSchedule;
use App\Models\CalendarHoliday;
use Carbon\Carbon;

class BusinessHoursService
{
    public function getCalendarIdForTeam(
        int $supportTeamId
    ): ?int {

        return SupportTeamCalendar::query()
            ->where('support_team_id', $supportTeamId)
            ->where('is_active', true)
            ->where(function ($query) {

                $query->whereNull('effective_from')
                    ->orWhere(
                        'effective_from',
                        '<=',
                        now()->toDateString()
                    );

            })
            ->where(function ($query) {

                $query->whereNull('effective_to')
                    ->orWhere(
                        'effective_to',
                        '>=',
                        now()->toDateString()
                    );

            })
            ->value('calendar_id');
    }


    public function isBusinessHour(
        int $calendarId,
        ?Carbon $dateTime = null
    ): bool {

        $dateTime ??= now();


        /*
         * Holiday
         */
        $holiday = CalendarHoliday::query()
            ->where('calendar_id', $calendarId)
            ->whereDate(
                'holiday_date',
                $dateTime->toDateString()
            )
            ->where('is_active', true)
            ->exists();


        if ($holiday) {
            return false;
        }


        /*
         * Day of week
         */
        $dayOfWeek = $dateTime->dayOfWeekIso;


        /*
         * Working Schedule
         */
        $schedule = WorkingSchedule::query()
            ->where('calendar_id', $calendarId)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
            ->first();


        if (!$schedule) {
            return false;
        }


        /*
         * Working hours
         */
        $start = Carbon::parse(
            $dateTime->toDateString()
            . ' '
            . $schedule->start_time
        );


        $end = Carbon::parse(
            $dateTime->toDateString()
            . ' '
            . $schedule->end_time
        );


        return $dateTime->betweenIncluded(
            $start,
            $end
        );
    }
}