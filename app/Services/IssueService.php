<?php

namespace App\Services;

use App\Interfaces\IssueRepositoryInterface;
use App\Models\Issue;
use App\Models\IssueAttachment;
use App\Models\IssueHistory;
use App\Models\IssueStatusHistory;
use App\Models\ProjectSupportConfiguration;

use Carbon\Carbon;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

use Illuminate\Validation\ValidationException;

class IssueService
{
    /*
    |--------------------------------------------------------------------------
    | Repository
    |--------------------------------------------------------------------------
    */

    protected IssueRepositoryInterface $repository;

    /*
    |--------------------------------------------------------------------------
    | Routing Service
    |--------------------------------------------------------------------------
    */

    protected IssueRoutingService $routingService;

    /*
    |--------------------------------------------------------------------------
    | Constructor
    |--------------------------------------------------------------------------
    */

    public function __construct(
        IssueRepositoryInterface $repository,
        IssueRoutingService $routingService
    ) {
        $this->repository = $repository;
        $this->routingService = $routingService;
    }


    /*
    |--------------------------------------------------------------------------
    | GET PAGINATED ISSUES
    |--------------------------------------------------------------------------
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


    /*
    |--------------------------------------------------------------------------
    | FIND ISSUE
    |--------------------------------------------------------------------------
    */

    public function find(int $id): Issue
    {
        return $this->repository->findOrFail($id);
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE ISSUE
    |--------------------------------------------------------------------------
    |
    | Main enterprise issue creation flow.
    |
    | Issue
    |   ↓
    | Project Support Configuration
    |   ↓
    | Routing Rule
    |   ↓
    | Working Calendar
    |   ↓
    | HO IT / Vendor
    |
    */

    public function create(
    array $data,
    ?UploadedFile $attachment = null
): Issue {

    DB::beginTransaction();

    try {

        $payload = $this->prepareCreateData($data);

        $issue = $this->repository->create($payload);

        if ($attachment) {
            $this->uploadAttachment($attachment, $issue);
        }

        // Enterprise routing happens here ONCE
        $assignment = $this->routingService->routeIssue(
            $issue->fresh()
        );

        $this->createHistory(
            $issue->fresh(),
            'Issue Created',
            'Issue created successfully.'
        );

        if ($assignment) {
            $this->sendAssignmentNotification(
                $issue->fresh()
            );
        }

        DB::commit();

        return $issue->fresh();

    } catch (\Throwable $e) {

        DB::rollBack();

        Log::error(
            'Issue Create Error',
            [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]
        );

        throw $e;
    }
}


    /*
    |--------------------------------------------------------------------------
    | PREPARE CREATE DATA
    |--------------------------------------------------------------------------
    */

    protected function prepareCreateData(
        array $data
    ): array {

        $projectId =
            $data['project_id'] ?? null;


        /*
        |--------------------------------------------------------------------------
        | Support Configuration
        |--------------------------------------------------------------------------
        */

        $supportConfigId =
            $this->resolveSupportConfigurationId(
                $projectId
            );


        return [

            /*
            |--------------------------------------------------------------------------
            | Ticket
            |--------------------------------------------------------------------------
            */

            'issue_number' =>
                $this->generateTicketNumber(),


            /*
            |--------------------------------------------------------------------------
            | Basic References
            |--------------------------------------------------------------------------
            */

            'state_id' =>
                $data['state_id'] ?? null,

            'service_id' =>
                $data['service_id'] ?? null,

            'project_id' =>
                $projectId,

            'support_config_id' =>
                $supportConfigId,

            'application_id' =>
                $data['application_id'] ?? null,

            'module_id' =>
                $data['module_id'] ?? null,

            'issue_category_id' =>
                $data['issue_category_id'] ?? null,

            'priority_id' =>
                $data['priority_id'] ?? null,


            /*
            |--------------------------------------------------------------------------
            | Issue
            |--------------------------------------------------------------------------
            */

            'issue_title' =>
                trim(
                    (string) (
                        $data['subject'] ?? ''
                    )
                ),

            'issue_description' =>
                trim(
                    (string) (
                        $data['description'] ?? ''
                    )
                ),


            /*
            |--------------------------------------------------------------------------
            | Status
            |--------------------------------------------------------------------------
            */

            'status' =>
                'Open',


            /*
            |--------------------------------------------------------------------------
            | User
            |--------------------------------------------------------------------------
            */

            'created_by' =>
                Auth::id(),


            /*
            |--------------------------------------------------------------------------
            | Dates
            |--------------------------------------------------------------------------
            */

            'created_at' =>
                now(),

            'updated_at' =>
                now(),
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | RESOLVE PROJECT SUPPORT CONFIGURATION
    |--------------------------------------------------------------------------
    */

    protected function resolveSupportConfigurationId(
        ?int $projectId
    ): ?int {

        if (!$projectId) {

            return null;
        }


        /*
        |--------------------------------------------------------------------------
        | Base Query
        |--------------------------------------------------------------------------
        */

        $query =
            ProjectSupportConfiguration::query()
                ->where(
                    'project_id',
                    $projectId
                )
                ->where(
                    'is_active',
                    1
                );


        /*
        |--------------------------------------------------------------------------
        | Prefer Auto Routing Configuration
        |--------------------------------------------------------------------------
        */

        if (
            Schema::hasColumn(
                'mst_project_support_configuration',
                'auto_routing_enabled'
            )
        ) {

            $configuration =
                (clone $query)
                    ->where(
                        'auto_routing_enabled',
                        1
                    )
                    ->orderByDesc(
                        'support_config_id'
                    )
                    ->first();


            if ($configuration) {

                return
                    $configuration
                        ->support_config_id;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Fallback Active Configuration
        |--------------------------------------------------------------------------
        */

        return
            $query
                ->orderByDesc(
                    'support_config_id'
                )
                ->first()
                ?->support_config_id;
    }


    /*
    |--------------------------------------------------------------------------
    | GENERATE ISSUE NUMBER
    |--------------------------------------------------------------------------
    */

    protected function generateTicketNumber(): string
    {
        $today =
            date('Ymd');

        $last =
            $this->repository->latest();

        $next =
            $last
                ? $last->issue_id + 1
                : 1;

        return sprintf(
            'IS-%s%03d',
            $today,
            $next
        );
    }


    /*
    |--------------------------------------------------------------------------
    | UPLOAD ATTACHMENT
    |--------------------------------------------------------------------------
    */

    protected function uploadAttachment(
        ?UploadedFile $file,
        Issue $issue
    ): ?string {

        if (!$file) {

            return null;
        }


        /*
        |--------------------------------------------------------------------------
        | Store File
        |--------------------------------------------------------------------------
        */

        $path =
            $file->store(
                'issues',
                'public'
            );


        /*
        |--------------------------------------------------------------------------
        | Create Attachment Record
        |--------------------------------------------------------------------------
        */

        $attachment =
            IssueAttachment::create([

                'issue_id' =>
                    $issue->issue_id,

                'user_id' =>
                    Auth::id() ?? 1,

                'original_file_name' =>
                    $file->getClientOriginalName(),

                'stored_file_name' =>
                    basename($path),

                'file_path' =>
                    '/storage/' . $path,

                'file_size' =>
                    $file->getSize(),

                'file_type' =>
                    $file->getMimeType(),

                'uploaded_at' =>
                    now(),

                'is_active' =>
                    1,
            ]);


        Log::info(
            'Issue Attachment Created',
            [
                'issue_id' =>
                    $issue->issue_id,

                'attachment_id' =>
                    $attachment->attachment_id,
            ]
        );


        return $path;
    }


    /*
    |--------------------------------------------------------------------------
    | REMOVE ATTACHMENT
    |--------------------------------------------------------------------------
    */

    protected function removeAttachment(
        Issue $issue
    ): void {

        if (!$issue->attachment) {

            return;
        }


        $path =
            'issues/' .
            $issue->attachment;


        if (
            Storage::disk('public')
                ->exists($path)
        ) {

            Storage::disk('public')
                ->delete($path);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | AFTER CREATE
    |--------------------------------------------------------------------------
    */

    protected function afterCreate(
        Issue $issue
    ): void {

        /*
        |--------------------------------------------------------------------------
        | IMPORTANT
        |--------------------------------------------------------------------------
        |
        | This method is intentionally NOT assigning
        | a generic Support Engineer.
        |
        | Routing is handled by IssueRoutingService.
        |
        */

        $this->createHistory(
            $issue,
            'Issue Created',
            'Issue created successfully.'
        );


        $assignment =
            $this->routingService->routeIssue(
                $issue->fresh()
            );


        if ($assignment) {

            $this->sendAssignmentNotification(
                $issue->fresh()
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | CHANGE STATUS
    |--------------------------------------------------------------------------
    */

    public function changeStatus(
        Issue $issue,
        string $status,
        ?string $remarks = null
    ): Issue {

        DB::beginTransaction();

        try {

            $oldStatus = $issue->status;

            $this->validateStatusTransition(
                $oldStatus,
                $status
            );


            $this->repository->updateStatus(
                $issue,
                $status
            );


            $issue =
                $this->repository->findOrFail(
                    $issue->issue_id
                );


            $this->createHistory(
                $issue,
                'Status Changed',
                ($remarks ?? '') .
                " ({$status})"
            );


            $this->createStatusHistory(
            $issue,
            null,
            null,
            $oldStatus,
            $status,
            [
                'change_type' =>
                    'MANUAL',

                'remarks' =>
                    $remarks,

                'change_reason' =>
                    'User status change',
            ]
        );

        


            DB::commit();


            return $issue;


        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error(
                'Issue Status Change Error',
                [
                    'issue_id' =>
                        $issue->issue_id,

                    'message' =>
                        $e->getMessage(),
                ]
            );

            throw $e;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | WORKFLOW
    |--------------------------------------------------------------------------
    */

    protected function validateStatusTransition(
        string $current,
        string $next
    ): void {

        $workflow = [

            'Open' => [
                'Assigned',
                'Closed',
            ],

            'Assigned' => [
                'In Progress',
                'Closed',
            ],

            'In Progress' => [
                'Resolved',
                'Closed',
            ],

            'Resolved' => [
                'Closed',
                'Reopened',
            ],

            'Reopened' => [
                'Assigned',
                'In Progress',
            ],
        ];


        /*
        |--------------------------------------------------------------------------
        | Unknown Status
        |--------------------------------------------------------------------------
        */

        if (!isset($workflow[$current])) {

            return;
        }


        if (
            !in_array(
                $next,
                $workflow[$current],
                true
            )
        ) {

            throw new \Exception(
                "Invalid workflow transition from {$current} to {$next}"
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | SLA
    |--------------------------------------------------------------------------
    */

    public function calculateSLA(
        Issue $issue
    ): Carbon {

        $priority =
            optional(
                $issue->priority
            )->priority_name;


        return match ($priority) {

            'Critical' =>
                $issue->created_at
                    ->copy()
                    ->addHours(2),

            'High' =>
                $issue->created_at
                    ->copy()
                    ->addHours(4),

            'Medium' =>
                $issue->created_at
                    ->copy()
                    ->addHours(8),

            'Low' =>
                $issue->created_at
                    ->copy()
                    ->addDay(),

            default =>
                $issue->created_at
                    ->copy()
                    ->addDay(),
        };
    }


    /*
    |--------------------------------------------------------------------------
    | SLA BREACHED
    |--------------------------------------------------------------------------
    */

    public function isSlaBreached(
        Issue $issue
    ): bool {

        return now()->greaterThan(
            $this->calculateSLA($issue)
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ESCALATE
    |--------------------------------------------------------------------------
    */

    public function escalate(
        Issue $issue
    ): void {

        if (
            !$this->isSlaBreached($issue)
        ) {

            return;
        }


        $this->createHistory(
            $issue,
            'Escalated',
            'SLA breached.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE HISTORY
    |--------------------------------------------------------------------------
    */

    protected function createHistory(
        Issue $issue,
        string $action,
        ?string $remarks = null
    ): void {

        IssueHistory::create([

            'issue_id' =>
                $issue->issue_id,

            'action' =>
                $action,

            'remarks' =>
                $remarks,

            'performed_by' =>
                Auth::id(),

            'performed_at' =>
                now(),
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | TIMELINE
    |--------------------------------------------------------------------------
    */

    public function timeline(
        int $issueId
    ) {

        return IssueHistory::query()

            ->where(
                'issue_id',
                $issueId
            )

            ->with('user')

            ->latest(
                'performed_at'
            )

            ->get();
    }


    /*
    |--------------------------------------------------------------------------
    | ADD COMMENT
    |--------------------------------------------------------------------------
    */

    public function addComment(
        Issue $issue,
        string $comment
    ): void {

        \App\Models\IssueComment::create([

            'issue_id' =>
                $issue->issue_id,

            'comment' =>
                $comment,

            'created_by' =>
                Auth::id(),
        ]);


        $this->createHistory(
            $issue,
            'Comment Added',
            $comment
        );
    }


    /*
    |--------------------------------------------------------------------------
    | ASSIGNMENT NOTIFICATION
    |--------------------------------------------------------------------------
    */

    protected function sendAssignmentNotification(
        Issue $issue
    ): void {

        /*
        |--------------------------------------------------------------------------
        | Current Team Based Routing
        |--------------------------------------------------------------------------
        */

        if (!$issue->current_team_id) {

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | If you later have assigned_to user,
        | notification can be sent here.
        |--------------------------------------------------------------------------
        */

        if (!$issue->assigned_to) {

            return;
        }


        $user =
            \App\Models\User::find(
                $issue->assigned_to
            );


        if (!$user) {

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Add Laravel Notification here
        |--------------------------------------------------------------------------
        */

        // $user->notify(
        //     new IssueAssignedNotification($issue)
        // );
    }


    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */

    public function dashboard(): array
    {
        return $this->repository->dashboard();
    }


    /*
    |--------------------------------------------------------------------------
    | SEARCH
    |--------------------------------------------------------------------------
    */

    public function search(
        string $keyword
    ) {

        return $this->repository->search(
            trim($keyword)
        );
    }


    /*
    |--------------------------------------------------------------------------
    | FILTER
    |--------------------------------------------------------------------------
    */

    public function filter(
        array $filters
    ) {

        return $this->repository->filter(
            $filters
        );
    }


    /*
    |--------------------------------------------------------------------------
    | RECENT
    |--------------------------------------------------------------------------
    */

    public function recent(
        int $limit = 10
    ) {

        return $this->repository->recent(
            $limit
        );
    }


    /*
    |--------------------------------------------------------------------------
    | MY ISSUES
    |--------------------------------------------------------------------------
    */

    public function myIssues()
    {
        return $this->repository
            ->createdBy(
                Auth::id()
            );
    }


    /*
    |--------------------------------------------------------------------------
    | ASSIGNED TO ME
    |--------------------------------------------------------------------------
    */

    public function assignedToMe()
    {
        return $this->repository
            ->assignedTo(
                Auth::id()
            );
    }


    /*
    |--------------------------------------------------------------------------
    | OPEN ISSUES
    |--------------------------------------------------------------------------
    */

    public function openIssues()
    {
        return $this->repository->open();
    }


    /*
    |--------------------------------------------------------------------------
    | CLOSED ISSUES
    |--------------------------------------------------------------------------
    */

    public function closedIssues()
    {
        return $this->repository->closed();
    }


    /*
    |--------------------------------------------------------------------------
    | SLA BREACHED ISSUES
    |--------------------------------------------------------------------------
    */

    public function slaBreached()
    {
        return $this->repository->slaBreached();
    }


    /*
    |--------------------------------------------------------------------------
    | SUMMARY
    |--------------------------------------------------------------------------
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


    /*
    |--------------------------------------------------------------------------
    | PRIORITY SUMMARY
    |--------------------------------------------------------------------------
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


    /*
    |--------------------------------------------------------------------------
    | PROJECT SUMMARY
    |--------------------------------------------------------------------------
    */

    public function projectSummary(
        array $filters = []
    ) {

        $issues =
            $this->repository
                ->filter($filters);


        return $issues

            ->groupBy('project_id')

            ->map(function ($items) {

                return [

                    'count' =>
                        $items->count(),

                    'project' =>
                        optional(
                            $items->first()->project
                        )->project_name,
                ];
            })

            ->values();
    }


    /*
    |--------------------------------------------------------------------------
    | STATE SUMMARY
    |--------------------------------------------------------------------------
    */

    public function stateSummary(
        array $filters = []
    ) {

        $issues =
            $this->repository
                ->filter($filters);


        return $issues

            ->groupBy('state_id')

            ->map(function ($items) {

                return [

                    'count' =>
                        $items->count(),

                    'state' =>
                        optional(
                            $items->first()->state
                        )->state_name,
                ];
            })

            ->values();
    }


    /*
    |--------------------------------------------------------------------------
    | MONTHLY REPORT
    |--------------------------------------------------------------------------
    */

    public function monthlyReport(
        ?int $year = null
    ) {

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


    /*
    |--------------------------------------------------------------------------
    | RECENT ACTIVITY
    |--------------------------------------------------------------------------
    */

    public function recentActivity(
        int $limit = 20
    ) {

        return IssueHistory::query()

            ->with([
                'issue',
                'user',
            ])

            ->latest()

            ->take($limit)

            ->get();
    }

    protected function createStatusHistory(
    Issue $issue,
    ?int $fromStatusId,
    ?int $toStatusId,
    ?string $fromStatus,
    string $toStatus,
    array $options = []
): IssueStatusHistory {

    return IssueStatusHistory::create([

        'issue_id' =>
            $issue->issue_id,

        'from_status_id' =>
            $fromStatusId,

        'to_status_id' =>
            $toStatusId,

        'from_status' =>
            $fromStatus,

        'to_status' =>
            $toStatus,

        'assignment_id' =>
            $options['assignment_id'] ?? null,

        'routing_rule_id' =>
            $options['routing_rule_id'] ?? null,

        'support_config_id' =>
            $options['support_config_id']
                ?? $issue->support_config_id
                ?? null,

        'changed_by' =>
            Auth::id(),

        'change_type' =>
            $options['change_type'] ?? 'MANUAL',

        'remarks' =>
            $options['remarks'] ?? null,

        'change_reason' =>
            $options['change_reason'] ?? null,

        'changed_at' =>
            now(),
    ]);
}

    }