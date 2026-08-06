<?php

namespace App\Services;

use App\Models\Issue;
use App\Models\IssueHistory;
use App\Models\IssueStatusHistory;
use Illuminate\Support\Facades\DB;

class IssueService
{
    public function __construct(protected IssueRoutingService $routingService) {}

    // public function create(array $data): Issue
    // {
    //     return DB::transaction(function () use ($data) {

    //         $data['issue_number'] = $this->generateIssueNumber();

    //         $data['status'] = 'NEW';

    //         $data['created_by'] = auth()->id();

    //         $issue = Issue::create($data);

    //         IssueStatusHistory::create([
    //             'issue_id' => $issue->issue_id,
    //             'old_status' => null,
    //             'new_status' => 'NEW',
    //             'remarks' => 'Issue created',
    //             'changed_by' => auth()->id(),
    //             'created_at' => now(),
    //         ]);

    //         $this->routingService->route($issue);

    //         return $issue->refresh();
    //     });
    // }

    public function changeStatus(Issue $issue,string $status,?string $remarks = null): Issue {

        return DB::transaction(function () use (
            $issue,
            $status,
            $remarks
        ) {

            $oldStatus = $issue->status;

            $issue->update([
                'status' => $status,
                'updated_by' => auth()->id(),
            ]);

            IssueStatusHistory::create([
                'issue_id' => $issue->issue_id,
                'old_status' => $oldStatus,
                'new_status' => $status,
                'remarks' => $remarks,
                'changed_by' => auth()->id(),
                'created_at' => now(),
            ]);

            return $issue->refresh();
        });
    }



        public function create(array $data): Issue
    {
        return DB::transaction(function () use ($data) {

            $issue = Issue::create([
                'issue_number' => $this->generateIssueNumber(),
                'project_id' => $data['project_id'] ?? null,
                'support_config_id' => $data['support_config_id'],
                'issue_category' => $data['issue_category'] ?? null,
                'issue_type' => $data['issue_type'] ?? null,
                'subject' => $data['subject'],
                'description' => $data['description'] ?? null,
                'priority' => $data['priority'] ?? 'MEDIUM',
                'status' => 'OPEN',
                'reported_by' => auth()->id(),
                'opened_at' => now(),
            ]);

            IssueHistory::create([
                'issue_id' => $issue->issue_id,
                'action' => 'CREATED',
                'from_status' => null,
                'to_status' => 'OPEN',
                'remarks' => 'Issue created.',
                'performed_by' => auth()->id(),
                'created_at' => now(),
            ]);

            return $issue;
        });
    }

    public function update(Issue $issue, array $data): Issue
    {
        $issue->update([
            'issue_category' => $data['issue_category'] ?? null,
            'issue_type' => $data['issue_type'] ?? null,
            'subject' => $data['subject'],
            'description' => $data['description'] ?? null,
            'priority' => $data['priority'] ?? 'MEDIUM',
        ]);

        return $issue->refresh();
    }

    public function resolve(Issue $issue, ?string $remarks = null): bool
    {
        return DB::transaction(function () use ($issue, $remarks) {

            $oldStatus = $issue->status;

            $issue->update([
                'status' => 'RESOLVED',
                'resolved_at' => now(),
            ]);

            IssueHistory::create([
                'issue_id' => $issue->issue_id,
                'action' => 'RESOLVED',
                'from_status' => $oldStatus,
                'to_status' => 'RESOLVED',
                'remarks' => $remarks,
                'performed_by' => auth()->id(),
                'created_at' => now(),
            ]);

            return true;
        });
    }

    public function close(Issue $issue, ?string $remarks = null): bool
    {
        return DB::transaction(function () use ($issue, $remarks) {

            $oldStatus = $issue->status;

            $issue->update([
                'status' => 'CLOSED',
                'closed_at' => now(),
            ]);

            IssueHistory::create([
                'issue_id' => $issue->issue_id,
                'action' => 'CLOSED',
                'from_status' => $oldStatus,
                'to_status' => 'CLOSED',
                'remarks' => $remarks,
                'performed_by' => auth()->id(),
                'created_at' => now(),
            ]);

            return true;
        });
    }

    protected function generateIssueNumber(): string
    {
        return 'ISS-' . now()->format('YmdHis') . '-' . random_int(100, 999);
    }


}