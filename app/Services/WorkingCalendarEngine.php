<?php

namespace App\Services;

use App\Models\WorkingCalendar;
use App\Models\WorkingSchedule;
use App\Models\CalendarHoliday;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class WorkingCalendarEngine
{
    /**
     * Determine whether the calendar is currently working.
     *
     * Result:
     *
     * is_working = true
     *      => HO IT can receive the issue
     *
     * is_working = false
     *      => Vendor fallback
     */
    public function check(
        WorkingCalendar $calendar,
        ?CarbonInterface $dateTime = null
    ): array {

        $timezone = $calendar->timezone ?: config('app.timezone');

        $now = $dateTime
            ? Carbon::parse($dateTime)->setTimezone($timezone)
            : Carbon::now($timezone);

        /*
        |--------------------------------------------------------------------------
        | 1. Calendar Active
        |--------------------------------------------------------------------------
        */

        if (!$calendar->is_active) {
            return $this->result(
                false,
                'CALENDAR_INACTIVE',
                $now
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 2. Calendar Effective From
        |--------------------------------------------------------------------------
        */

        if (
            $calendar->effective_from &&
            $now->toDateString() <
            Carbon::parse($calendar->effective_from)->toDateString()
        ) {
            return $this->result(
                false,
                'CALENDAR_NOT_EFFECTIVE',
                $now
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 3. Calendar Effective To
        |--------------------------------------------------------------------------
        */

        if (
            $calendar->effective_to &&
            $now->toDateString() >
            Carbon::parse($calendar->effective_to)->toDateString()
        ) {
            return $this->result(
                false,
                'CALENDAR_EXPIRED',
                $now
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 4. Holiday
        |--------------------------------------------------------------------------
        */

        $holiday = CalendarHoliday::query()
            ->where('calendar_id', $calendar->calendar_id)
            ->whereDate(
                'holiday_date',
                $now->toDateString()
            )
            ->where('is_active', true)
            ->first();

        /*
         * Holiday without working-day override.
         */
        if (
            $holiday &&
            !$holiday->is_working_day_override
        ) {

            return $this->result(
                false,
                'HOLIDAY',
                $now,
                $holiday
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 5. Day Of Week
        |--------------------------------------------------------------------------
        */

        $dayOfWeek = $now->dayOfWeekIso;

        /*
        |--------------------------------------------------------------------------
        | 6. 2nd / 4th Saturday
        |--------------------------------------------------------------------------
        |
        | Saturday = 6 in ISO.
        |
        */

        if ($dayOfWeek === 6) {

            $weekOfMonth = (int) ceil($now->day / 7);

            if (
                in_array(
                    $weekOfMonth,
                    [2, 4],
                    true
                )
            ) {

                return $this->result(
                    false,
                    'WEEKLY_SPECIAL_OFF',
                    $now
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 7. Get Today's Schedule
        |--------------------------------------------------------------------------
        */

        $schedules = WorkingSchedule::query()
            ->where(
                'calendar_id',
                $calendar->calendar_id
            )
            ->where(
                'day_of_week',
                $dayOfWeek
            )
            ->where(
                'is_active',
                true
            )

            ->where(function ($query) use ($now) {

                $query
                    ->whereNull('effective_from')
                    ->orWhereDate(
                        'effective_from',
                        '<=',
                        $now->toDateString()
                    );

            })

            ->where(function ($query) use ($now) {

                $query
                    ->whereNull('effective_to')
                    ->orWhereDate(
                        'effective_to',
                        '>=',
                        $now->toDateString()
                    );

            })

            ->orderBy('sequence_no')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | 8. No Schedule
        |--------------------------------------------------------------------------
        */

        if ($schedules->isEmpty()) {

            return $this->result(
                false,
                'NO_SCHEDULE',
                $now
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 9. Check Schedule
        |--------------------------------------------------------------------------
        */

        foreach ($schedules as $schedule) {

            /*
             * Non-working schedule.
             */
            if (!$schedule->is_working_day) {
                continue;
            }

            /*
             * 24 Hours.
             */
            if ($schedule->is_24_hours) {

                return $this->result(
                    true,
                    'WORKING_24_HOURS',
                    $now,
                    null,
                    $schedule
                );
            }

            /*
             * Invalid schedule.
             */
            if (
                !$schedule->start_time ||
                !$schedule->end_time
            ) {
                continue;
            }

            /*
             |--------------------------------------------------------------------------
             | Create Start / End
             |--------------------------------------------------------------------------
             */

            $start = Carbon::createFromFormat(
                'Y-m-d H:i:s',
                $now->format('Y-m-d') .
                ' ' .
                $schedule->start_time,
                $timezone
            );

            $end = Carbon::createFromFormat(
                'Y-m-d H:i:s',
                $now->format('Y-m-d') .
                ' ' .
                $schedule->end_time,
                $timezone
            );

            /*
             |--------------------------------------------------------------------------
             | Overnight Schedule
             |--------------------------------------------------------------------------
             */

            if ($end->lessThanOrEqualTo($start)) {
                $end->addDay();
            }

            /*
             |--------------------------------------------------------------------------
             | Current Time Within Business Hours
             |--------------------------------------------------------------------------
             */

            if (
                $now->greaterThanOrEqualTo($start) &&
                $now->lessThan($end)
            ) {

                return $this->result(
                    true,
                    'WORKING_HOURS',
                    $now,
                    null,
                    $schedule
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | 10. Outside Business Hours
        |--------------------------------------------------------------------------
        */

        return $this->result(
            false,
            'OUTSIDE_WORKING_HOURS',
            $now
        );
    }

    /**
     * Standard result.
     */
    private function result(
        bool $working,
        string $status,
        Carbon $now,
        ?CalendarHoliday $holiday = null,
        ?WorkingSchedule $schedule = null
    ): array {

        return [

            'is_working' => $working,

            'status' => $status,

            'current_datetime' =>
                $now->format('Y-m-d H:i:s'),

            'timezone' =>
                $now->timezoneName,

            'date' =>
                $now->toDateString(),

            'day_of_week' =>
                $now->dayOfWeekIso,

            'holiday_id' =>
                $holiday?->holiday_id,

            'holiday_name' =>
                $holiday?->holiday_name,

            'schedule_id' =>
                $schedule?->schedule_id,

            'schedule_name' =>
                $schedule?->schedule_name,

            'start_time' =>
                $schedule?->start_time,

            'end_time' =>
                $schedule?->end_time,
        ];
    }
}