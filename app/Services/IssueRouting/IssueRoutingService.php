<?php

namespace App\Services\IssueRouting;

use App\Enums\IssueRoutingStatus;
use App\Models\Issue;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class IssueRoutingService
{
    public function __construct(
        protected VendorRoutingService $vendorRoutingService,
        protected HoItRoutingService $hoItRoutingService,
    ) {
    }

    public function route(Issue $issue): Issue
    {
        return DB::transaction(function () use ($issue) {

            /*
             * ---------------------------------------------------------
             * 1. Load configuration + routing rules + team
             * ---------------------------------------------------------
             */
            $issue->load([
                'configuration.routingRules.team',
            ]);

            $configuration = $issue->configuration;

            if (!$configuration) {

                $this->markFailed($issue);

                throw new RuntimeException(
                    'No support configuration found for this issue.'
                );
            }


            /*
             * ---------------------------------------------------------
             * 2. Configuration active?
             * ---------------------------------------------------------
             */
            if (!$configuration->is_active) {

                $this->markFailed($issue);

                throw new RuntimeException(
                    'Support configuration is inactive.'
                );
            }


            /*
             * ---------------------------------------------------------
             * 3. Find matching routing rule
             * ---------------------------------------------------------
             */
            $rule = $this->findRoutingRule(
                $issue,
                $configuration
            );

            if (!$rule) {

                $this->markFailed($issue);

                throw new RuntimeException(
                    'No active routing rule found for this issue.'
                );
            }


            /*
             * ---------------------------------------------------------
             * 4. Get support team
             * ---------------------------------------------------------
             */
            $team = $rule->team;

            if (!$team) {

                $this->markFailed($issue);

                throw new RuntimeException(
                    'No support team configured for routing rule.'
                );
            }


            if (!$team->is_active) {

                $this->markFailed($issue);

                throw new RuntimeException(
                    'Support team is inactive.'
                );
            }


            /*
             * ---------------------------------------------------------
             * 5. Determine Team Type
             * ---------------------------------------------------------
             */
            $teamType = strtoupper(
                trim($team->team_type)
            );


            /*
             * ---------------------------------------------------------
             * 6. HO IT
             * ---------------------------------------------------------
             */
            if ($teamType === 'HO_IT') {

                return $this->hoItRoutingService->route(
                    $issue,
                    $configuration,
                    $rule,
                    $team
                );
            }


            /*
             * ---------------------------------------------------------
             * 7. VENDOR
             * ---------------------------------------------------------
             */
            if ($teamType === 'VENDOR') {

                return $this->vendorRoutingService->route(
                    $issue,
                    $configuration,
                    $rule,
                    $team
                );
            }


            /*
             * ---------------------------------------------------------
             * 8. Invalid Team Type
             * ---------------------------------------------------------
             */
            $this->markFailed($issue);

            throw new RuntimeException(
                "Unsupported team type: {$team->team_type}"
            );
        });
    }


    /**
     * Find best matching routing rule.
     */
    protected function findRoutingRule(
        Issue $issue,
        $configuration
    ) {
        $rules = $configuration->routingRules
            ->where('is_active', true);


        /*
         * Project
         */
        $rules = $rules->filter(function ($rule) use ($issue) {

            return (int) $rule->project_id ===
                (int) $issue->project_id;
        });


        /*
         * Category
         */
        $categoryRules = $rules->filter(function ($rule) use ($issue) {

            return (int) $rule->issue_category_id ===
                (int) $issue->issue_category_id;
        });

        if ($categoryRules->isNotEmpty()) {
            $rules = $categoryRules;
        }


        /*
         * Priority
         */
        $priorityRules = $rules->filter(function ($rule) use ($issue) {

            return (int) $rule->priority_id ===
                (int) $issue->priority_id;
        });

        if ($priorityRules->isNotEmpty()) {
            $rules = $priorityRules;
        }


        /*
         * Routing priority
         */
        return $rules
            ->sortBy(function ($rule) {
                return $rule->routing_priority ?? PHP_INT_MAX;
            })
            ->first();
    }


    protected function markFailed(Issue $issue): void
    {
        $issue->update([
            'routing_status' =>
                IssueRoutingStatus::FAILED->value,
        ]);
    }
}