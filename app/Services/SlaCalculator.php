<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\SlaPolicy;
use App\Models\WorkingCalendar;

class SlaCalculator
{
    protected BusinessTimeCalculator $businessTime;

    public function __construct(
        BusinessTimeCalculator $businessTime
    ) {
        $this->businessTime = $businessTime;
    }

    public function calculate(
        Carbon $startAt,
        SlaPolicy $policy,
        WorkingCalendar $calendar
    ): array {

        $responseMinutes =
            (int) $policy->response_time_minutes;

        $resolutionMinutes =
            (int) $policy->resolution_time_minutes;

        $responseWarningMinutes =
            $this->warningMinutes(
                $responseMinutes,
                $policy->response_warning_percent
            );

        $resolutionWarningMinutes =
            $this->warningMinutes(
                $resolutionMinutes,
                $policy->resolution_warning_percent
            );

        /*
        |--------------------------------------------------------------------------
        | Configure calendar
        |--------------------------------------------------------------------------
        */

        $calendarService =
            new WorkingCalendarService($calendar);

        $this->businessTime->setCalendar(
            $calendarService
        );

        /*
        |--------------------------------------------------------------------------
        | Calculate SLA
        |--------------------------------------------------------------------------
        */

        $responseDueAt =
            $this->businessTime->addMinutes(
                $startAt->copy(),
                $responseMinutes
            );

        $resolutionDueAt =
            $this->businessTime->addMinutes(
                $startAt->copy(),
                $resolutionMinutes
            );

        $responseWarningAt =
            $this->businessTime->addMinutes(
                $startAt->copy(),
                $responseWarningMinutes
            );

        $resolutionWarningAt =
            $this->businessTime->addMinutes(
                $startAt->copy(),
                $resolutionWarningMinutes
            );

        return [
            'response_due_at' =>
                $responseDueAt,

            'resolution_due_at' =>
                $resolutionDueAt,

            'response_warning_at' =>
                $responseWarningAt,

            'resolution_warning_at' =>
                $resolutionWarningAt,
        ];
    }

    protected function warningMinutes(
        int $slaMinutes,
        $percentage
    ): int {

        return (int) round(
            $slaMinutes *
            ((float) $percentage / 100)
        );
    }
}