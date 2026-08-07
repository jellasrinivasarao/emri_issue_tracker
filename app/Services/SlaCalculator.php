<?php

namespace App\Services;

use Carbon\Carbon;

use App\Models\SlaPolicy;
use App\Models\WorkingCalendar;

class SlaCalculator
{

        protected BusinessTimeCalculator $businessTime;

        public function __construct(BusinessTimeCalculator $businessTime) {
                $this->businessTime = $businessTime;
        }
    
        // public function calculate(Carbon $start,int $minutes,int $calendarId): Carbon
        // {
            
        // }

        public function calculate(Carbon $startAt,SlaPolicy $policy,WorkingCalendar $calendar): array {


        /*
        |--------------------------------------------------------------------------
        | Working Calendar
        |--------------------------------------------------------------------------
        */

        $calendarService =
            new WorkingCalendarService($calendar);

        $this->businessTime->setCalendar(
            $calendarService
        );

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        $responseDueAt =
            $this->businessTime->addMinutes(
                $startAt,
                (int) $policy->response_time_minutes
            );

        /*
        |--------------------------------------------------------------------------
        | Resolution
        |--------------------------------------------------------------------------
        */

        $resolutionDueAt =
            $this->businessTime->addMinutes(
                $startAt,
                (int) $policy->resolution_time_minutes
            );

        return [

            'response_due_at' =>
                $responseDueAt,

            'resolution_due_at' =>
                $resolutionDueAt,

        ];
        
        }
}