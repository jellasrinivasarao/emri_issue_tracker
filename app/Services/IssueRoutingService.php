<?php

namespace App\Services;

use App\Models\WorkingCalendar;
use Carbon\CarbonInterface;

use App\Models\Issue;
use App\Models\IssueAssignment;
use App\Models\IssueRoutingRule;
use Illuminate\Support\Facades\DB;
use App\Models\IssueHistory;

class IssueRoutingService
{
    public function __construct(protected WorkingCalendarEngine $calendarEngine) {}



    public function resolve(Issue $issue): ?IssueRoutingRule
    {
        return IssueRoutingRule::query()
            ->where('project_id', $issue->project_id)
            ->where('is_active', 1)

            ->where(function ($query) use ($issue) {
                $query->whereNull('issue_category_id')
                    ->orWhere('issue_category_id', $issue->issue_category_id);
            })

            ->where(function ($query) use ($issue) {
                $query->whereNull('issue_type_id')
                    ->orWhere('issue_type_id', $issue->issue_type_id);
            })

            ->where(function ($query) use ($issue) {
                $query->whereNull('priority_id')
                    ->orWhere('priority_id', $issue->priority_id);
            })

            ->orderByDesc('routing_priority')

            ->first();
    }
    public function route(Issue $issue): ?IssueRoutingRule
    {
        return DB::transaction(function () use ($issue) {

            $rule = $this->resolve($issue);

            if (! $rule) {
                $issue->update([
                    'status' => 'UNASSIGNED',
                ]);

                return null;
            }

            $issue->update([
                'routing_rule_id' => $rule->routing_rule_id,
                'support_level' => $rule->support_level,
                'support_team_id' => $rule->support_team_id,
                'sla_hours' => $rule->sla_hours,
                'status' => 'ASSIGNED',
            ]);
            IssueAssignment::create([
                'issue_id' => $issue->issue_id,
                'support_level' => $rule->support_level,
                'support_team_id' => $rule->support_team_id,
                'assignment_type' => 'ROUTING',
                'assigned_at' => now(),
                'created_by' => auth()->id(),
            ]);

            return $rule;
        });
    }

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





    public function routeIssue(Issue $issue): ?IssueAssignment
    {
        return DB::transaction(function () use ($issue) {

            $configuration = $issue->configuration;

            if (! $configuration || ! $configuration->auto_routing_enabled) {
                return null;
            }

            $rule = $this->findMatchingRule($issue);

            if (! $rule) {
                $this->writeHistory(
                    $issue,
                    'ROUTING_FAILED',
                    $issue->status,
                    $issue->status,
                    null,
                    null,
                    'No matching routing rule found.'
                );

                return null;
            }

            $oldTeam = $issue->current_team_id;

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
                'OPEN',
                'ASSIGNED',
                $oldTeam,
                $rule->support_team_id,
                'Issue automatically routed using rule: ' . $rule->rule_code
            );

            return $assignment;
        });
    }

    protected function findMatchingRule(Issue $issue): ?IssueRoutingRule
    {
        $query = IssueRoutingRule::query()
            ->where('support_config_id', $issue->support_config_id)
            ->where('is_active', 1);

        if ($issue->issue_category) {
            $query->where(function ($q) use ($issue) {
                $q->whereNull('issue_category')
                    ->orWhere('issue_category', $issue->issue_category);
            });
        }

        if ($issue->issue_type) {
            $query->where(function ($q) use ($issue) {
                $q->whereNull('issue_type')
                    ->orWhere('issue_type', $issue->issue_type);
            });
        }

        if ($issue->priority) {
            $query->where(function ($q) use ($issue) {
                $q->whereNull('priority')
                    ->orWhere('priority', $issue->priority);
            });
        }

        return $query
            ->orderByDesc('is_default')
            ->orderBy('routing_level')
            ->first();
    }

    protected function writeHistory(
        Issue $issue,
        string $action,
        ?string $fromStatus,
        ?string $toStatus,
        ?int $fromTeam,
        ?int $toTeam,
        ?string $remarks = null
    ): void {
        IssueHistory::create([
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

    public function manuallyRoute(
        Issue $issue,
        int $teamId,
        ?string $remarks = null
    ): IssueAssignment {

        return DB::transaction(function () use ($issue, $teamId, $remarks) {

            $oldTeam = $issue->current_team_id;

            $assignment = IssueAssignment::create([
                'issue_id' => $issue->issue_id,
                'support_team_id' => $teamId,
                'assignment_level' => 1,
                'assignment_type' => 'MANUAL',
                'status' => 'ASSIGNED',
                'assigned_at' => now(),
                'remarks' => $remarks,
            ]);

            $issue->update([
                'current_team_id' => $teamId,
                'status' => 'ASSIGNED',
                'assigned_at' => now(),
            ]);

            $this->writeHistory(
                $issue,
                'MANUAL_ROUTING',
                'ASSIGNED',
                'ASSIGNED',
                $oldTeam,
                $teamId,
                $remarks
            );

            return $assignment;
        });
    }
}