<?php

namespace App\Services\IssueRouting;

use App\Models\Issue;
use RuntimeException;

class VendorRoutingService
{
    public function __construct(
        protected BusinessHoursService $businessHoursService
    ) {
    }

    public function route(
        Issue $issue,
        $configuration,
        $rule,
        $team
    ): Issue {

        /*
         * ---------------------------------------------------------
         * Get vendor calendar
         * ---------------------------------------------------------
         *
         * This will come from the team-calendar mapping.
         */
        $calendarId =
            $this->businessHoursService
                ->getCalendarIdForTeam(
                    $team->support_team_id
                );


        if (!$calendarId) {

            throw new RuntimeException(
                'Business calendar is not configured for vendor team.'
            );
        }


        /*
         * ---------------------------------------------------------
         * Check business hours
         * ---------------------------------------------------------
         */
        $isBusinessHour =
            $this->businessHoursService
                ->isBusinessHour(
                    $calendarId
                );


        /*
         * ---------------------------------------------------------
         * Outside business hours
         * ---------------------------------------------------------
         */
        if (!$isBusinessHour) {

            $issue->update([

                'routing_status' =>
                    'WAITING_BUSINESS_HOURS',

                'assigned_at' => null,

            ]);

            return $issue->refresh();
        }


        /*
         * ---------------------------------------------------------
         * Business hours → Assign Vendor
         * ---------------------------------------------------------
         */
        $slaHours = $rule->sla_hours
            ?? $configuration->sla_hours;


        $issue->update([

            'current_owner_group_id' =>
                $team->support_team_id,

            'routing_status' =>
                'ROUTED',

            'assigned_at' =>
                now(),

            'sla_due_at' =>
                now()->addHours(
                    (float) $slaHours
                ),
        ]);


        return $issue->refresh();
    }
}