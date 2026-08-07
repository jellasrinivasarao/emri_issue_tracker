<?php

namespace App\Services;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\WorkingCalendar;
use App\Models\WorkingSchedule;
use App\Models\CalendarHoliday;

class BusinessTimeCalculator
{

    protected ?WorkingCalendarService $calendar = null;
    
    
    #protected WorkingCalendar $calendar;

    protected $schedules;

    protected $holidays;

    #WorkingCalendar $calendar
    #WorkingCalendarService $calendar
    
    // public function __construct(WorkingCalendarService $calendar)
    // {
    //     $this->calendar = $calendar;

    //     /*
    //     $this->schedules = WorkingSchedule::where(
    //             'calendar_id',
    //             $calendar->calendar_id
    //         )
    //         ->get()
    //         ->keyBy('day_of_week');

    //     $this->holidays = CalendarHoliday::where(
    //             'calendar_id',
    //             $calendar->calendar_id
    //         )
    //         ->pluck('holiday_date')
    //         ->map(fn($d)=>Carbon::parse($d)->toDateString())
    //         ->toArray();

    //         */

    //     $current = $this->calendar->moveToWorkingTime($current);

    //     $end = $this->calendar->officeEnd($current);

    //     $current = $this->calendar->nextWorkingDay($current);

    //     $this->calendar->isHoliday($current);

    // }

    public function setCalendar(WorkingCalendarService $calendar): void {

        $this->calendar = $calendar;

    }


    public function addBusinessMinutes(
    Carbon $start,
    int $minutes
): Carbon
{
    $current = $start->copy();

    while ($minutes > 0) {

        $current = $this->moveToWorkingTime($current);

        $schedule = $this->getSchedule($current);

        $end = Carbon::parse(
            $current->toDateString() .
            ' ' .
            $schedule->end_time
        );

        $available = $current->diffInMinutes($end);

        if ($available >= $minutes) {

            return $current->addMinutes($minutes);

        }

        $minutes -= $available;

        $current = $this->nextWorkingDay($current);

    }

    return $current;
}


        protected function getSchedule(Carbon $date)
        {
            return $this->schedules[
                strtolower(
                    $date->format('l')
                )
            ];
        }


        protected function moveToWorkingTime(
    Carbon $date
): Carbon
{

    while (true) {

        if ($this->isHoliday($date)) {

            $date = $this->nextWorkingDay($date);

            continue;

        }

        $schedule = $this->getSchedule($date);

        if (!$schedule) {

            $date = $this->nextWorkingDay($date);

            continue;

        }

        $officeStart = Carbon::parse(
            $date->toDateString() .
            ' ' .
            $schedule->start_time
        );

        $officeEnd = Carbon::parse(
            $date->toDateString() .
            ' ' .
            $schedule->end_time
        );

        if ($date->lt($officeStart)) {

            return $officeStart;

        }

        if ($date->gte($officeEnd)) {

            $date = $this->nextWorkingDay($date);

            continue;

        }

        return $date;

    }

}

protected function isHoliday(
    Carbon $date
): bool
{
    return in_array(
        $date->toDateString(),
        $this->holidays
    );
}

protected function nextWorkingDay(
    Carbon $date
): Carbon
{

    do {

        $date = $date
            ->copy()
            ->addDay()
            ->startOfDay();

    } while (

        $this->isHoliday($date)
        ||

        !$this->getSchedule($date)

    );

    $schedule = $this->getSchedule($date);

    return Carbon::parse($date->toDateString().' '.$schedule->start_time);

}


#####
public function addMinutes(Carbon $start,int $minutes): Carbon {

        if ($minutes <= 0) {

            return $start->copy();

        }

        $current =
            $this->calendar
                ->moveToWorkingTime(
                    $start->copy()
                );

        while ($minutes > 0) {

            /*
            |--------------------------------------------------------------------------
            | Current shift
            |--------------------------------------------------------------------------
            */

            $shift =
                $this->calendar
                    ->getShift($current);

            if (!$shift) {

                $current =
                    $this->calendar
                        ->nextWorkingDay(
                            $current
                        );

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Shift End
            |--------------------------------------------------------------------------
            */

            $shiftEnd =
                Carbon::parse(
                    $current->toDateString()
                    . ' '
                    . $shift->end_time
                );

            /*
            |--------------------------------------------------------------------------
            | Overnight shift
            |--------------------------------------------------------------------------
            */

            if (
                $shift->end_time
                <
                $shift->start_time
            ) {

                $shiftEnd->addDay();

            }

            /*
            |--------------------------------------------------------------------------
            | Lunch Break
            |--------------------------------------------------------------------------
            */

            $break =
                $this->calendar
                    ->getLunchBreak($current);

            /*
            |--------------------------------------------------------------------------
            | Available minutes
            |--------------------------------------------------------------------------
            */

            $available =
                $current->diffInMinutes(
                    $shiftEnd
                );

            /*
            |--------------------------------------------------------------------------
            | If lunch break is ahead
            |--------------------------------------------------------------------------
            */

            if (
                $break
                &&
                $current->lt($break['start'])
            ) {

                $availableBeforeBreak =
                    $current->diffInMinutes(
                        $break['start']
                    );

                if (
                    $minutes
                    <=
                    $availableBeforeBreak
                ) {

                    return $current->addMinutes(
                        $minutes
                    );

                }

                $minutes -=
                    $availableBeforeBreak;

                $current =
                    $break['end'];

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | SLA fits inside current shift
            |--------------------------------------------------------------------------
            */

            if ($minutes <= $available) {

                return $current->addMinutes(
                    $minutes
                );

            }

            /*
            |--------------------------------------------------------------------------
            | Consume current shift
            |--------------------------------------------------------------------------
            */

            $minutes -= $available;

            /*
            |--------------------------------------------------------------------------
            | Move to next working period
            |--------------------------------------------------------------------------
            */

            $current =
                $this->calendar
                    ->nextWorkingDay(
                        $current
                    );

        }

        return $current;
    }



}