<?php

namespace App\Services;

use App\Interfaces\IssueRepositoryInterface;
use App\Models\Issue;
use App\Models\IssueAttachment;
use App\Models\WorkingCalendar;
use App\Models\WorkingSchedule;
use App\Services\WorkingCalendarEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Carbon\Carbon;

use Illuminate\Validation\ValidationException;

class IssueService
{
    /**
     * Repository Instance
     */
    protected IssueRepositoryInterface $repository;

    /**
     * Constructor
     */
    public function __construct(
        IssueRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    /**
     * Get Paginated Issues
     */
    public function paginate(
        array $filters = [],
        int $perPage = 15
    ) {
        return $this->repository->paginate(
            $filters,
            $perPage
        );
    }

    /**
     * Find Issue
     */
    public function find(int $id): Issue
    {
        return $this->repository->findOrFail($id);
    }

    /**
     * Create New Issue
     */
    public function create(array $data, ?UploadedFile $attachment = null): Issue
    {
        Log::info('=== ISSUE CREATION STARTED ===', ['route' => 'issue/create', 'user_id' => Auth::id()]);
        Log::channel('insert_log')->info('═══════════════════════════════════════════════════════════════');
        Log::channel('insert_log')->info('[ROUTE] issue/create request started', [
            'user_id' => Auth::id(),
            'project_id' => $data['project_id'] ?? null,
            'state_id' => $data['state_id'] ?? null,
            'application_id' => $data['application_id'] ?? null,
            'subject' => $data['subject'] ?? null,
            'timestamp' => now(),
        ]);
        
        DB::beginTransaction();
        Log::info('[DB] Transaction started', ['timestamp' => now()]);
        Log::channel('insert_log')->info('[DB] Transaction started for issue/create', ['timestamp' => now()]);

        try {
            Log::info('[STEP 1] Preparing issue data...');
            $payload = $this->prepareCreateData($data);
            Log::info('Issue Payload', $payload);

            Log::info('[STEP 2] Inserting into TABLE: issues', [
                'ticket_number' => $payload['issue_number'] ?? null,
                'title' => substr($payload['issue_title'] ?? '', 0, 50),
                'status_id' => $payload['status_id'] ?? null
            ]);
            $issue = $this->repository->create($payload);
            Log::info('[TABLE: issues] Row created successfully', ['issue_id' => $issue->issue_id, 'issue_number' => $issue->issue_number]);

            if ($attachment) {
                Log::info('[STEP 3] Uploading attachment...');
                Log::info('[TABLE: txn_issue_attachment] About to insert attachment', ['filename' => $attachment->getClientOriginalName()]);
                $this->uploadAttachment($attachment, $issue);
            }

            Log::info('[STEP 4] Running afterCreate hooks...');
            $this->afterCreate($issue);

            Log::info('[DB] Committing transaction...', ['timestamp' => now()]);
            DB::commit();
            Log::info('=== ISSUE CREATION COMPLETED ===', ['issue_id' => $issue->issue_id, 'issue_number' => $issue->issue_number]);

            return $issue;

        } catch (\Throwable $e) {

            Log::error('[DB] Rolling back transaction due to error...', ['error_message' => $e->getMessage()]);
            DB::rollBack();

            Log::error(
                '❌ Issue Create Error',
                [
                    'message' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile(),
                    'timestamp' => now()
                ]
            );
            Log::error('=== ISSUE CREATION FAILED ===');

            throw $e;
        }
    }

    /**
     * Update Issue
     */
    public function update(
        Request $request,
        Issue $issue
    ): Issue {

        DB::beginTransaction();

        try {

            $data = $this->prepareUpdateData(
                $request,
                $issue
            );

            $this->repository->update(
                $issue,
                $data
            );

            $issue = $this->repository->findOrFail(
                $issue->id
            );

            DB::commit();

            return $issue;

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error(
                'Issue Update Error',
                [
                    'message' => $e->getMessage()
                ]
            );

            throw $e;
        }
    }

    /**
     * Delete Issue
     */
    public function delete(Issue $issue): bool
    {
        DB::beginTransaction();

        try {

            $this->removeAttachment($issue);

            $status = $this->repository
                ->delete($issue);

            DB::commit();

            return $status;

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error(
                'Issue Delete Error',
                [
                    'message' => $e->getMessage()
                ]
            );

            throw $e;
        }
    }

    /**
     * Prepare Create Data
     */

    protected function prepareCreateData(array $data): array
    {
        $routingData = $this->resolveRoutingMetadata($data);

        return [
            'issue_number' => $this->generateTicketNumber(),
            'state_id' => $data['state_id'] ?? null,
            'project_id' => $data['project_id'] ?? null,
            'application_id' => $data['application_id'] ?? null,
            'module_id' => $data['module_id'] ?? null,
            'issue_category_id' => $data['issue_category_id'] ?? null,
            'priority_id' => $data['priority_id'] ?? null,
            'issue_title' => trim((string) ($data['subject'] ?? '')),
            'issue_description' => trim((string) ($data['description'] ?? '')),
            'status_id' => $data['status_id'] ?? 1,
            'raised_by_user_id' => Auth::id(),
            'occurred_date' => $data['occurred_date'] ?? null,
            'occurred_time' => $data['occurred_time'] ?? null,
            'affected_users' => $data['affected_users'] ?? null,
            'created_at' => now(),
            'updated_at' => now(),
            'ho_intervention_required' => $routingData['ho_intervention_required'],
            'ho_working_hours' => $routingData['ho_working_hours'],
            'first_level_vendor_ids' => $routingData['first_level_vendor_ids'],
            'second_level_vendor_ids' => $routingData['second_level_vendor_ids'],
            'current_stage' => $routingData['current_stage'],
            'current_owner_type' => $routingData['current_owner_type'],
            'current_owner_id' => $routingData['current_owner_id'],
            'workflow_status' => $routingData['workflow_status'],
        ];
    }

    protected function resolveRoutingMetadata(array $data): array
    {
        Log::info('═══════════════════════════════════════════════════════════════');
        Log::info('RESOLVING ROUTING METADATA - Spec-Based Routing Logic');
        Log::info('═══════════════════════════════════════════════════════════════');
        Log::channel('insert_log')->info('═══════════════════════════════════════════════════════════════');
        Log::channel('insert_log')->info('[ROUTING CHECK] Starting metadata resolution before insert', [
            'project_id' => $data['project_id'] ?? null,
            'state_id' => $data['state_id'] ?? null,
            'application_id' => $data['application_id'] ?? null,
            'occurred_date' => $data['occurred_date'] ?? null,
            'occurred_time' => $data['occurred_time'] ?? null,
        ]);
        
        $projectId = isset($data['project_id']) ? (int) $data['project_id'] : null;
        $stateId = isset($data['state_id']) ? (int) $data['state_id'] : null;
        $applicationId = isset($data['application_id']) ? (int) $data['application_id'] : null;

        Log::info('[STEP 1] INPUT PARAMETERS', [
            'project_id' => $projectId,
            'state_id' => $stateId,
            'application_id' => $applicationId
        ]);
        Log::channel('insert_log')->info('[STEP 1] INPUT PARAMETERS', [
            'project_id' => $projectId,
            'state_id' => $stateId,
            'application_id' => $applicationId,
        ]);

        // Determine the datetime for checks
        Log::info('[STEP 2] Determining DateTime for Checks...');
        $dateTime = null;
        if (!empty($data['occurred_date'])) {
            $time = $data['occurred_time'] ?? '00:00';
            try {
                $dateTime = Carbon::createFromFormat('Y-m-d H:i', $data['occurred_date'] . ' ' . $time);
                Log::info('[STEP 2 RESULT] Using provided occurred_date/time', ['datetime' => $dateTime->toDateTimeString()]);
            } catch (\Throwable $e) {
                $dateTime = Carbon::now();
                Log::info('[STEP 2 RESULT] Parse error, using current datetime', ['datetime' => $dateTime->toDateTimeString()]);
            }
        } else {
            $dateTime = Carbon::now();
            Log::info('[STEP 2 RESULT] No occurred_date, using current datetime', ['datetime' => $dateTime->toDateTimeString()]);
        }

        // STEP 3: Check mst_issue_routing_rule with rule_code priority
        Log::info('═══════════════════════════════════════════════════════════════');
        Log::info('[STEP 3] Checking mst_issue_routing_rule - Rule Priority Logic');
        Log::info('═══════════════════════════════════════════════════════════════');

        Log::channel('insert_log')->info('[TABLE: mst_issue_routing_rule] Checking rule_code values', [
            'ROUTE_HO_IT_L1' => 'active status check',
            'ROUTE_VENDOR_L2' => 'active status check',
            'is_active' => 1,
        ]);

        $routeHoL1Active = DB::table('mst_issue_routing_rule')
            ->where('rule_code', 'ROUTE_HO_IT_L1')
            ->where('is_active', 1)
            ->exists();
            
        $routeVendorL2Active = DB::table('mst_issue_routing_rule')
            ->where('rule_code', 'ROUTE_VENDOR_L2')
            ->where('is_active', 1)
            ->exists();

        Log::info('[STEP 3 RESULT] Routing Rules Status', [
            'ROUTE_HO_IT_L1' => $routeHoL1Active ? 'ACTIVE' : 'INACTIVE',
            'ROUTE_VENDOR_L2' => $routeVendorL2Active ? 'ACTIVE' : 'INACTIVE'
        ]);
        Log::channel('insert_log')->info('[TABLE: mst_issue_routing_rule] Rule check result', [
            'ROUTE_HO_IT_L1' => $routeHoL1Active ? 'ACTIVE' : 'INACTIVE',
            'ROUTE_VENDOR_L2' => $routeVendorL2Active ? 'ACTIVE' : 'INACTIVE',
        ]);

        // Priority: ROUTE_HO_IT_L1 takes precedence
        if ($routeHoL1Active) {
            Log::info('[ROUTING PRIORITY] ROUTE_HO_IT_L1 is ACTIVE - Using HO Routing Flow');
            return $this->routeHOITL1Flow($projectId, $stateId, $applicationId, $dateTime);
        } elseif ($routeVendorL2Active) {
            Log::info('[ROUTING PRIORITY] ROUTE_VENDOR_L2 is ACTIVE - Using Direct Vendor L2 Flow');
            return $this->routeDirectVendorL2Flow($projectId, $stateId, $applicationId);
        } else {
            Log::info('[ROUTING PRIORITY] No active routing rules - Using fallback (Direct Vendor L2)');
            return $this->routeDirectVendorL2Flow($projectId, $stateId, $applicationId);
        }
    }

    /**
     * SCENARIO 1: Direct Vendor L2 Routing
     * Condition: ROUTE_HO_IT_L1 = 0, ROUTE_VENDOR_L2 = 1
     */
    private function routeDirectVendorL2Flow(?int $projectId, ?int $stateId, ?int $applicationId): array
    {
        Log::info('═══════════════════════════════════════════════════════════════');
        Log::info('[SCENARIO 1] Direct Vendor L2 Routing');
        Log::info('═══════════════════════════════════════════════════════════════');
        
        Log::info('[S1-STEP 1] Skip holiday and working hours checks');
        Log::info('[S1-STEP 2] Directly query map_vendor_state for active vendors');
        Log::channel('insert_log')->info('[TABLE: map_vendor_state] Scenario 1 vendor lookup', [
            'project_id' => $projectId,
            'state_id' => $stateId,
            'application_id' => $applicationId,
            'is_active' => 1,
        ]);
        
        $vendorIds = [];
        if ($projectId && $stateId) {
            Log::info('[TABLE: map_vendor_state] Query with conditions:', [
                'project_id' => $projectId,
                'state_id' => $stateId,
                'application_id' => $applicationId,
                'is_active' => 1
            ]);
            
            $vendorIds = DB::table('map_vendor_state')
                ->where('project_id', $projectId)
                ->where('state_id', $stateId)
                ->where('is_active', 1)
                ->when($applicationId, function($q) use ($applicationId) {
                    $q->where(function($sq) use ($applicationId) {
                        $sq->whereNull('application_id')->orWhere('application_id', $applicationId);
                    });
                }, function($q) {
                    $q->whereNull('application_id');
                })
                ->distinct()
                ->orderBy('vendor_id')
                ->pluck('vendor_id')
                ->map(fn($v) => (int)$v)
                ->all();
                
            Log::info('[S1-STEP 2 RESULT] Vendors found from map_vendor_state', [
                'vendor_ids' => $vendorIds,
                'count' => count($vendorIds)
            ]);
        }

        Log::info('[S1 FINAL RESULT]', [
            'ho_intervention_required' => 0,
            'ho_working_hours' => 0,
            'first_level_vendor_ids' => implode(',', $vendorIds),
            'second_level_vendor_ids' => null
        ]);

        return [
            'ho_intervention_required' => 0,
            'ho_working_hours' => 0,
            'first_level_vendor_ids' => $this->formatVendorIds($vendorIds),
            'second_level_vendor_ids' => null,
            'current_stage' => 'ISSUE_RAISED',
            'current_owner_type' => 0,
            'current_owner_id' => null,
            'workflow_status' => 0,
        ];
    }

    /**
     * SCENARIO 2: HO L1 Routing with Holiday/Working Hours Checks
     * Condition: ROUTE_HO_IT_L1 = 1
     */
    private function routeHOITL1Flow(?int $projectId, ?int $stateId, ?int $applicationId, $dateTime): array
    {
        Log::info('═══════════════════════════════════════════════════════════════');
        Log::info('[SCENARIO 2] HO L1 Routing - Holiday & Working Hours Checks');
        Log::info('═══════════════════════════════════════════════════════════════');

        // SCENARIO 2.1: Check Holiday First
        Log::info('[S2-STEP 1] Checking mst_calendar_holiday for current date');
        Log::channel('insert_log')->info('[TABLE: mst_calendar_holiday] Checking holiday date before insert', [
            'holiday_date' => $dateTime->format('Y-m-d'),
            'is_active' => 1,
        ]);
        $isHoliday = DB::table('mst_calendar_holiday')
            ->where('holiday_date', $dateTime->format('Y-m-d'))
            ->where('is_active', 1)
            ->exists();

        Log::info('[S2-STEP 1 RESULT] Holiday Check', [
            'date_checked' => $dateTime->format('Y-m-d'),
            'is_holiday' => $isHoliday ? 'YES - Holiday Found' : 'NO - Not a Holiday'
        ]);

        if ($isHoliday) {
            return $this->routeHOHolidayScenario($projectId, $stateId, $applicationId);
        }

        // SCENARIO 2.2, 2.3, 2.4: Check Working Schedule
        Log::info('[S2-STEP 2] Holiday NOT found - Checking mst_working_schedule');
        Log::channel('insert_log')->info('[TABLE: mst_working_schedule] Holiday not found, checking work schedule before insert', [
            'day_of_week' => strtoupper($dateTime->format('l')),
            'date' => $dateTime->format('Y-m-d'),
        ]);
        return $this->routeHOWorkingScheduleCheck($projectId, $stateId, $applicationId, $dateTime);
    }

    /**
     * SCENARIO 2.1: Holiday Found - Route to Vendor L2
     */
    private function routeHOHolidayScenario(?int $projectId, ?int $stateId, ?int $applicationId): array
    {
        Log::info('═══════════════════════════════════════════════════════════════');
        Log::info('[SCENARIO 2.1] Holiday Found - Route to Vendor L2');
        Log::info('═══════════════════════════════════════════════════════════════');

        $vendorIds = [];
        if ($projectId && $stateId) {
            Log::info('[S2.1] Query map_vendor_state for Vendor L2 assignment');
            
            $vendorIds = DB::table('map_vendor_state')
                ->where('project_id', $projectId)
                ->where('state_id', $stateId)
                ->where('is_active', 1)
                ->when($applicationId, function($q) use ($applicationId) {
                    $q->where(function($sq) use ($applicationId) {
                        $sq->whereNull('application_id')->orWhere('application_id', $applicationId);
                    });
                }, function($q) {
                    $q->whereNull('application_id');
                })
                ->distinct()
                ->orderBy('vendor_id')
                ->pluck('vendor_id')
                ->map(fn($v) => (int)$v)
                ->all();

            Log::info('[S2.1 RESULT] Vendors for L2', ['vendor_ids' => $vendorIds]);
        }

        Log::info('[S2.1 FINAL RESULT]', [
            'ho_intervention_required' => 1,
            'ho_working_hours' => 0,
            'reason' => 'Holiday - HO Unavailable'
        ]);

        return [
            'ho_intervention_required' => 1,
            'ho_working_hours' => 0,
            'first_level_vendor_ids' => null,
            'second_level_vendor_ids' => $this->formatVendorIds($vendorIds),
            'current_stage' => 'ISSUE_RAISED',
            'current_owner_type' => 0,
            'current_owner_id' => null,
            'workflow_status' => 0,
        ];
    }

    /**
     * Check Working Schedule - Routes to SCENARIO 2.2, 2.3, or 2.4
     */
    private function routeHOWorkingScheduleCheck(?int $projectId, ?int $stateId, ?int $applicationId, $dateTime): array
    {
        Log::info('[S2-STEP 2.1] Getting day of week from ticket datetime');
        $dayOfWeekNumber = (int) $dateTime->format('N'); // 1 = Monday, 7 = Sunday
        $dayOfWeek = strtoupper($dateTime->format('l'));
        $currentTime = $dateTime->format('H:i:s');

        Log::info('[S2-STEP 2.1 RESULT]', [
            'day_of_week' => $dayOfWeek,
            'day_of_week_number' => $dayOfWeekNumber,
            'current_time' => $currentTime
        ]);

        Log::info('[TABLE: mst_working_schedule] Query with conditions:', [
            'day_of_week' => $dayOfWeek,
            'is_active' => 1
        ]);
        Log::channel('insert_log')->info('[TABLE: mst_working_schedule] Query with conditions', [
            'day_of_week' => $dayOfWeek,
            'is_active' => 1,
            'current_time' => $currentTime,
        ]);

        $schedule = DB::table('mst_working_schedule')
            ->where('day_of_week', $dayOfWeekNumber)
            ->where('is_active', 1)
            ->where(function ($query) use ($dateTime) {
                $query->whereNull('effective_from')
                    ->orWhereDate('effective_from', '<=', $dateTime->toDateString());
            })
            ->where(function ($query) use ($dateTime) {
                $query->whereNull('effective_to')
                    ->orWhereDate('effective_to', '>=', $dateTime->toDateString());
            })
            ->orderBy('sequence_no')
            ->first();

        if (!$schedule) {
            Log::info('[S2-STEP 2.2 RESULT] No schedule found for day - Defaulting to off');
            return $this->routeHOWeeklyOffScenario($projectId, $stateId, $applicationId);
        }

        Log::info('[S2-STEP 2.2] Schedule Found', [
            'schedule_name' => $schedule->schedule_name,
            'is_working_day' => $schedule->is_working_day,
            'start_time' => $schedule->start_time ?? 'N/A',
            'end_time' => $schedule->end_time ?? 'N/A'
        ]);

        // SCENARIO 2.3: Weekly Off (is_working_day = 0)
        if (!$schedule->is_working_day) {
            Log::info('[SCENARIO 2.3 DETECTED] Weekly Off - is_working_day = 0');
            return $this->routeHOWeeklyOffScenario($projectId, $stateId, $applicationId);
        }

        // SCENARIO 2.2 or 2.4: Check if within working hours
        $startTime = $schedule->start_time;
        $endTime = $schedule->end_time;

        Log::info('[S2-STEP 2.3] Comparing current time with working hours', [
            'current_time' => $currentTime,
            'start_time' => $startTime,
            'end_time' => $endTime
        ]);

        // Convert to comparable format
        $currentTimeObj = Carbon::createFromFormat('H:i:s', $currentTime);
        $startTimeObj = Carbon::createFromFormat('H:i:s', $startTime);
        $endTimeObj = Carbon::createFromFormat('H:i:s', $endTime);

        if ($currentTimeObj->isBetween($startTimeObj, $endTimeObj)) {
            // SCENARIO 2.2: Within Working Hours
            Log::info('[SCENARIO 2.2 DETECTED] Within Working Hours');
            return $this->routeHOWorkingHoursScenario($projectId, $stateId, $applicationId);
        } else {
            // SCENARIO 2.4: Outside Working Hours
            Log::info('[SCENARIO 2.4 DETECTED] Outside Working Hours');
            return $this->routeHOOutsideHoursScenario($projectId, $stateId, $applicationId);
        }
    }

    /**
     * SCENARIO 2.2: Within Working Hours - HO Handles
     */
    private function routeHOWorkingHoursScenario(?int $projectId, ?int $stateId, ?int $applicationId): array
    {
        Log::info('═══════════════════════════════════════════════════════════════');
        Log::info('[SCENARIO 2.2] Within Working Hours - HO Handles');
        Log::info('═══════════════════════════════════════════════════════════════');

        Log::info('[S2.2] Get configured first-level vendor IDs from routing rule');
        $firstLevelVendors = [];
        
        $rule = DB::table('mst_issue_routing_rule')
            ->where('rule_code', 'ROUTE_HO_IT_L1')
            ->where('is_active', 1)
            ->first();

        if ($rule && !empty($rule->first_level_vendor_ids)) {
            $vendorStr = $rule->first_level_vendor_ids;
            $firstLevelVendors = array_map('trim', explode(',', $vendorStr));
            Log::info('[S2.2 RESULT] First-level vendors from rule', ['vendor_ids' => $firstLevelVendors]);
        }

        Log::info('[S2.2 FINAL RESULT]', [
            'ho_intervention_required' => 1,
            'ho_working_hours' => 1,
            'first_level_vendor_ids' => implode(',', $firstLevelVendors),
            'reason' => 'Within working hours - HO will handle'
        ]);

        return [
            'ho_intervention_required' => 1,
            'ho_working_hours' => 1,
            'first_level_vendor_ids' => $this->formatVendorIds($firstLevelVendors),
            'second_level_vendor_ids' => null,
            'current_stage' => 'ISSUE_RAISED',
            'current_owner_type' => 0,
            'current_owner_id' => null,
            'workflow_status' => 0,
        ];
    }

    /**
     * SCENARIO 2.3: Weekly Off - Route to Vendor L2
     */
    private function routeHOWeeklyOffScenario(?int $projectId, ?int $stateId, ?int $applicationId): array
    {
        Log::info('═══════════════════════════════════════════════════════════════');
        Log::info('[SCENARIO 2.3] Weekly Off - Route to Vendor L2');
        Log::info('═══════════════════════════════════════════════════════════════');

        $vendorIds = [];
        if ($projectId && $stateId) {
            Log::info('[S2.3] Query map_vendor_state for Vendor L2 assignment');
            
            $vendorIds = DB::table('map_vendor_state')
                ->where('project_id', $projectId)
                ->where('state_id', $stateId)
                ->where('is_active', 1)
                ->when($applicationId, function($q) use ($applicationId) {
                    $q->where(function($sq) use ($applicationId) {
                        $sq->whereNull('application_id')->orWhere('application_id', $applicationId);
                    });
                }, function($q) {
                    $q->whereNull('application_id');
                })
                ->distinct()
                ->orderBy('vendor_id')
                ->pluck('vendor_id')
                ->map(fn($v) => (int)$v)
                ->all();

            Log::info('[S2.3 RESULT] Vendors for L2', ['vendor_ids' => $vendorIds]);
        }

        Log::info('[S2.3 FINAL RESULT]', [
            'ho_intervention_required' => 1,
            'ho_working_hours' => 0,
            'reason' => 'Weekly Off - HO Unavailable'
        ]);

        return [
            'ho_intervention_required' => 1,
            'ho_working_hours' => 0,
            'first_level_vendor_ids' => null,
            'second_level_vendor_ids' => $this->formatVendorIds($vendorIds),
            'current_stage' => 'ISSUE_RAISED',
            'current_owner_type' => 0,
            'current_owner_id' => null,
            'workflow_status' => 0,
        ];
    }

    /**
     * SCENARIO 2.4: Outside Working Hours - Route to Vendor L2
     */
    private function routeHOOutsideHoursScenario(?int $projectId, ?int $stateId, ?int $applicationId): array
    {
        Log::info('═══════════════════════════════════════════════════════════════');
        Log::info('[SCENARIO 2.4] Outside Working Hours - Route to Vendor L2');
        Log::info('═══════════════════════════════════════════════════════════════');

        $vendorIds = [];
        if ($projectId && $stateId) {
            Log::info('[S2.4] Query map_vendor_state for Vendor L2 assignment');
            
            $vendorIds = DB::table('map_vendor_state')
                ->where('project_id', $projectId)
                ->where('state_id', $stateId)
                ->where('is_active', 1)
                ->when($applicationId, function($q) use ($applicationId) {
                    $q->where(function($sq) use ($applicationId) {
                        $sq->whereNull('application_id')->orWhere('application_id', $applicationId);
                    });
                }, function($q) {
                    $q->whereNull('application_id');
                })
                ->distinct()
                ->orderBy('vendor_id')
                ->pluck('vendor_id')
                ->map(fn($v) => (int)$v)
                ->all();

            Log::info('[S2.4 RESULT] Vendors for L2', ['vendor_ids' => $vendorIds]);
        }

        Log::info('[S2.4 FINAL RESULT]', [
            'ho_intervention_required' => 1,
            'ho_working_hours' => 0,
            'reason' => 'Outside working hours'
        ]);

        return [
            'ho_intervention_required' => 1,
            'ho_working_hours' => 0,
            'first_level_vendor_ids' => null,
            'second_level_vendor_ids' => $this->formatVendorIds($vendorIds),
            'current_stage' => 'ISSUE_RAISED',
            'current_owner_type' => 0,
            'current_owner_id' => null,
            'workflow_status' => 0,
        ];
    }

    protected function findVendorIds(?int $projectId, ?int $stateId, ?int $applicationId): array
    {
        Log::info('═══════════════════════════════════════════════════════════════');
        Log::info('[findVendorIds] Looking up vendors from map_vendor_state');
        Log::info('═══════════════════════════════════════════════════════════════');
        
        if (! $projectId || ! $stateId) {
            Log::info('[findVendorIds] Missing required parameters', [
                'has_project_id' => !empty($projectId),
                'has_state_id' => !empty($stateId)
            ]);
            Log::info('[findVendorIds] Returning empty vendor list');
            return [];
        }

        Log::info('[TABLE: map_vendor_state] Building query with conditions:', [
            'project_id' => $projectId,
            'state_id' => $stateId,
            'is_active' => 1,
            'application_id' => $applicationId ? 'NULL or ' . $applicationId : 'NULL only'
        ]);

        $query = DB::table('map_vendor_state')
            ->where('project_id', $projectId)
            ->where('state_id', $stateId)
            ->where('is_active', 1)
            ->when($applicationId, function ($query, $applicationId) {
                Log::info('[TABLE: map_vendor_state] Applying application_id filter', ['app_id' => $applicationId]);
                $query->where(function ($subQuery) use ($applicationId) {
                    $subQuery->whereNull('application_id')
                        ->orWhere('application_id', $applicationId);
                });
            }, function ($query) {
                Log::info('[TABLE: map_vendor_state] Filtering for NULL application_id only');
                $query->whereNull('application_id');
            });

        $vendorIds = $query
            ->distinct()
            ->orderBy('vendor_id')
            ->pluck('vendor_id')
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->values()
            ->all();
            
        Log::info('[TABLE: map_vendor_state] Query executed successfully', [
            'found_vendors' => $vendorIds,
            'total_count' => count($vendorIds)
        ]);
        
        return $vendorIds;
    }

    protected function formatVendorIds(array $ids): ?string
    {
        $ids = array_filter(array_map('intval', $ids), fn ($id) => $id > 0);

        return empty($ids) ? null : implode(',', array_unique($ids));
    }

    /**
     * Prepare Update Data
     */
    protected function prepareUpdateData(
        Request $request,
        Issue $issue
    ): array {

        $attachment = $issue->attachment;

        if ($request->hasFile('attachment')) {

            $this->removeAttachment($issue);

            $attachment = $this->uploadAttachment($request);
        }

        return [

            'state_id'
                => $request->state_id,

            // service_id intentionally omitted from create/update payloads

            'project_id'
                => $request->project_id,

            'application_id'
                => $request->application_id,

            'module_id'
                => $request->module_id,

            'issue_category_id'
                => $request->issue_category_id,

            'priority_id'
                => $request->priority_id,

            'subject'
                => trim($request->subject),

            'description'
                => trim($request->description),

            'occurred_date'
                => $request->occurred_date,

            'occurred_time'
                => $request->occurred_time,

            'affected_users'
                => $request->affected_users,

            'attachment'
                => $attachment,

            'updated_by'
                => Auth::id(),

            'updated_at'
                => now()

        ];
    }

    /**
     * Generate Ticket Number
     */
    protected function generateTicketNumber(): string
    {
        // $last = $this->repository->latest();

        // $next = $last? ($last->issue_id + 1): 1;

        // return sprintf('ISSUE-%s-%06d',date('Y'),$next);

        $today = date('Ymd');

        $last = $this->repository->latest();

        $next = $last ? ($last->issue_id + 1) : 1;

        return sprintf('IS-%s%03d', $today, $next);

    }

    /**
     * Upload Attachment
     */
    protected function uploadAttachment(?UploadedFile $file, Issue $issue): ?string {

        if (!$file) {
            Log::info('[TABLE: txn_issue_attachment] Skipped - No attachment received');
            return null;
        }

        Log::info('[TABLE: txn_issue_attachment] Processing attachment', [
            'issue_id' => $issue->issue_id,
            'filename' => $file->getClientOriginalName(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType()
        ]);
    
        Log::info('[STEP 3.1] Storing file to disk...');
        
        try {
            // Ensure directory exists
            $storeDir = 'issues/' . $issue->issue_id;
            $path = $file->store($storeDir, 'public');
            
            if (!$path) {
                throw new \Exception('File store returned empty path');
            }
            
            Log::info('[STEP 3.1 COMPLETE] File stored successfully', [
                'path' => $path,
                'full_path' => storage_path('app/public/' . $path)
            ]);

            Log::channel('insert_log')->info('═══════════════════════════════════════════════════════════════');
            Log::channel('insert_log')->info('[INSERT] Starting txn_issue_attachment INSERT operation');
            Log::channel('insert_log')->info('═══════════════════════════════════════════════════════════════');
            
            Log::channel('insert_log')->info('[TABLE: txn_issue_attachment] Preparing INSERT statement', [
                'issue_id' => $issue->issue_id,
                'user_id' => auth()->id() ?? 1,
                'original_file_name' => $file->getClientOriginalName(),
                'stored_file_name' => basename($path),
                'file_path' => '/storage/' . $path,
                'file_size' => $file->getSize(),
                'file_type' => $file->getMimeType(),
                'uploaded_at' => now()
            ]);
            
            Log::channel('insert_log')->info('[TABLE: txn_issue_attachment] Executing INSERT query');
            
            $attachment = IssueAttachment::create([
                'issue_id' => $issue->issue_id,
                'user_id' => auth()->id() ?? 1,
                'original_file_name' => $file->getClientOriginalName(),
                'stored_file_name' => basename($path),
                'file_path' => '/storage/' . $path,
                'file_size' => $file->getSize(),
                'file_type' => $file->getMimeType(),
                'uploaded_at' => now(),
                'is_active' => 1,
            ]); 

            Log::channel('insert_log')->info('✓ [TABLE: txn_issue_attachment] INSERT Successful', [
                'attachment_id' => $attachment->attachment_id,
                'file_path' => '/storage/' . $path,
                'file_size' => $file->getSize()
            ]);
            
            return $path;
            
        } catch (\Throwable $e) {
            Log::error('✗ [TABLE: txn_issue_attachment] File upload FAILED', [
                'issue_id' => $issue->issue_id,
                'file_name' => $file->getClientOriginalName(),
                'error' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            
            Log::channel('insert_log')->error('✗ [TABLE: txn_issue_attachment] INSERT Failed', [
                'issue_id' => $issue->issue_id,
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName()
            ]);
            
            return null;
        }
    }

    /**
     * After Create Process
     */
    protected function afterCreate(Issue $issue): void
    {
        Log::info('[STEP 4.1] Assigning engineer...');
        $this->assignEngineer($issue);
        Log::info('[STEP 4.1 COMPLETE] Engineer assigned', ['assigned_to' => $issue->assigned_to]);

        Log::info('[STEP 4.2] Creating issue history record...');
        Log::info('[TABLE: issue_history] About to insert history entry', ['action' => 'Issue Created']);
        $this->createHistory(
            $issue,
            'Issue Created',
            'Issue created successfully.'
        );
        Log::info('[TABLE: issue_history] History record created');

        Log::info('[STEP 4.3] Creating initial status history...');
        $this->createStatusHistory($issue, $issue->status_id, 'Initial status');
        Log::info('[STEP 4.3 COMPLETE] Initial status history created');

        Log::info('[STEP 4.4] Assigning vendors during creation...');
        $this->assignVendorsDuringCreation($issue);
        Log::info('[STEP 4.4 COMPLETE] Vendors assigned');

        Log::info('[STEP 4.5] Sending assignment notification...');
        $this->sendAssignmentNotification($issue);
        Log::info('[STEP 4.5 COMPLETE] Notification sent');
    }

    /**
     * Auto Assign Engineer
     */
    protected function assignEngineer(Issue $issue): void
    {
        Log::info('[TABLE: users] Querying for Support Engineer...');
        $engineer = \App\Models\User::query()
            ->where('is_active', 1)
            ->whereHas('roles', function ($query) {
                $query->where('role_name', 'Support Engineer');
            })
            ->first();

        if (!$engineer) {
            Log::info('[TABLE: users] No Support Engineer found for auto-assignment');
            return;
        }

        Log::info('[TABLE: issues] Updating issue assigned_to field', ['engineer_id' => $engineer->user_id, 'engineer_name' => $engineer->user_name]);
        $this->repository->assign($issue, $engineer->user_id);
        Log::info('[TABLE: issues] Assignment complete');

        Log::info('[TABLE: issue_history] Creating assignment history', ['engineer_name' => $engineer->user_name]);
        $this->createHistory(
            $issue,
            'Assigned',
            'Assigned to '.$engineer->user_name
        );
    }

    /**
     * Assign Vendors During Issue Creation
     * Extracts vendors from routing metadata and inserts them into map_issue_vendor_assignment
     */
    protected function assignVendorsDuringCreation(Issue $issue): void
    {
        Log::info('═══════════════════════════════════════════════════════════════');
        Log::info('[VENDOR ASSIGNMENT] Starting vendor assignment during creation');
        Log::info('═══════════════════════════════════════════════════════════════');

        if ((int) ($issue->ho_working_hours ?? 0) === 1) {
            Log::info('[VENDOR ASSIGNMENT] HO is working; no vendor assignment required');
            return;
        }

        // Extract vendor IDs from first_level_vendor_ids and second_level_vendor_ids
        $vendorIds = [];
        
        foreach (['first_level_vendor_ids', 'second_level_vendor_ids'] as $vendorField) {
            $vendorStr = trim((string) ($issue->{$vendorField} ?? ''));
            if (empty($vendorStr)) {
                continue;
            }

            $ids = array_map('trim', explode(',', $vendorStr));
            foreach ($ids as $id) {
                $vendorId = (int) $id;
                if ($vendorId > 0 && !in_array($vendorId, $vendorIds)) {
                    $vendorIds[] = $vendorId;
                }
            }
        }

        Log::info('[VENDOR ASSIGNMENT] Extracted vendor IDs', [
            'vendor_ids' => $vendorIds,
            'count' => count($vendorIds)
        ]);

        if (empty($vendorIds)) {
            Log::info('[VENDOR ASSIGNMENT] No vendors to assign - skipping vendor assignment');
            return;
        }

        // Get initial vendor status ID using Role -> Status mapping
        $initialVendorStatusId = $this->getInitialVendorStatusId();
        
        Log::info('[VENDOR ASSIGNMENT] Initial vendor status ID determined', [
            'status_id' => $initialVendorStatusId
        ]);

        if (!$initialVendorStatusId) {
            Log::warning('[VENDOR ASSIGNMENT] Could not determine initial vendor status - skipping vendor assignment');
            return;
        }

        // Insert each vendor into map_issue_vendor_assignment
        Log::channel('insert_log')->info('═══════════════════════════════════════════════════════════════');
        Log::channel('insert_log')->info('[INSERT] Starting map_issue_vendor_assignment INSERT operation');
        Log::channel('insert_log')->info('═══════════════════════════════════════════════════════════════');

        foreach ($vendorIds as $vendorId) {
            try {
                // Check if assignment already exists (shouldn't on creation, but be safe)
                $exists = DB::table('map_issue_vendor_assignment')
                    ->where('issue_id', $issue->issue_id)
                    ->where('vendor_id', $vendorId)
                    ->exists();

                if ($exists) {
                    Log::info('[VENDOR ASSIGNMENT] Vendor assignment already exists - skipping', [
                        'issue_id' => $issue->issue_id,
                        'vendor_id' => $vendorId
                    ]);
                    continue;
                }

                Log::channel('insert_log')->info('[TABLE: map_issue_vendor_assignment] Preparing INSERT statement', [
                    'issue_id' => $issue->issue_id,
                    'vendor_id' => $vendorId,
                    'vendor_status_id' => $initialVendorStatusId,
                    'is_active' => 1,
                    'created_by' => Auth::id(),
                    'created_at' => now()
                ]);

                DB::table('map_issue_vendor_assignment')->insert([
                    'issue_id' => $issue->issue_id,
                    'vendor_id' => $vendorId,
                    'vendor_status_id' => $initialVendorStatusId,
                    'is_active' => 1,
                    'created_by' => Auth::id(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                Log::channel('insert_log')->info('✓ [TABLE: map_issue_vendor_assignment] INSERT Successful', [
                    'issue_id' => $issue->issue_id,
                    'vendor_id' => $vendorId,
                    'status_id' => $initialVendorStatusId
                ]);

                Log::info('[VENDOR ASSIGNMENT] Vendor assigned successfully', [
                    'issue_id' => $issue->issue_id,
                    'vendor_id' => $vendorId,
                    'status_id' => $initialVendorStatusId
                ]);

            } catch (\Throwable $e) {
                Log::error('[VENDOR ASSIGNMENT] Failed to assign vendor', [
                    'issue_id' => $issue->issue_id,
                    'vendor_id' => $vendorId,
                    'error' => $e->getMessage(),
                    'line' => $e->getLine()
                ]);

                Log::channel('insert_log')->error('✗ [TABLE: map_issue_vendor_assignment] INSERT Failed', [
                    'issue_id' => $issue->issue_id,
                    'vendor_id' => $vendorId,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Get Initial Vendor Status ID
     * When a vendor is first assigned, status should be "New" (status_id = 1)
     * This ensures vendors always start with "New" status, not "Rejected"
     */
    protected function getInitialVendorStatusId(): ?int
    {
        Log::info('[INITIAL VENDOR STATUS] Using "New" status for vendor assignment');

        $newStatus = DB::table('mst_issue_status')
            ->where(function ($query) {
                $query->where('status_name', 'New')
                    ->orWhereRaw('LOWER(status_name) = ?', ['new']);
            })
            ->where(function ($query) {
                $query->where('is_active', 1)->orWhereNull('is_active');
            })
            ->first(['status_id', 'status_name']);

        if (!$newStatus) {
            $newStatus = DB::table('mst_issue_status')
                ->where('is_active', 1)
                ->orderBy('status_id')
                ->first(['status_id', 'status_name']);

            if (!$newStatus) {
                Log::warning('[INITIAL VENDOR STATUS] No active issue status found');
                return null;
            }

            Log::info('[INITIAL VENDOR STATUS] "New" status not found; using first active status', [
                'status_id' => $newStatus->status_id,
                'status_name' => $newStatus->status_name,
            ]);
        }

        Log::info('[INITIAL VENDOR STATUS] Using "New" status for initial vendor assignment', [
            'status_id' => $newStatus->status_id,
            'status_name' => $newStatus->status_name
        ]);

        return (int) $newStatus->status_id;
    }

    /**
     * Change Status
     */
    public function changeStatus(
        Issue $issue,
        string $status,
        ?string $remarks = null
    ): Issue {

        DB::beginTransaction();

        try {

            $this->validateStatusTransition(
                $issue->status,
                $status
            );

            $this->repository->updateStatus(
                $issue,
                $status
            );

            $issue = $this->repository->findOrFail(
                $issue->id
            );

            $this->createHistory(
                $issue,
                'Status Changed',
                ($remarks ?? '').
                " ({$status})"
            );

            DB::commit();

            return $issue;

        } catch (\Throwable $e) {

            DB::rollBack();

            throw $e;
        }
    }

    /**
     * Validate Workflow
     */
    protected function validateStatusTransition(
        string $current,
        string $next
    ): void {

        $workflow = [

            'Open' => [
                'Assigned',
                'Closed'
            ],

            'Assigned' => [
                'In Progress',
                'Closed'
            ],

            'In Progress' => [
                'Resolved',
                'Closed'
            ],

            'Resolved' => [
                'Closed',
                'Reopened'
            ],

            'Reopened' => [
                'Assigned',
                'In Progress'
            ],

        ];

        if (!isset($workflow[$current])) {

            return;

        }

        if (!in_array($next, $workflow[$current])) {

            throw new \Exception(
                "Invalid workflow transition from {$current} to {$next}"
            );

        }
    }

    /**
     * SLA Due Time
     */
    public function calculateSLA(
        Issue $issue
    ): Carbon {

        $priority = optional(
            $issue->priority
        )->priority_name;

        return match ($priority) {

            'Critical' =>
                $issue->created_at->copy()->addHours(2),

            'High' =>
                $issue->created_at->copy()->addHours(4),

            'Medium' =>
                $issue->created_at->copy()->addHours(8),

            'Low' =>
                $issue->created_at->copy()->addDay(),

            default =>
                $issue->created_at->copy()->addDay(),

        };
    }

    /**
     * SLA Breach
     */
    public function isSlaBreached(
        Issue $issue
    ): bool {

        return now()->greaterThan(
            $this->calculateSLA($issue)
        );

    }

    /**
     * Escalate
     */
    public function escalate(
        Issue $issue
    ): void {

        if (!$this->isSlaBreached($issue)) {

            return;

        }

        $this->createHistory(

            $issue,

            'Escalated',

            'SLA breached.'

        );

    }

    /**
     * Create History
     */
    protected function createHistory(
        Issue $issue,
        string $action,
        ?string $remarks = null
    ): void {
        Log::channel('insert_log')->info('═══════════════════════════════════════════════════════════════');
        Log::channel('insert_log')->info('[INSERT] Starting issue_history INSERT operation');
        Log::channel('insert_log')->info('═══════════════════════════════════════════════════════════════');
        
        Log::channel('insert_log')->info('[TABLE: issue_history] Preparing INSERT statement', [
            'issue_id' => $issue->issue_id,
            'action' => $action,
            'remarks' => substr($remarks ?? '', 0, 100),
            'performed_by' => Auth::id(),
            'performed_at' => now()
        ]);

        Log::channel('insert_log')->info('[TABLE: issue_history] Executing INSERT query');
        
        \App\Models\IssueHistory::create([

            'issue_id' => $issue->issue_id,

            'action' => $action,

            'remarks' => $remarks,

            'performed_by' => Auth::id(),

            'performed_at' => now()

        ]);
        Log::channel('insert_log')->info('✓ [TABLE: issue_history] INSERT Successful', [
            'issue_id' => $issue->issue_id,
            'action' => $action,
            'performed_by' => Auth::id()
        ]);

    }

    /**
     * Create Status History
     */
    protected function createStatusHistory(
        Issue $issue,
        ?int $statusId,
        ?string $comment = null,
        ?int $vendorId = null
    ): void {
        Log::channel('insert_log')->info('═══════════════════════════════════════════════════════════════');
        Log::channel('insert_log')->info('[INSERT] Starting txn_issue_status_history INSERT operation');
        Log::channel('insert_log')->info('═══════════════════════════════════════════════════════════════');
        
        Log::channel('insert_log')->info('[TABLE: txn_issue_status_history] Preparing INSERT statement', [
            'issue_id' => $issue->issue_id,
            'new_status_id' => $statusId,
            'vendor_id' => $vendorId,
            'changed_by_user_id' => Auth::id(),
            'comment' => $comment ?? 'Status updated',
            'changed_at' => now()
        ]);
        
        Log::channel('insert_log')->info('[TABLE: txn_issue_status_history] Executing INSERT query');
        
        try {
            $data = [
                'issue_id' => $issue->issue_id,
                'new_status_id' => $statusId,
                'changed_by_user_id' => Auth::id(),
                'comment' => $comment ?? 'Status updated',
                'changed_at' => now(),
            ];

            // Preserve vendor context using the existing schema.
            if ($vendorId !== null && Schema::hasColumn('txn_issue_status_history', 'vendor_id')) {
                $data['vendor_id'] = $vendorId;
            } elseif ($vendorId !== null) {
                $vendorName = DB::table('mst_vendor')
                    ->where('vendor_id', $vendorId)
                    ->value('vendor_name');
                $data['comment'] = trim(($vendorName ? $vendorName . ' - ' : '') . ($data['comment'] ?? 'Vendor status updated'));
            }

            DB::table('txn_issue_status_history')->insert($data);
            
            Log::channel('insert_log')->info('✓ [TABLE: txn_issue_status_history] INSERT Successful', [
                'issue_id' => $issue->issue_id,
                'status_id' => $statusId,
                'vendor_id' => $vendorId,
                'changed_by' => Auth::id()
            ]);
        } catch (\Throwable $e) {
            Log::channel('insert_log')->error('✗ [TABLE: txn_issue_status_history] INSERT Failed', [
                'issue_id' => $issue->issue_id,
                'error' => $e->getMessage()
            ]);
            Log::error('[TABLE: txn_issue_status_history] Status history creation failed', [
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ]);
        }
    }

    /**
     * Timeline
     */
    public function timeline(
        int $issueId
    )
    {
        return \App\Models\IssueHistory::query()
            ->where('issue_id', $issueId)
            ->with('user')
            ->latest('performed_at')
            ->get();
    }

    /**
     * Add Comment
     */
    public function addComment(
        Issue $issue,
        string $comment
    ): void {

        \App\Models\IssueComment::create([

            'issue_id' => $issue->id,

            'comment' => $comment,

            'created_by' => Auth::id()

        ]);

        $this->createHistory(

            $issue,

            'Comment Added',

            $comment

        );
    }

    /**
     * Send Notification
     */
    protected function sendAssignmentNotification(
        Issue $issue
    ): void {

        if (!$issue->assigned_to) {

            return;

        }

        $user = \App\Models\User::find(
            $issue->assigned_to
        );

        if (!$user) {

            return;

        }

        // Replace with Notification class
        // Notification::send($user,new IssueAssignedNotification($issue));

    }


        /**
     * Dashboard Statistics
     */
    public function dashboard(): array
    {
        return $this->repository->dashboard();
    }

    /**
     * Search Issues
     */
    public function search(string $keyword)
    {
        return $this->repository->search(
            trim($keyword)
        );
    }

    /**
     * Advanced Filter
     */
    public function filter(array $filters)
    {
        return $this->repository->filter($filters);
    }

    /**
     * Recent Issues
     */
    public function recent(int $limit = 10)
    {
        return $this->repository->recent($limit);
    }

    /**
     * My Created Issues
     */
    public function myIssues()
    {
        return $this->repository
            ->createdBy(Auth::id());
    }

    /**
     * Assigned To Me
     */
    public function assignedToMe()
    {
        return $this->repository
            ->assignedTo(Auth::id());
    }

    /**
     * Open Issues
     */
    public function openIssues()
    {
        return $this->repository
            ->open();
    }

    /**
     * Closed Issues
     */
    public function closedIssues()
    {
        return $this->repository
            ->closed();
    }

    /**
     * SLA Breached Issues
     */
    public function slaBreached()
    {
        return $this->repository
            ->slaBreached();
    }

    /**
     * Issue Summary
     */
    public function summary(): array
    {
        return [

            'total' =>
                $this->repository
                    ->all()
                    ->count(),

            'open' =>
                $this->repository
                    ->countByStatus('Open'),

            'assigned' =>
                $this->repository
                    ->countByStatus('Assigned'),

            'progress' =>
                $this->repository
                    ->countByStatus('In Progress'),

            'resolved' =>
                $this->repository
                    ->countByStatus('Resolved'),

            'closed' =>
                $this->repository
                    ->countByStatus('Closed'),

        ];
    }

    /**
     * Priority Summary
     */
    public function prioritySummary(): array
    {
        return [

            'critical' =>
                $this->repository
                    ->countByPriority(1),

            'high' =>
                $this->repository
                    ->countByPriority(2),

            'medium' =>
                $this->repository
                    ->countByPriority(3),

            'low' =>
                $this->repository
                    ->countByPriority(4),

        ];
    }

    /**
     * Project Summary
     */
    public function projectSummary(array $filters = [])
    {
        $issues = $this->repository
            ->filter($filters);

        return $issues
            ->groupBy('project_id')
            ->map(function ($items) {

                return [

                    'count' => $items->count(),

                    'project' => optional(
                        $items->first()->project
                    )->project_name

                ];

            })
            ->values();
    }

    /**
     * State Summary
     */
    public function stateSummary(array $filters = [])
    {
        $issues = $this->repository
            ->filter($filters);

        return $issues
            ->groupBy('state_id')
            ->map(function ($items) {

                return [

                    'count' => $items->count(),

                    'state' => optional(
                        $items->first()->state
                    )->state_name

                ];

            })
            ->values();
    }

    /**
     * Monthly Report
     */
    public function monthlyReport(
        int $year = null
    )
    {
        $year ??= now()->year;

        return Issue::query()

            ->selectRaw(
                'MONTH(created_at) month,
                 COUNT(*) total'
            )

            ->whereYear(
                'created_at',
                $year
            )

            ->groupByRaw(
                'MONTH(created_at)'
            )

            ->orderByRaw(
                'MONTH(created_at)'
            )

            ->get();
    }

    /**
     * Recent Activity
     */
    public function recentActivity(
        int $limit = 20
    )
    {
        return \App\Models\IssueHistory::query()

            ->with([
                'issue',
                'user'
            ])

            ->latest()

            ->take($limit)

            ->get();
    }

        /**
     * Resolve Issue
     */
    public function resolve(
        Issue $issue,
        ?string $remarks = null
    ): Issue {

        return $this->changeStatus(
            $issue,
            'Resolved',
            $remarks
        );

    }

    /**
     * Close Issue - With Vendor Resolution Validation
     */
    public function close(
        Issue $issue,
        ?string $remarks = null
    ): Issue {

        // Validate vendor resolution requirements before closing
        $this->validateVendorResolutionBeforeClose($issue);

        return $this->changeStatus(
            $issue,
            'Closed',
            $remarks
        );

    }

    /**
     * Reopen Issue
     */
    public function reopen(
        Issue $issue,
        ?string $remarks = null
    ): Issue {

        return $this->changeStatus(
            $issue,
            'Reopened',
            $remarks
        );

    }

    /**
     * Assign Issue
     */
    public function assign(
        Issue $issue,
        int $userId
    ): Issue {

        DB::beginTransaction();

        try {

            $this->repository->assign(
                $issue,
                $userId
            );

            $issue = $this->repository->findOrFail($issue->id);

            $this->createHistory(

                $issue,

                'Assigned',

                'Assigned to User ID : '.$userId

            );

            DB::commit();

            return $issue;

        } catch (\Throwable $e) {

            DB::rollBack();

            throw $e;

        }

    }

    /**
     * Validate Vendor Resolution Before Close
     * Ensures all active vendors have resolved status before allowing close
     */
    public function validateVendorResolutionBeforeClose(Issue $issue): void
    {
        Log::info('[VENDOR VALIDATION] Checking vendor resolution for close', [
            'issue_id' => $issue->issue_id
        ]);

        $vendorProgress = $this->getVendorProgress($issue);

        if (!$vendorProgress) {
            Log::info('[VENDOR VALIDATION] No active vendors - close allowed');
            return;
        }

        if ($vendorProgress['active_count'] === 0) {
            Log::info('[VENDOR VALIDATION] No active vendors - close allowed');
            return;
        }

        if ($vendorProgress['active_count'] !== $vendorProgress['resolved_count']) {
            Log::warning('[VENDOR VALIDATION] Not all vendors resolved - close blocked', [
                'active' => $vendorProgress['active_count'],
                'resolved' => $vendorProgress['resolved_count']
            ]);

            $pendingList = $this->getPendingVendorsList($issue);
            throw \Illuminate\Validation\ValidationException::withMessages([
                'vendor_resolution' => $this->formatPendingVendorsMessage($pendingList),
            ]);
        }

        Log::info('[VENDOR VALIDATION] All vendors resolved - close allowed');
    }

    /**
     * Update Vendor Status
     * Updates vendor assignment status and tracks in history
     */
    public function updateVendorStatus(
        Issue $issue,
        int $vendorId,
        int $newVendorStatusId,
        ?string $remarks = null
    ): array {
        Log::info('[VENDOR STATUS UPDATE] Starting vendor status update', [
            'issue_id' => $issue->issue_id,
            'vendor_id' => $vendorId,
            'new_status_id' => $newVendorStatusId
        ]);

        DB::beginTransaction();

        try {
            // Get vendor and new status names for logging
            $vendor = DB::table('mst_vendor')
                ->where('vendor_id', $vendorId)
                ->first(['vendor_name']);

            $statusRow = DB::table('mst_issue_status')
                ->where('status_id', $newVendorStatusId)
                ->first(['status_name', 'status_id']);

            $isResolved = (int) $newVendorStatusId === 3;  // 3 = Resolved status

            Log::info('[VENDOR STATUS UPDATE] Status details', [
                'vendor_name' => $vendor->vendor_name ?? 'Unknown',
                'status_name' => $statusRow->status_name ?? 'Unknown',
                'is_resolved' => $isResolved
            ]);

            // Check if this is a Reject status
            $isReject = str_contains(strtolower($statusRow->status_name ?? ''), 'reject');
            
            Log::info('[VENDOR STATUS UPDATE] Determining is_active flag', [
                'is_reject' => $isReject,
                'is_active' => !$isReject ? 1 : 0
            ]);

            // Update map_issue_vendor_assignment
            Log::channel('insert_log')->info('═══════════════════════════════════════════════════════════════');
            Log::channel('insert_log')->info('[UPDATE] Starting vendor status UPDATE operation', [
                'table' => 'map_issue_vendor_assignment',
                'issue_id' => $issue->issue_id,
                'vendor_id' => $vendorId
            ]);

            DB::table('map_issue_vendor_assignment')
                ->where('issue_id', $issue->issue_id)
                ->where('vendor_id', $vendorId)
                ->update([
                    'vendor_status_id' => $newVendorStatusId,
                    'status_updated_by' => Auth::id(),
                    'status_updated_at' => now(),
                    'status_remarks' => $remarks,
                    'is_active' => $isReject ? 0 : 1,
                    'updated_at' => now(),
                ]);

            Log::channel('insert_log')->info('✓ [UPDATE] Vendor status updated successfully', [
                'vendor_id' => $vendorId,
                'new_status_id' => $newVendorStatusId
            ]);

            // Insert into txn_issue_status_history with vendor_id
            Log::info('[VENDOR STATUS UPDATE] Creating status history entry with vendor_id');
            $this->createStatusHistory(
                $issue,
                $newVendorStatusId,
                ($remarks ?? ''),
                $vendorId
            );

            // Check if all active vendors are now resolved
            Log::info('[VENDOR STATUS UPDATE] Checking if all active vendors are resolved');
            $vendorProgress = $this->getVendorProgress($issue);
            
            if ($vendorProgress && 
                $vendorProgress['active_count'] > 0 && 
                $vendorProgress['active_count'] === $vendorProgress['resolved_count']) {
                
                Log::info('[VENDOR STATUS UPDATE] All active vendors resolved - updating main ticket status', [
                    'issue_id' => $issue->issue_id,
                    'active_count' => $vendorProgress['active_count'],
                    'resolved_count' => $vendorProgress['resolved_count']
                ]);
                
                // Get Resolved status ID (typically 3)
                $resolvedStatus = DB::table('mst_issue_status')
                    ->where('status_name', 'Resolved')
                    ->first(['status_id']);
                
                if ($resolvedStatus) {
                    DB::table('txn_issue')
                        ->where('issue_id', $issue->issue_id)
                        ->update([
                            'status_id' => $resolvedStatus->status_id,
                            'updated_at' => now(),
                        ]);
                    
                    // Record the overall status change in history
                    Log::info('[VENDOR STATUS UPDATE] Recording overall ticket resolution in history');
                    $this->createStatusHistory(
                        $issue,
                        $resolvedStatus->status_id,
                        'All active vendors resolved',
                        null  // No vendor_id for overall status
                    );
                    
                    Log::info('[VENDOR STATUS UPDATE] Main ticket status updated to Resolved', [
                        'issue_id' => $issue->issue_id,
                        'status_id' => $resolvedStatus->status_id
                    ]);
                }
            } elseif ($vendorProgress && $vendorProgress['active_count'] > 0) {
                $resolvedStatusId = DB::table('mst_issue_status')
                    ->whereRaw('LOWER(status_name) = ?', ['resolved'])
                    ->value('status_id');

                if ($resolvedStatusId && (int) $issue->status_id === (int) $resolvedStatusId) {
                    $inProgressStatusId = DB::table('mst_issue_status')
                        ->whereRaw('LOWER(status_name) = ?', ['in progress'])
                        ->value('status_id');

                    if ($inProgressStatusId) {
                        DB::table('txn_issue')
                            ->where('issue_id', $issue->issue_id)
                            ->update([
                                'status_id' => $inProgressStatusId,
                                'updated_at' => now(),
                            ]);
                    }
                }
            } else {
                Log::info('[VENDOR STATUS UPDATE] Not all active vendors resolved yet', [
                    'issue_id' => $issue->issue_id,
                    'active_count' => $vendorProgress['active_count'] ?? 0,
                    'resolved_count' => $vendorProgress['resolved_count'] ?? 0
                ]);
            }

            DB::commit();

            Log::info('[VENDOR STATUS UPDATE] Vendor status update completed successfully', [
                'issue_id' => $issue->issue_id,
                'vendor_id' => $vendorId,
                'status_id' => $newVendorStatusId
            ]);

            return [
                'success' => true,
                'issue_id' => $issue->issue_id,
                'vendor_id' => $vendorId,
                'status_id' => $newVendorStatusId,
            ];

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('[VENDOR STATUS UPDATE] Failed to update vendor status', [
                'issue_id' => $issue->issue_id,
                'vendor_id' => $vendorId,
                'error' => $e->getMessage(),
                'line' => $e->getLine()
            ]);

            throw $e;
        }
    }

    /**
     * Get Vendor Progress
     * Returns count of active vendors and resolved vendors
     * Resolved = vendor_status_id = 3 (Resolved status)
     */
    public function getVendorProgress(Issue $issue): ?array
    {
        $vendorAssignments = DB::table('map_issue_vendor_assignment as m')
            ->join('mst_issue_status as s', 'm.vendor_status_id', '=', 's.status_id')
            ->where('m.issue_id', $issue->issue_id)
            ->where('m.is_active', 1)
            ->get(['m.vendor_id', 'm.vendor_status_id', 's.status_name']);

        if ($vendorAssignments->isEmpty()) {
            return null;
        }

        $activeCount = $vendorAssignments->count();
        $resolvedCount = $vendorAssignments->filter(function ($row) {
            return (int) $row->vendor_status_id === 3;  // 3 = Resolved status
        })->count();

        Log::info('[VENDOR PROGRESS] Calculated vendor progress', [
            'issue_id' => $issue->issue_id,
            'active_count' => $activeCount,
            'resolved_count' => $resolvedCount
        ]);

        return [
            'active_count' => $activeCount,
            'resolved_count' => $resolvedCount,
            'assignments' => $vendorAssignments,
        ];
    }

    /**
     * Get Pending Vendors List
     * Returns list of vendors that have not resolved the issue
     * Unresolved = vendor_status_id != 3 (not Resolved status)
     */
    public function getPendingVendorsList(Issue $issue): array
    {
        $pendingVendors = DB::table('map_issue_vendor_assignment as m')
            ->join('mst_vendor as v', 'm.vendor_id', '=', 'v.vendor_id')
            ->join('mst_issue_status as s', 'm.vendor_status_id', '=', 's.status_id')
            ->where('m.issue_id', $issue->issue_id)
            ->where('m.is_active', 1)
            ->where('m.vendor_status_id', '!=', 3)  // Not Resolved (3 = Resolved)
            ->get(['v.vendor_name', 's.status_name', 'm.vendor_id'])
            ->map(function ($row) {
                return [
                    'vendor_id' => (int) $row->vendor_id,
                    'vendor_name' => $row->vendor_name,
                    'status' => $row->status_name,
                ];
            })
            ->all();

        return $pendingVendors;
    }

    /**
     * Format Pending Vendors Message for Error Display
     */
    private function formatPendingVendorsMessage(array $pendingList): string
    {
        if (empty($pendingList)) {
            return 'Unable to close ticket.';
        }

        $message = "Ticket cannot be closed. The following vendors have not resolved the issue:\n";
        foreach ($pendingList as $vendor) {
            $message .= "- " . $vendor['vendor_name'] . " - " . $vendor['status'] . "\n";
        }
        return trim($message);
    }

    /**
     * Bulk Status Update
     */
    public function bulkStatusUpdate(
        array $ids,
        string $status
    ): bool {

        DB::beginTransaction();

        try {

            $this->repository
                ->bulkStatusUpdate(
                    $ids,
                    $status
                );

            foreach ($ids as $id) {

                $issue = $this->repository
                    ->find($id);

                if ($issue) {

                    $this->createHistory(

                        $issue,

                        'Bulk Status',

                        $status

                    );

                }

            }

            DB::commit();

            return true;

        } catch (\Throwable $e) {

            DB::rollBack();

            throw $e;

        }

    }

    /**
     * Bulk Delete
     */
    public function bulkDelete(
        array $ids
    ): bool {

        DB::beginTransaction();

        try {

            foreach ($ids as $id) {

                $issue = $this->repository
                    ->find($id);

                if (!$issue) {

                    continue;

                }

                $this->removeAttachment(
                    $issue
                );

            }

            $this->repository
                ->bulkDelete($ids);

            DB::commit();

            return true;

        } catch (\Throwable $e) {

            DB::rollBack();

            throw $e;

        }

    }

    /**
     * Export Collection
     */
    public function export(
        array $filters = []
    )
    {

        return $this->repository
            ->filter($filters);

    }

    /**
     * Get Dashboard Widget Data
     */
    public function dashboardWidgets(): array
    {

        return [

            'summary' => $this->summary(),

            'priority' => $this->prioritySummary(),

            'projects' => $this->projectSummary(),

            'states' => $this->stateSummary(),

            'monthly' => $this->monthlyReport(),

            'recent' => $this->recent(),

            'activity' => $this->recentActivity(),

        ];

    }

    /**
     * Check Whether Issue Can Be Closed
     */
    public function canClose(
        Issue $issue
    ): bool {

        return in_array(

            $issue->status,

            [

                'Resolved',

                'In Progress',

                'Assigned'

            ]

        );

    }

    /**
     * Check Whether Issue Can Be Reopened
     */
    public function canReopen(
        Issue $issue
    ): bool {

        return $issue->status === 'Closed';

    }

    /**
     * Ticket Exists
     */
    public function ticketExists(
        string $ticketNo
    ): bool {

        return $this->repository
                ->findByTicket($ticketNo)
            !== null;

    }

    /**
     * Get Ticket By Number
     */
    public function getByTicket(
        string $ticketNo
    ): ?Issue {

        return $this->repository
            ->findByTicket($ticketNo);

    }

    /**
     * Refresh Issue
     */
    public function refresh(
        Issue $issue
    ): Issue {

        return $this->repository
            ->findOrFail($issue->id);

    }

    /**
     * Health Check
     */
    public function health(): array
    {

        return [

            'status' => 'OK',

            'time' => now(),

            'service' => class_basename($this),

            'repository' => class_basename($this->repository),

        ];

    }




protected function validateCreateRequest(Request $request): void
{
    $errors = [];

    if (blank($request->state_id)) {
        $errors['state_id'] = 'State is required.';
    }

    // Service is optional; do not enforce here.

    if (blank($request->project_id)) {
        $errors['project_id'] = 'Project is required.';
    }

    if (blank($request->application_id)) {
        $errors['application_id'] = 'Application is required.';
    }

    if (blank($request->issue_category_id)) {
        $errors['issue_category_id'] = 'Issue Category is required.';
    }

    if (blank($request->priority_id)) {
        $errors['priority_id'] = 'Priority is required.';
    }

    if (blank($request->subject)) {
        $errors['subject'] = 'Subject is required.';
    }

    if (blank($request->description)) {
        $errors['description'] = 'Description is required.';
    }

    if (!empty($errors)) {
        throw ValidationException::withMessages($errors);
    }
}

}