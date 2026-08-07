<?php

namespace App\Services;

use Carbon\Carbon;
use RuntimeException;

class BusinessTimeCalculator
{
    protected ?WorkingCalendarService $calendar = null;

    public function setCalendar(WorkingCalendarService $calendar): void
    {
        $this->calendar = $calendar;
    }

    /**
     * Add business minutes to a datetime.
     */
    public function addMinutes(
        Carbon $start,
        int $minutes
    ): Carbon {

        if (!$this->calendar) {
            throw new RuntimeException(
                'WorkingCalendarService has not been set.'
            );
        }

        if ($minutes <= 0) {
            return $start->copy();
        }

        $current = $start->copy();

        /*
        |--------------------------------------------------------------------------
        | Safety protection
        |--------------------------------------------------------------------------
        |
        | Never allow SLA calculation to run forever.
        |
        */

        $maxDays = 366;

        $daysChecked = 0;

        while ($minutes > 0) {

            if ($daysChecked > $maxDays) {

                throw new RuntimeException(
                    'Unable to calculate business time. '
                    . 'No working period found within 366 days.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Move current datetime into working time
            |--------------------------------------------------------------------------
            */

            $workingTime = $this->calendar->moveToWorkingTime($current->copy());

            /*
            |--------------------------------------------------------------------------
            | Safety check
            |--------------------------------------------------------------------------
            |
            | moveToWorkingTime() must actually move forward.
            |
            */

            if (
                $workingTime->lessThan($current)
            ) {

                throw new RuntimeException(
                    'WorkingCalendarService::moveToWorkingTime() '
                    . 'returned a datetime earlier than the current datetime.'
                );
            }

            $current = $workingTime;

            /*
            |--------------------------------------------------------------------------
            | Get current shift
            |--------------------------------------------------------------------------
            */

            $shift = $this->calendar->getShift(
                $current
            );

            /*
            |--------------------------------------------------------------------------
            | No shift for this day
            |--------------------------------------------------------------------------
            */

            if (!$shift) {

                $nextDay = $current
                    ->copy()
                    ->startOfDay()
                    ->addDay();

                /*
                |--------------------------------------------------------------------------
                | Safety check against infinite loop
                |--------------------------------------------------------------------------
                */

                if (
                    $nextDay->lessThanOrEqual($current)
                ) {

                    throw new RuntimeException(
                        'Unable to move to the next working day.'
                    );
                }

                $current = $nextDay;

                $daysChecked++;

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Shift start
            |--------------------------------------------------------------------------
            */

            $shiftStart = Carbon::parse(
                $current->toDateString()
                . ' '
                . $shift->start_time,
                $current->timezone
            );

            /*
            |--------------------------------------------------------------------------
            | Shift end
            |--------------------------------------------------------------------------
            */

            $shiftEnd = Carbon::parse(
                $current->toDateString()
                . ' '
                . $shift->end_time,
                $current->timezone
            );

            /*
            |--------------------------------------------------------------------------
            | Overnight shift
            |--------------------------------------------------------------------------
            */

            if (
                $shiftEnd->lessThanOrEqual($shiftStart)
            ) {

                $shiftEnd->addDay();
            }

            /*
            |--------------------------------------------------------------------------
            | Make sure current is inside shift
            |--------------------------------------------------------------------------
            */

            if ($current->lessThan($shiftStart)) {

                $current = $shiftStart->copy();
            }

            /*
            |--------------------------------------------------------------------------
            | Current time is after shift
            |--------------------------------------------------------------------------
            */

            if (
                $current->greaterThanOrEqual($shiftEnd)
            ) {

                $nextDay = $current
                    ->copy()
                    ->startOfDay()
                    ->addDay();

                $current = $nextDay;

                $daysChecked++;

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Lunch Break
            |--------------------------------------------------------------------------
            */

            $break = $this->calendar->getLunchBreak(
                $current
            );

            /*
            |--------------------------------------------------------------------------
            | Calculate available minutes
            |--------------------------------------------------------------------------
            */

            $periodEnd = $shiftEnd;

            /*
            |--------------------------------------------------------------------------
            | Break exists and is ahead
            |--------------------------------------------------------------------------
            */

            if (
                $break
                && $current->lt($break['start'])
                && $break['start']->lt($shiftEnd)
            ) {

                $periodEnd = $break['start'];
            }

            /*
            |--------------------------------------------------------------------------
            | Available working minutes
            |--------------------------------------------------------------------------
            */

            $available = $current->diffInMinutes(
                $periodEnd
            );

            /*
            |--------------------------------------------------------------------------
            | Prevent zero-minute infinite loop
            |--------------------------------------------------------------------------
            */

            if ($available <= 0) {

                /*
                | If we're exactly at the break start,
                | jump to break end.
                */

                if (
                    $break
                    && $current->equalTo($break['start'])
                ) {

                    $current = $break['end'];

                    continue;
                }

                /*
                | Otherwise move to next working day.
                */

                $current = $current
                    ->copy()
                    ->startOfDay()
                    ->addDay();

                $daysChecked++;

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | SLA fits in current working period
            |--------------------------------------------------------------------------
            */

            if ($minutes <= $available) {

                return $current
                    ->copy()
                    ->addMinutes($minutes);
            }

            /*
            |--------------------------------------------------------------------------
            | Consume current working period
            |--------------------------------------------------------------------------
            */

            $minutes -= $available;

            /*
            |--------------------------------------------------------------------------
            | Move to lunch break
            |--------------------------------------------------------------------------
            */

            if (
                $break
                && $periodEnd->equalTo($break['start'])
            ) {

                $current = $break['end'];

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Current shift consumed
            |--------------------------------------------------------------------------
            */

            $current = $shiftEnd->copy();

            /*
            |--------------------------------------------------------------------------
            | Move to next day
            |--------------------------------------------------------------------------
            */

            $current = $current
                ->startOfDay()
                ->addDay();

            $daysChecked++;
        }

        return $current;
    }
}