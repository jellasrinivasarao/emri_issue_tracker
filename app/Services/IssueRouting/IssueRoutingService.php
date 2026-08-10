<?php

namespace App\Services\IssueRouting;

use App\Enums\IssueRoutingStatus;
use App\Models\Issue;
use App\Models\IssueAssignment;
use App\Models\ProjectSupportConfiguration;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class IssueRoutingService
{
    public function __construct(
        protected VendorRoutingService $vendorRoutingService,
        protected HoItRoutingService $hoItRoutingService,
    ) {
    }

    /**
     * Route an issue to the appropriate support team.
     *
     * @throws RuntimeException
     */
    public function route(Issue $issue): ?IssueAssignment
{
    return DB::transaction(function () use ($issue) {

        $configuration = $issue->configuration;



        dd([
    'issue_id' => $issue->issue_id,
    'project_id' => $issue->project_id,
    'support_config_id' => $issue->support_config_id,

    'configuration_id' =>
        $issue->configuration?->support_config_id,

    'configuration_project_id' =>
        $issue->configuration?->project_id,

    'configuration_active' =>
        $issue->configuration?->is_active,

    'auto_routing_enabled' =>
        $issue->configuration?->auto_routing_enabled,
]);



        if (! $configuration && $issue->support_config_id) {
            $configuration = ProjectSupportConfiguration::find(
                $issue->support_config_id
            );
        }

        if (! $configuration && $issue->project_id) {
            $configuration = ProjectSupportConfiguration::query()
                ->where('project_id', $issue->project_id)
                ->where('is_active', 1)
                ->orderByDesc('support_config_id')
                ->first();
        }

        if (! $configuration) {
            throw new \RuntimeException(
                "No support configuration found for issue {$issue->issue_id}"
            );
        }

        $configuration->load('routingRules.team');

        if (! $configuration->auto_routing_enabled) {
            return null;
        }

        $rule = $this->findMatchingRule($issue);

        if (! $rule) {
            $this->writeHistory(
                $issue,
                'ROUTING_FAILED',
                $issue->status,
                $issue->status,
                $issue->current_team_id,
                null,
                'No matching routing rule found.'
            );

            return null;
        }

        $oldTeam = $issue->current_team_id;


        $issue->update([
    'current_team_id' => $rule->support_team_id,
    'status' => 'ASSIGNED',
    'assigned_at' => now(),
]);

$this->writeHistory(
    $issue,
    'AUTO_ROUTED',
    'OPEN',
    'ASSIGNED',
    $oldTeam,
    $rule->support_team_id,
    'Issue automatically routed using rule: ' . $rule->rule_code
);



        $assignment = IssueAssignment::create([
            'issue_id' => $issue->issue_id,
            'routing_rule_id' => $rule->routing_rule_id,
            'support_team_id' => $rule->support_team_id,
            'assignment_level' => $rule->routing_level,
            'assignment_type' => 'AUTO',
            'status' => 'ASSIGNED',
            'assigned_at' => now(),
        ]);

        $issue->update([
            'current_team_id' => $rule->support_team_id,
            'status' => 'ASSIGNED',
            'assigned_at' => now(),
        ]);

        $this->writeHistory(
            $issue,
            'AUTO_ROUTED',
            $issue->status,
            'ASSIGNED',
            $oldTeam,
            $rule->support_team_id,
            'Issue automatically routed using rule: ' . $rule->rule_code
        );

        return $assignment;
    });
}



protected function writeHistory(
    Issue $issue,
    string $action,
    ?string $fromStatus,
    ?string $toStatus,
    ?int $fromTeam,
    ?int $toTeam,
    ?string $remarks = null
): IssueHistory {
    return IssueHistory::create([
        'issue_id' => $issue->issue_id,
        'action' => $action,
        'from_status' => $fromStatus,
        'to_status' => $toStatus,
        'from_team_id' => $fromTeam,
        'to_team_id' => $toTeam,
        'remarks' => $remarks,
        'performed_by' => auth()->id(),
        'created_at' => now(),
    ]);
}



    /**
     * Find the best matching routing rule.
     */
    protected function findRoutingRule(
        Issue $issue,
        $configuration
    ) {
        $rules = $configuration->routingRules
            ->where('is_active', true);

        /*
         * -------------------------------------------------------------
         * 1. Project must match
         * -------------------------------------------------------------
         */
        $rules = $rules->filter(function ($rule) use ($issue) {
            return (int) $rule->project_id ===
                (int) $issue->project_id;
        });

        /*
         * -------------------------------------------------------------
         * 2. Prefer Category-specific rule
         * -------------------------------------------------------------
         */
        $categoryRules = $rules->filter(function ($rule) use ($issue) {
            return (int) $rule->issue_category_id ===
                (int) $issue->issue_category_id;
        });

        if ($categoryRules->isNotEmpty()) {
            $rules = $categoryRules;
        }

        /*
         * -------------------------------------------------------------
         * 3. Prefer Priority-specific rule
         * -------------------------------------------------------------
         */
        $priorityRules = $rules->filter(function ($rule) use ($issue) {
            return (int) $rule->priority_id ===
                (int) $issue->priority_id;
        });

        if ($priorityRules->isNotEmpty()) {
            $rules = $priorityRules;
        }

        /*
         * -------------------------------------------------------------
         * 4. Lowest routing_priority wins
         * -------------------------------------------------------------
         */
        return $rules
            ->sortBy(function ($rule) {
                return $rule->routing_priority ?? PHP_INT_MAX;
            })
            ->first();
    }

    /**
     * Mark issue routing as failed.
     */
    protected function markFailed(
        Issue $issue,
        ?string $reason = null
    ): void {
        $data = [
            'routing_status' => IssueRoutingStatus::FAILED->value,
        ];

        /*
         * Add this only if your txn_issue table has routing_error/message.
         */
        if (
            $reason !== null &&
            in_array('routing_error', $issue->getFillable(), true)
        ) {
            $data['routing_error'] = $reason;
        }

        $issue->update($data);
    }



    protected function findMatchingRule(Issue $issue): ?Model
{
    $configuration = $issue->configuration;

    if (! $configuration) {
        return null;
    }

    return $configuration->routingRules()
        ->where('is_active', true)
        ->where('project_id', $issue->project_id)
        ->where(function ($query) use ($issue) {
            $query->whereNull('issue_category_id')
                ->orWhere(
                    'issue_category_id',
                    $issue->issue_category_id
                );
        })
        ->where(function ($query) use ($issue) {
            $query->whereNull('priority_id')
                ->orWhere(
                    'priority_id',
                    $issue->priority_id
                );
        })
        ->orderByRaw(
            'CASE WHEN issue_category_id IS NULL THEN 1 ELSE 0 END'
        )
        ->orderByRaw(
            'CASE WHEN priority_id IS NULL THEN 1 ELSE 0 END'
        )
        ->orderBy('routing_priority')
        ->first();
}
}