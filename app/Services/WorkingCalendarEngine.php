<?php

namespace App\Services;

use App\Models\WorkingCalendar;
use App\Models\WorkingSchedule;
use App\Models\CalendarHoliday;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class WorkingCalendarEngine
{
    public function check(WorkingCalendar $calendar,?CarbonInterface $dateTime = null): array {

        $timezone = $calendar->timezone
            ?: config('app.timezone');

        $now = $dateTime
            ? Carbon::parse($dateTime)->setTimezone($timezone)
            : Carbon::now($timezone);

        /*
        |--------------------------------------------------------------------------
        | 1. Calendar active check
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
        | 2. Effective date
        |--------------------------------------------------------------------------
        */

        if (
            $calendar->effective_from &&
            $now->toDateString() <
            $calendar->effective_from->toDateString()
        ) {
            return $this->result(
                false,
                'CALENDAR_NOT_EFFECTIVE',
                $now
            );
        }

        if (
            $calendar->effective_to &&
            $now->toDateString() >
            $calendar->effective_to->toDateString()
        ) {
            return $this->result(
                false,
                'CALENDAR_EXPIRED',
                $now
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 3. Holiday check
        |--------------------------------------------------------------------------
        */

        $holiday = CalendarHoliday::query()
            ->where('calendar_id', $calendar->calendar_id)
            ->whereDate('holiday_date', $now->toDateString())
            ->where('is_active', true)
            ->first();

        if ($holiday && !$holiday->is_working_day_override) {

            return $this->result(
                false,
                'HOLIDAY',
                $now,
                $holiday
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 4. Day of week
        |--------------------------------------------------------------------------
        */

        $dayOfWeek = $now->dayOfWeekIso;

        /*
        |--------------------------------------------------------------------------
        | 5. Load today's schedules
        |--------------------------------------------------------------------------
        */

        $schedules = WorkingSchedule::query()
            ->where('calendar_id', $calendar->calendar_id)
            ->where('day_of_week', $dayOfWeek)
            ->where('is_active', true)
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
        | 6. No schedule
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
        | 7. Check schedules
        |--------------------------------------------------------------------------
        */

        foreach ($schedules as $schedule) {

            if (!$schedule->is_working_day) {
                continue;
            }

            if ($schedule->is_24_hours) {

                return $this->result(
                    true,
                    'WORKING_24_HOURS',
                    $now,
                    null,
                    $schedule
                );
            }

            if (
                !$schedule->start_time ||
                !$schedule->end_time
            ) {
                continue;
            }

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
            | Overnight shift
            |--------------------------------------------------------------------------
            */

            if ($end->lessThanOrEqualTo($start)) {
                $end->addDay();
            }

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
        | 8. Outside working hours
        |--------------------------------------------------------------------------
        */

        return $this->result(
            false,
            'OUTSIDE_WORKING_HOURS',
            $now
        );
    }

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

            'current_datetime' => $now
                ->format('Y-m-d H:i:s'),

            'timezone' => $now->timezoneName,

            'holiday_id' => $holiday?->holiday_id,

            'holiday_name' => $holiday?->holiday_name,

            'schedule_id' => $schedule?->schedule_id,

            'schedule_name' => $schedule?->schedule_name,

            'start_time' => $schedule?->start_time,

            'end_time' => $schedule?->end_time,
        ];
    }
}