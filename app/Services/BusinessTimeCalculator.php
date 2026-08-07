<?php

namespace App\Services;

use Carbon\Carbon;

class BusinessTimeCalculator
{

    protected ?WorkingCalendarService $calendar = null;


    public function setCalendar(WorkingCalendarService $calendar): void {

        $this->calendar = $calendar;

    }

    public function addMinutes(Carbon $start,int $minutes): Carbon {

        if (!$this->calendar) {
            throw new \Exception('WorkingCalendarService has not been set.');
        }

        if ($minutes <= 0) {
            return $start->copy();
        }

        $current = $this->calendar->moveToWorkingTime($start->copy());
        

        while ($minutes > 0) {

            /*
            |--------------------------------------------------------------------------
            | Current shift
            |--------------------------------------------------------------------------
            */

            $shift =$this->calendar->getShift($current);

            if (!$shift) {

                $current = $this->calendar->nextWorkingDay($current);

                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Shift End
            |--------------------------------------------------------------------------
            */

            $shiftEnd = Carbon::parse($current->toDateString(). ' '. $shift->end_time);

            /*
            |--------------------------------------------------------------------------
            | Overnight shift
            |--------------------------------------------------------------------------
            */

            if ($shift->end_time<$shift->start_time) {

                $shiftEnd->addDay();

            }

            /*
            |--------------------------------------------------------------------------
            | Lunch Break
            |--------------------------------------------------------------------------
            */

            $break = $this->calendar->getLunchBreak($current);

            /*
            |--------------------------------------------------------------------------
            | Available minutes
            |--------------------------------------------------------------------------
            */

            $available = $current->diffInMinutes($shiftEnd);

            /*
            |--------------------------------------------------------------------------
            | If lunch break is ahead
            |--------------------------------------------------------------------------
            */

            if ($break && $current->lt($break['start'])) {

                $availableBeforeBreak = $current->diffInMinutes($break['start']);

                if ($minutes <=$availableBeforeBreak) {

                    return $current->addMinutes($minutes);

                }

                $minutes -= $availableBeforeBreak;

                $current = $break['end'];

                continue;
            }


            $available = $current->diffInMinutes($shiftEnd);
            /*
            |--------------------------------------------------------------------------
            | SLA fits inside current shift
            |--------------------------------------------------------------------------
            */

            if ($minutes <= $available) {

                return $current->addMinutes($minutes);

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

            $current = $this->calendar->nextWorkingDay($current);

        }

        return $current;
    }



}