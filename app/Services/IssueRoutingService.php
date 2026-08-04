<?php

namespace App\Services;

use App\Models\WorkingCalendar;
use Carbon\CarbonInterface;

class IssueRoutingService
{
    public function __construct(protected WorkingCalendarEngine $calendarEngine) {}

    public function determineRoute(WorkingCalendar $calendar,bool $hoInterventionRequired,?CarbonInterface $dateTime = null): array {

        /*
        |--------------------------------------------------------------------------
        | HO intervention not required
        |--------------------------------------------------------------------------
        */

        if (!$hoInterventionRequired) {

            return [
                'route' => 'VENDOR_LEVEL_2',
                'reason' => 'HO_INTERVENTION_NOT_REQUIRED',
                'is_working' => false,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Check HO working hours
        |--------------------------------------------------------------------------
        */

        $calendarResult = $this->calendarEngine->check(
            $calendar,
            $dateTime
        );

        /*
        |--------------------------------------------------------------------------
        | HO is working
        |--------------------------------------------------------------------------
        */

        if ($calendarResult['is_working']) {

            return [
                'route' => 'HO_IT_LEVEL_1',
                'reason' => $calendarResult['status'],
                'is_working' => true,
                'calendar' => $calendarResult,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | HO is not working
        |--------------------------------------------------------------------------
        */

        return [
            'route' => 'VENDOR_LEVEL_2',
            'reason' => $calendarResult['status'],
            'is_working' => false,
            'calendar' => $calendarResult,
        ];
    }
}