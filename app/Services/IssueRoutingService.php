<?php

namespace App\Services;

use App\Models\Issue;
use App\Models\IssueAssignment;
use App\Models\IssueHistory;
use App\Models\ProjectSupportConfiguration;

use App\Models\IssueRoutingRule;
use App\Models\WorkingCalendar;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;

class IssueRoutingService
{
    public function __construct(
        protected WorkingCalendarEngine $calendarEngine
    ) {
    }

    /*
    |--------------------------------------------------------------------------
    | CONFIGURATION
    |--------------------------------------------------------------------------
    */

    public function createConfiguration(array $data)
    {
        return DB::transaction(function () use ($data) {

            $data['is_active'] =
                $data['is_active'] ?? true;

            return ProjectSupportConfiguration::create($data);
        });
    }

    public function updateConfiguration(
        ProjectSupportConfiguration $configuration,
        array $data
    ) {

        return DB::transaction(function () use (
            $configuration,
            $data
        ) {

            $data['is_active'] =
                $data['is_active'] ?? false;

            $configuration->update($data);

            return $configuration->fresh();
        });
    }

    public function toggleConfiguration(
        ProjectSupportConfiguration $configuration
    ) {

        return DB::transaction(function () use ($configuration) {

            $configuration->is_active =
                !$configuration->is_active;

            $configuration->save();

            return $configuration;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | FIND ROUTING RULE
    |--------------------------------------------------------------------------
    */

    protected function findMatchingRule(
        Issue $issue
    ): ?IssueRoutingRule {

        return IssueRoutingRule::query()

            ->where(
                'support_config_id',
                $issue->support_config_id
            )

            ->where(
                'is_active',
                1
            )

            /*
             * Category
             */
            ->where(function ($query) use ($issue) {

                $query
                    ->whereNull('issue_category')
                    ->orWhere(
                        'issue_category',
                        $issue->issue_category_id
                    );
            })

            /*
             * Issue Type
             */
            ->where(function ($query) use ($issue) {

                $query
                    ->whereNull('issue_type')
                    ->orWhere(
                        'issue_type',
                        $issue->issue_type_id
                    );
            })

            /*
             * Priority
             */
            ->where(function ($query) use ($issue) {

                $query
                    ->whereNull('priority')
                    ->orWhere(
                        'priority',
                        $issue->priority_id
                    );
            })

            ->orderByDesc('is_default')

            ->orderBy('routing_level')

            ->first();
    }

    /*
    |--------------------------------------------------------------------------
    | DETERMINE ROUTE
    |--------------------------------------------------------------------------
    */

    public function determineRoute(
        ?WorkingCalendar $calendar,
        bool $hoInterventionRequired,
        ?CarbonInterface $dateTime = null
    ): array {

        /*
        |--------------------------------------------------------------------------
        | HO intervention is not required
        |--------------------------------------------------------------------------
        */

        if (!$hoInterventionRequired) {

            return [
                'route' =>
                    'VENDOR_LEVEL_2',

                'reason' =>
                    'HO_INTERVENTION_NOT_REQUIRED',

                'is_working' =>
                    false,

                'calendar' =>
                    null,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | HO intervention required but calendar missing
        |--------------------------------------------------------------------------
        */

        if (!$calendar) {

            return [
                'route' =>
                    'VENDOR_LEVEL_2',

                'reason' =>
                    'HO_WORKING_CALENDAR_NOT_CONFIGURED',

                'is_working' =>
                    false,

                'calendar' =>
                    null,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Check Working Calendar
        |--------------------------------------------------------------------------
        */

        $calendarResult =
            $this->calendarEngine->check(
                $calendar,
                $dateTime
            );

        /*
        |--------------------------------------------------------------------------
        | HO unavailable
        |--------------------------------------------------------------------------
        |
        | Holiday
        | Weekend
        | No schedule
        | Outside business hours
        | Calendar inactive
        |
        */

        if (!$calendarResult['is_working']) {

            return [
                'route' =>
                    'VENDOR_LEVEL_2',

                'reason' =>
                    $calendarResult['status'],

                'is_working' =>
                    false,

                'calendar' =>
                    $calendarResult,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | HO available
        |--------------------------------------------------------------------------
        */

        return [
            'route' =>
                'HO_IT_LEVEL_1',

            'reason' =>
                $calendarResult['status'],

            'is_working' =>
                true,

            'calendar' =>
                $calendarResult,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | ROUTE ISSUE
    |--------------------------------------------------------------------------
    */

    public function routeIssue(
        Issue $issue
    ): ?IssueAssignment {

        return DB::transaction(function () use ($issue) {

            /*
            |--------------------------------------------------------------------------
            | Load Configuration
            |--------------------------------------------------------------------------
            */

            $configuration =
                $issue->configuration;

            if (
                !$configuration ||
                !$configuration->auto_routing_enabled
            ) {

                $this->writeHistory(
                    $issue,
                    'ROUTING_SKIPPED',
                    $issue->status,
                    $issue->status,
                    $issue->current_team_id,
                    null,
                    'Auto routing is disabled.'
                );

                return null;
            }

            /*
            |--------------------------------------------------------------------------
            | Find Rule
            |--------------------------------------------------------------------------
            */

            $rule =
                $this->findMatchingRule($issue);

            if (!$rule) {

                $issue->update([
                    'status' => 'UNASSIGNED',
                ]);

                $this->writeHistory(
                    $issue,
                    'ROUTING_FAILED',
                    $issue->status,
                    'UNASSIGNED',
                    $issue->current_team_id,
                    null,
                    'No matching routing rule found.'
                );

                return null;
            }

            /*
            |--------------------------------------------------------------------------
            | HO Intervention
            |--------------------------------------------------------------------------
            */

            $hoInterventionRequired =
                (bool) $rule->ho_intervention_required;

            /*
            |--------------------------------------------------------------------------
            | Working Calendar
            |--------------------------------------------------------------------------
            */

            $calendar =
                $configuration->workingCalendar;

            /*
            |--------------------------------------------------------------------------
            | Determine Final Route
            |--------------------------------------------------------------------------
            */

            $routeDecision =
                $this->determineRoute(
                    $calendar,
                    $hoInterventionRequired,
                    $issue->created_at ?? now()
                );

            /*
            |--------------------------------------------------------------------------
            | Resolve Team
            |--------------------------------------------------------------------------
            */

            $teamId =
                $this->resolveTeam(
                    $rule,
                    $routeDecision['route']
                );

            /*
            |--------------------------------------------------------------------------
            | Team Not Configured
            |--------------------------------------------------------------------------
            */

            if (!$teamId) {

                $issue->update([
                    'status' => 'UNASSIGNED',
                ]);

                $this->writeHistory(
                    $issue,
                    'ROUTING_FAILED',
                    $issue->status,
                    'UNASSIGNED',
                    $issue->current_team_id,
                    null,
                    'No team configured for route: ' .
                    $routeDecision['route']
                );

                return null;
            }

            /*
            |--------------------------------------------------------------------------
            | Previous Team
            |--------------------------------------------------------------------------
            */

            $oldTeam =
                $issue->current_team_id;

            /*
            |--------------------------------------------------------------------------
            | Create Assignment
            |--------------------------------------------------------------------------
            */

            $assignment =
                IssueAssignment::create([

                    'issue_id' =>
                        $issue->issue_id,

                    'routing_rule_id' =>
                        $rule->routing_rule_id,
                    'support_config_id' => $issue->support_config_id,

                    'support_team_id' =>
                        $teamId,

                    'assignment_level' =>
                        $routeDecision['route']
                            === 'HO_IT_LEVEL_1'
                            ? 1
                            : 2,

                    'assignment_type' =>
                        'AUTO',

                    'status' =>
                        'ASSIGNED',

                    'assigned_at' =>
                        now(),
                    'assignment_reason' => 'WORKING_HOURS',
                    'remarks'           => 'HO IT Level 1 assigned automatically.',
                    'assigned_by'       => auth()->id(),

                    // 'remarks' =>
                    //     $routeDecision['reason'],
                ]);

            /*
            |--------------------------------------------------------------------------
            | Update Issue
            |--------------------------------------------------------------------------
            */

            $issue->update([

                'current_team_id' =>
                    $teamId,

                'routing_rule_id' =>
                    $rule->routing_rule_id,

                'status' =>
                    'ASSIGNED',

                'assigned_at' =>
                    now(),
            ]);

            /*
            |--------------------------------------------------------------------------
            | History
            |--------------------------------------------------------------------------
            */

            $this->writeHistory(
                $issue,
                'AUTO_ROUTED',
                'Open',
                'ASSIGNED',
                $oldTeam,
                $teamId,
                'Route: ' .
                $routeDecision['route'] .
                ' | Reason: ' .
                $routeDecision['reason']
            );

            return $assignment;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | RESOLVE TEAM
    |--------------------------------------------------------------------------
    */

    protected function resolveTeam(
        IssueRoutingRule $rule,
        string $route
    ): ?int {

        /*
        |--------------------------------------------------------------------------
        | HO IT LEVEL 1
        |--------------------------------------------------------------------------
        */

        if ($route === 'HO_IT_LEVEL_1') {

            return $rule->ho_it_team_id
                ?? $rule->support_team_id;
        }

        /*
        |--------------------------------------------------------------------------
        | VENDOR LEVEL 2
        |--------------------------------------------------------------------------
        */

        if ($route === 'VENDOR_LEVEL_2') {

            return $rule->vendor_team_id
                ?? $rule->support_team_id;
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | HISTORY
    |--------------------------------------------------------------------------
    */

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

            'issue_id' =>
                $issue->issue_id,

            'action' =>
                $action,

            'from_status' =>
                $fromStatus,

            'to_status' =>
                $toStatus,

            'from_team_id' =>
                $fromTeam,

            'to_team_id' =>
                $toTeam,

            'remarks' =>
                $remarks,

            'performed_by' =>
                auth()->id(),

            'created_at' =>
                now(),
        ]);
    }
}