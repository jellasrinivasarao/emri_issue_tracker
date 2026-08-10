<?php

namespace App\Services\IssueRouting;

use App\Models\Issue;

class HoItRoutingService
{
    public function route(
        Issue $issue,
        $configuration,
        $rule,
        $team
    ): Issue {

        $slaHours = $rule->sla_hours
            ?? $configuration->sla_hours;

        $issue->update([

            /*
             * Assigned Support Team
             */
            'current_owner_group_id' =>
                $team->support_team_id,

            /*
             * Routing
             */
            'routing_status' => 'ROUTED',

            /*
             * Assignment
             */
            'assigned_at' => now(),

            /*
             * SLA
             */
            'sla_due_at' =>
                now()->addHours(
                    (float) $slaHours
                ),
        ]);

        return $issue->refresh();
    }
}