<?php

namespace App\Services;

use App\Models\Issue;
use App\Models\SlaPolicy;
use App\Models\IssueSla;
use App\Models\WorkingCalendar;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Illuminate\Support\Facades\Log;

class IssueSlaService
{
    protected SlaCalculator $slaCalculator;

    public function __construct(SlaCalculator $slaCalculator) {
        $this->slaCalculator = $slaCalculator;
    }


    public function validateConfiguration(int $projectId,int $applicationId,int $serviceId,int $priorityId): array {

    $policy = SlaPolicy::query()
        ->where('project_id', $projectId)
        ->where('application_id', $applicationId)
        ->where('service_id', $serviceId)
        ->where('priority_id', $priorityId)
        ->where('is_active', 1)
        ->where(function ($query) {
            $query->whereNull('effective_from')
                ->orWhere('effective_from', '<=', now());
        })
        ->where(function ($query) {
            $query->whereNull('effective_to')
                ->orWhere('effective_to', '>=', now());
        })
        ->first();

    if (!$policy) {
        return [
            'valid' => false,
            'message' => 'No active SLA policy configured.',
        ];
    }

    if (!$policy->calendar_id) {
        return [
            'valid' => false,
            'message' => 'No working calendar configured for SLA policy.',
        ];
    }

    $calendar = WorkingCalendar::query()
        ->where('calendar_id', $policy->calendar_id)
        ->where('is_active', 1)
        ->first();

    if (!$calendar) {
        return [
            'valid' => false,
            'message' => 'The working calendar configured for this SLA policy is inactive or missing.',
        ];
    }

    return [
        'valid' => true,
        'policy' => $policy,
        'calendar' => $calendar,
    ];
}

    /**
     * Create SLA for a newly created issue.
     */
    public function createForIssue(Issue $issue): IssueSla {

        return DB::transaction(function () use ($issue) {


        Log::info('SLA lookup started', [
        'issue_id' => $issue->issue_id,
        'project_id' => $issue->project_id,
        'application_id' => $issue->application_id,
        'service_id' => $issue->service_id,
        'priority_id' => $issue->priority_id,
        'current_time' => now()->toDateTimeString(),
    ]);
            /*
            |--------------------------------------------------------------------------
            | 1. Find matching SLA policy
            |--------------------------------------------------------------------------
            */

            $policy = $this->findPolicy($issue);

            if (!$policy) {

            Log::warning('SLA policy not found', [

                'issue_id' =>
                    $issue->issue_id,

                'project_id' =>
                    $issue->project_id,

                'application_id' =>
                    $issue->application_id,

                'service_id' =>
                    $issue->service_id,

                'priority_id' =>
                    $issue->priority_id,

                'current_time' =>
                    now()->toDateTimeString(),

            ]);

                throw new RuntimeException(
                    'No active SLA policy found for this issue.'
                );

            }

            Log::info('Exact SLA policy found', [
        'sla_policy_id' => $policy->sla_policy_id,
        'sla_policy_code' => $policy->sla_policy_code,
        'project_id' => $policy->project_id,
        'application_id' => $policy->application_id,
        'service_id' => $policy->service_id,
        'priority_id' => $policy->priority_id,
        'calendar_id' => $policy->calendar_id,
    ]);
    

    if (!$policy->calendar_id) {

        Log::error('SLA policy has no calendar configured', [
            'sla_policy_id' => $policy->sla_policy_id,
            'issue_id' => $issue->issue_id,
        ]);

        throw new \RuntimeException(
            'No working calendar configured for the SLA policy.'
        );
    }
    

            /*
            |--------------------------------------------------------------------------
            | 2. Get Working Calendar
            |--------------------------------------------------------------------------
            */

            // $calendar = WorkingCalendar::find(
            //     $policy->calendar_id
            // );

            $calendar = WorkingCalendar::query()
                ->where('calendar_id', $policy->calendar_id)
                ->where('is_active', 1)
                ->first();

            if (!$calendar) {
                Log::error('Active working calendar not found for SLA policy', [
                    'sla_policy_id' => $policy->sla_policy_id,
                    'calendar_id' => $policy->calendar_id,
                    'issue_id' => $issue->issue_id,
                ]);
                throw new RuntimeException(
                    'Active working calendar not found for SLA policy.'
                );

            }

                Log::info('SLA calendar found', [
        'sla_policy_id' => $policy->sla_policy_id,
        'calendar_id' => $calendar->calendar_id,
        'calendar_code' => $calendar->calendar_code,
        'calendar_name' => $calendar->calendar_name,
        'timezone' => $calendar->timezone,
    ]);

            /*
            |--------------------------------------------------------------------------
            | 3. Start time
            |--------------------------------------------------------------------------
            */

            
            ###$startAt = Carbon::now($calendar->timezone ?? config('app.timezone'));

            $startAt = $issue->created_at? Carbon::parse($issue->created_at): now();

            /*
            |--------------------------------------------------------------------------
            | 4. Calculate SLA
            |--------------------------------------------------------------------------
            */

            $calculation = $this->slaCalculator->calculate($startAt,$policy,$calendar);

            /*
            |--------------------------------------------------------------------------
            | 5. Create SLA transaction
            |--------------------------------------------------------------------------
            */

            return IssueSla::create([

                'issue_id' => $issue->issue_id,
                'sla_policy_id' => $policy->sla_policy_id,
                'response_due_at' => $calculation['response_due_at'],
                'resolution_due_at' =>$calculation['resolution_due_at'],
                'response_warning_at' => $calculation['response_warning_at'],
                'resolution_warning_at' => $calculation['resolution_warning_at'],
                'response_status' => 'RUNNING',
                'resolution_status' => 'RUNNING',
                'overall_status' =>'RUNNING',

                'response_completed_at' => null,

                'resolution_completed_at' => null,

                'response_breached_at' => null,

                'resolution_breached_at' => null,

                'is_response_breached' => 0,

                'is_resolution_breached' => 0,

                'is_paused' => 0,

                'paused_at' => null,

                'total_paused_minutes' => 0,

                'created_at' => now(),

            ]);

        });

    }

    /**
     * Find SLA policy applicable to issue.
     */

    protected function findPolicy(Issue $issue): ?SlaPolicy {

        $now = now();

        Log::info('SLA lookup started', [
                'issue_id' => $issue->issue_id,
                'project_id' => $issue->project_id,
                'application_id' => $issue->application_id,
                'service_id' => $issue->service_id,
                'priority_id' => $issue->priority_id,
                'current_time' => $now->toDateTimeString(),
            ]);
    
        /*
    |--------------------------------------------------------------------------
    | 1. Exact Application SLA
    |--------------------------------------------------------------------------
    */
    $policy = SlaPolicy::query()
        ->where('project_id', $issue->project_id)
        ->where('application_id', $issue->application_id)
        ->where('service_id', $issue->service_id)
        ->where('priority_id', $issue->priority_id)
        ->where('is_active', 1)
        ->where(function ($query) use ($now) {

            $query->whereNull('effective_from')
                ->orWhere('effective_from', '<=', $now);

        })
        ->where(function ($query) use ($now) {

            $query->whereNull('effective_to')
                ->orWhere('effective_to', '>=', $now);

        })
        ->first();


        if ($policy) {

        Log::info('Exact SLA policy found', [
            'sla_policy_id' => $policy->sla_policy_id,
            'sla_policy_code' => $policy->sla_policy_code,
            'project_id' => $policy->project_id,
            'application_id' => $policy->application_id,
            'service_id' => $policy->service_id,
            'priority_id' => $policy->priority_id,
        ]);

        return $policy;
    }




            $candidates = SlaPolicy::query()
            ->where('project_id', $issue->project_id)
            ->where('application_id', $issue->application_id)
            ->where('service_id', $issue->service_id)
            ->get([
                'sla_policy_id',
                'sla_policy_code',
                'priority_id',
                'calendar_id',
                'is_active',
                'effective_from',
                'effective_to',
            ]);

        Log::warning('SLA candidates', [
            'issue_id' => $issue->issue_id,
            'required' => [
                'project_id' => $issue->project_id,
                'application_id' => $issue->application_id,
                'service_id' => $issue->service_id,
                'priority_id' => $issue->priority_id,
            ],
            'candidates' => $candidates->toArray(),
        ]);






    /*
    |--------------------------------------------------------------------------
    | 2. Generic Application SLA
    |--------------------------------------------------------------------------
    |
    | application_id = NULL means the SLA applies to all applications.
    |
    */
    $policy = SlaPolicy::query()
        ->where('project_id', $issue->project_id)
        ->whereNull('application_id')
        ->where('service_id', $issue->service_id)
        ->where('priority_id', $issue->priority_id)
        ->where('is_active', 1)
        ->where(function ($query) use ($now) {

            $query->whereNull('effective_from')
                ->orWhere('effective_from', '<=', $now);

        })
        ->where(function ($query) use ($now) {

            $query->whereNull('effective_to')
                ->orWhere('effective_to', '>=', $now);

        })
        ->first();


         if ($policy) {

        Log::info('Generic SLA policy found', [
            'sla_policy_id' => $policy->sla_policy_id,
            'sla_policy_code' => $policy->sla_policy_code,
            'project_id' => $policy->project_id,
            'application_id' => null,
            'service_id' => $policy->service_id,
            'priority_id' => $policy->priority_id,
        ]);

        return $policy;
    }




    /*
    |--------------------------------------------------------------------------
    | 3. Diagnostic information
    |--------------------------------------------------------------------------
    */

    $candidates = SlaPolicy::query()
        ->where('project_id', $issue->project_id)
        ->where('service_id', $issue->service_id)
        ->where('priority_id', $issue->priority_id)
        ->get([
            'sla_policy_id',
            'sla_policy_code',
            'project_id',
            'application_id',
            'service_id',
            'priority_id',
            'calendar_id',
            'is_active',
            'effective_from',
            'effective_to',
        ]);

    Log::warning('No matching SLA policy found', [
        'issue_id' => $issue->issue_id,

        'required' => [
            'project_id' => $issue->project_id,
            'application_id' => $issue->application_id,
            'service_id' => $issue->service_id,
            'priority_id' => $issue->priority_id,
        ],

        'candidate_policies' => $candidates->toArray(),
    ]);


    return null;
    
    }
    
    protected function findPolicy1(Issue $issue): ?SlaPolicy {

        return SlaPolicy::query()
            ->where('is_active', 1)
            ->where('project_id', $issue->project_id)
            ->where('service_id', $issue->service_id)
            ->where('priority_id', $issue->priority_id)

            ->where(function ($query) use ($issue) {

                $query

                    ->whereNull('service_id')

                    ->orWhere(
                        'service_id',
                        $issue->service_id
                    );

            })

            ->where(function ($query) use ($issue) {

                $query

                    ->whereNull('application_id')

                    ->orWhere(
                        'application_id',
                        $issue->application_id
                    );

            })

            ->orderByRaw(
                'CASE
                    WHEN service_id IS NOT NULL THEN 1
                    ELSE 2
                 END'
            )

            ->first();
    }





    // public function markResponseCompleted(IssueSla $sla): IssueSla {

    //         // Response SLA already completed
    //         if ($sla->response_completed_at) {

    //             return $sla;

    //         }
            

    //         $sla->update([

    //             'response_completed_at' => now(),

    //         ]);

    //         return $sla;
    //     }


    public function markResponseCompleted(IssueSla $sla,?Carbon $completedAt = null): IssueSla {

            if ($sla->response_completed_at) {
                return $sla;
            }

            $completedAt ??= now();

            $sla->update([

                'response_completed_at' => $completedAt,
                'response_status' => now()->lte($sla->response_due_at) ? 'MET': 'BREACHED',

            ]);

            return $sla->fresh();
        }


        public function markResolutionCompleted(IssueSla $sla): IssueSla {

            if ($sla->resolution_completed_at) {

                return $sla;

            }

            $sla->update([
                'resolution_completed_at' => now(),
                'resolution_status' => now()->lte($sla->resolution_due_at) ? 'MET': 'BREACHED',

            ]);

            #$this->updateOverallStatus($sla->fresh());

            return $sla->fresh();
        }

        public function checkResponseBreach(IssueSla $sla): bool {

                if (
                    $sla->response_completed_at
                ) {

                    return false;

                }

                if (
                    $sla->is_response_breached
                ) {

                    return true;

                }

                if (
                    now()->gte(
                        $sla->response_due_at
                    )
                ) {

                    $sla->update([

                        'is_response_breached' => 1,

                        'response_breached_at' => now(),

                    ]);

                    return true;
                }

                return false;
            }


            public function checkResolutionBreach(IssueSla $sla): bool {

                    if (
                        $sla->resolution_completed_at
                    ) {

                        return false;

                    }

                    if (
                        $sla->is_resolution_breached
                    ) {

                        return true;

                    }

                    if (
                        now()->gte(
                            $sla->resolution_due_at
                        )
                    ) {

                        $sla->update([

                            'is_resolution_breached' => 1,

                            'resolution_breached_at' => now(),

                        ]);

                        return true;
                    }

                    return false;
                }

        public function pause(IssueSla $sla): IssueSla {

                if ($sla->is_paused) {

                    return $sla;

                }

                $sla->update([

                    'is_paused' => 1,

                    'paused_at' => now(),

                ]);

                return $sla;
            }

        public function resume(IssueSla $sla): IssueSla {

                if (!$sla->is_paused) {

                    return $sla;

                }

                $pausedMinutes =
                    $sla->paused_at
                        ->diffInMinutes(now());

            $sla->update([

                'is_paused' => 0,

                'paused_at' => null,

                'total_paused_minutes' =>
                    $sla->total_paused_minutes
                    + $pausedMinutes,

                'response_due_at' =>
                    $sla->response_due_at
                        ->addMinutes($pausedMinutes),

                'resolution_due_at' =>
                    $sla->resolution_due_at
                        ->addMinutes($pausedMinutes),

            ]);

            return $sla;
        }


    protected function calculateWarningAt(Carbon $startAt,int $slaMinutes,float $warningPercent,WorkingCalendar $calendar): Carbon {

    $warningMinutes = (int) round(
        $slaMinutes * ($warningPercent / 100)
    );

    return $this->slaCalculator->addBusinessMinutes(
        $startAt,
        $warningMinutes,
        $calendar
    );
}


        
}