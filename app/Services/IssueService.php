<?php

namespace App\Services;

use App\Interfaces\IssueRepositoryInterface;
use App\Models\Issue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

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
    public function create(Request $request): Issue
    {
        DB::beginTransaction();

        try {

            $data = $this->prepareCreateData($request);

            $issue = $this->repository->create($data);

            $this->afterCreate($issue);

            DB::commit();

            return $issue;

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error(
                'Issue Create Error',
                [
                    'message' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile()
                ]
            );

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
    protected function prepareCreateData(
        Request $request
    ): array {

        return [

            'ticket_no' => $this->generateTicketNumber(),

            'state_id' => $request->state_id,

            'service_id' => $request->service_id,

            'project_id' => $request->project_id,

            'application_id' => $request->application_id,

            'module_id' => $request->module_id,

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
                => $this->uploadAttachment($request),

            'status' => 'Open',

            'created_by'
                => Auth::id(),

            'created_at'
                => now(),

            'updated_at'
                => now(),

        ];
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

            $attachment = $this->uploadAttachment(
                $request
            );
        }

        return [

            'state_id'
                => $request->state_id,

            'service_id'
                => $request->service_id,

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
        $last = $this->repository->latest();

        $next = $last
            ? ($last->id + 1)
            : 1;

        return sprintf(
            'ISS-%s-%06d',
            date('Y'),
            $next
        );
    }

    /**
     * Upload Attachment
     */
    protected function uploadAttachment(
        Request $request
    ): ?string {

        if (!$request->hasFile('attachment')) {
            return null;
        }

        $file = $request->file('attachment');

        $filename =
            Carbon::now()->format('YmdHis')
            .'_'
            .Str::uuid()
            .'.'
            .$file->getClientOriginalExtension();

        $file->storeAs(
            'issues',
            $filename,
            'public'
        );

        return $filename;
    }

    /**
     * Remove Attachment
     */
    protected function removeAttachment(
        Issue $issue
    ): void {

        if (!$issue->attachment) {
            return;
        }

        if (
            Storage::disk('public')
                ->exists('issues/'.$issue->attachment)
        ) {

            Storage::disk('public')
                ->delete('issues/'.$issue->attachment);

        }
    }

    /**
     * After Create Process
     */
    protected function afterCreate(Issue $issue): void
    {
        $this->assignEngineer($issue);

        $this->createHistory(
            $issue,
            'Issue Created',
            'Issue created successfully.'
        );

        $this->sendAssignmentNotification($issue);
    }

    /**
     * Auto Assign Engineer
     */
    protected function assignEngineer(Issue $issue): void
    {
        $engineer = \App\Models\User::query()
            ->where('status', 1)
            ->whereHas('roles', function ($query) {
                $query->where('name', 'Support Engineer');
            })
            ->withCount([
                'assignedIssues as active_issue_count' => function ($query) {
                    $query->whereNotIn('status', [
                        'Closed',
                        'Resolved'
                    ]);
                }
            ])
            ->orderBy('active_issue_count')
            ->first();

        if (!$engineer) {
            return;
        }

        $this->repository->assign(
            $issue,
            $engineer->id
        );

        $this->createHistory(
            $issue,
            'Assigned',
            'Assigned to '.$engineer->name
        );
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

        \App\Models\IssueHistory::create([

            'issue_id' => $issue->id,

            'action' => $action,

            'remarks' => $remarks,

            'performed_by' => Auth::id(),

            'performed_at' => now()

        ]);

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
     * Close Issue
     */
    public function close(
        Issue $issue,
        ?string $remarks = null
    ): Issue {

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

            $issue = $this->repository
                ->findOrFail($issue->id);

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

}