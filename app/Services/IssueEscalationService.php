<?php

namespace App\Services;

use App\Models\Issue;
use App\Models\IssueEscalation;
use App\Models\SupportGroup;
use Illuminate\Support\Facades\DB;

class IssueEscalationService
{
    /*
    |--------------------------------------------------------------------------
    | Create Escalation
    |--------------------------------------------------------------------------
    */

    public function escalate(
        Issue $issue,
        int $toGroupId,
        string $type,
        string $reason,
        ?int $level = null,
        ?string $slaType = null,
        $slaDueAt = null
    ): IssueEscalation {

        return DB::transaction(function () use (
            $issue,
            $toGroupId,
            $type,
            $reason,
            $level,
            $slaType,
            $slaDueAt
        ) {

            /*
            |--------------------------------------------------------------------------
            | Prevent Duplicate Active Escalation
            |--------------------------------------------------------------------------
            */

            $existing = IssueEscalation::query()
                ->where('issue_id', $issue->issue_id)
                ->where(
                    'escalation_type',
                    $type
                )
                ->whereIn(
                    'escalation_status',
                    [
                        'PENDING',
                        'SENT',
                        'ACKNOWLEDGED',
                        'IN_PROGRESS',
                    ]
                )
                ->latest('escalation_id')
                ->first();

            if ($existing) {
                return $existing;
            }

            /*
            |--------------------------------------------------------------------------
            | Determine Level
            |--------------------------------------------------------------------------
            */

            if (!$level) {

                $lastLevel =
                    IssueEscalation::query()
                        ->where(
                            'issue_id',
                            $issue->issue_id
                        )
                        ->max(
                            'escalation_level'
                        );

                $level =
                    ((int) $lastLevel) + 1;
            }

            /*
            |--------------------------------------------------------------------------
            | Create Escalation
            |--------------------------------------------------------------------------
            */

            return IssueEscalation::create([

                'issue_id' =>
                    $issue->issue_id,

                'escalation_level' =>
                    $level,

                'escalation_type' =>
                    $type,

                'escalated_from_group_id' =>
                    $issue->current_support_group_id,

                'escalated_to_group_id' =>
                    $toGroupId,

                'escalated_by' =>
                    auth()->id(),

                'escalated_at' =>
                    now(),

                'escalation_status' =>
                    'PENDING',

                'reason' =>
                    $reason,

                'sla_type' =>
                    $slaType,

                'sla_due_at' =>
                    $slaDueAt,

            ]);
        });
    }


    public function send(
    IssueEscalation $escalation
): IssueEscalation {

    if (
        $escalation->escalation_status !== 'PENDING'
    ) {
        return $escalation;
    }

    /*
    |--------------------------------------------------------------------------
    | Notification logic goes here
    |--------------------------------------------------------------------------
    |
    | NotificationService
    | Email
    | In-app notification
    | Queue
    |
    */

    $escalation->update([

        'escalation_status' =>
            'SENT',

    ]);

    return $escalation->fresh();
}


public function acknowledge(
    IssueEscalation $escalation,
    int $userId
): IssueEscalation {

    if (
        !in_array(
            $escalation->escalation_status,
            [
                'SENT',
                'PENDING',
            ]
        )
    ) {
        return $escalation;
    }

    $escalation->update([

        'escalation_status' =>
            'ACKNOWLEDGED',

        'acknowledged_at' =>
            now(),

        'acknowledged_by' =>
            $userId,

    ]);

    return $escalation->fresh();
}



public function start(
    IssueEscalation $escalation
): IssueEscalation {

    if (
        $escalation->escalation_status
        !== 'ACKNOWLEDGED'
    ) {
        return $escalation;
    }

    $escalation->update([

        'escalation_status' =>
            'IN_PROGRESS',

    ]);

    return $escalation->fresh();
}




public function resolve(
    IssueEscalation $escalation,
    ?int $userId = null,
    ?string $remarks = null
): IssueEscalation {

    if (
        $escalation->escalation_status
        === 'RESOLVED'
    ) {
        return $escalation;
    }

    $escalation->update([

        'escalation_status' =>
            'RESOLVED',

        'resolved_at' =>
            now(),

        'resolved_by' =>
            $userId ?? auth()->id(),

        'remarks' =>
            $remarks,

    ]);

    return $escalation->fresh();
}



public function cancel(
    IssueEscalation $escalation,
    ?string $remarks = null
): IssueEscalation {

    if (
        in_array(
            $escalation->escalation_status,
            [
                'RESOLVED',
                'CANCELLED',
            ]
        )
    ) {
        return $escalation;
    }

    $escalation->update([

        'escalation_status' =>
            'CANCELLED',

        'remarks' =>
            $remarks,

    ]);

    return $escalation->fresh();
}


}