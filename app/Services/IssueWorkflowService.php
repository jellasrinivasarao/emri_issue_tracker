<?php

namespace App\Services;

use App\Models\Issue;
use App\Models\IssueStatus;
use App\Models\IssueUpdate;
use App\Models\IssueStatusHistory;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class IssueWorkflowService
{
    /*
    |--------------------------------------------------------------------------
    | Start Work
    |--------------------------------------------------------------------------
    */

    public function startWork(
        Issue $issue,
        int $userId
    ): Issue {

        return DB::transaction(function () use ($issue, $userId) {

            $this->validateNotClosed($issue);

            $status = $this->getStatus('In Progress');

            $oldStatus = $issue->statusName();

            $issue->update([
                'status_id' => $status->status_id,
                'current_owner_user_id' => $userId,
                'current_assignee_id' => $userId,
                'opened_at' => $issue->opened_at ?? now(),
                'assigned_at' => $issue->assigned_at ?? now(),
            ]);

            $this->history(
                $issue,
                $oldStatus,
                'In Progress',
                'Work started',
                $userId
            );

            return $issue->fresh();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Request Information
    |--------------------------------------------------------------------------
    */

    public function requestInformation(
        Issue $issue,
        string $message,
        int $userId
    ): Issue {

        return DB::transaction(function () use (
            $issue,
            $message,
            $userId
        ) {

            $this->validateNotClosed($issue);

            if (trim($message) === '') {
                throw new RuntimeException(
                    'Information request message is required.'
                );
            }

            $status = $this->getStatus('Pending');

            $oldStatus = $issue->statusName();

            $issue->update([
                'status_id' => $status->status_id,
            ]);

            IssueUpdate::create([
                'issue_id' => $issue->issue_id,
                'update_type' => 'Information Requested',
                'update_message' => $message,
                'created_by' => $userId,
                'created_at' => now(),
            ]);

            $this->history(
                $issue,
                $oldStatus,
                'Pending',
                $message,
                $userId
            );

            return $issue->fresh();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Escalate Vendor
    |--------------------------------------------------------------------------
    */

    public function escalateToVendor(
        Issue $issue,
        string $reason,
        int $userId
    ): Issue {

        return DB::transaction(function () use (
            $issue,
            $reason,
            $userId
        ) {

            $this->validateNotClosed($issue);

            if (trim($reason) === '') {
                throw new RuntimeException(
                    'Vendor escalation reason is required.'
                );
            }

            $status = $this->getStatus('Pending');

            $oldStatus = $issue->statusName();

            $issue->update([
                'status_id' => $status->status_id,
            ]);

            IssueUpdate::create([
                'issue_id' => $issue->issue_id,
                'update_type' => 'Vendor Escalation',
                'update_message' => $reason,
                'created_by' => $userId,
                'created_at' => now(),
            ]);

            $this->history(
                $issue,
                $oldStatus,
                'Pending',
                'Escalated to Vendor: ' . $reason,
                $userId
            );

            return $issue->fresh();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Submit Resolution
    |--------------------------------------------------------------------------
    */

    public function submitResolution(
        Issue $issue,
        string $resolution,
        int $userId
    ): Issue {

        return DB::transaction(function () use (
            $issue,
            $resolution,
            $userId
        ) {

            $this->validateNotClosed($issue);

            if (trim($resolution) === '') {
                throw new RuntimeException(
                    'Resolution summary is required.'
                );
            }

            $status = $this->getStatus('Resolved');

            $oldStatus = $issue->statusName();

            $issue->update([
                'status_id' => $status->status_id,
                'resolution_summary' => $resolution,
                'resolved_by' => $userId,
                'resolved_at' => now(),
            ]);

            IssueUpdate::create([
                'issue_id' => $issue->issue_id,
                'update_type' => 'Resolution',
                'update_message' => $resolution,
                'created_by' => $userId,
                'created_at' => now(),
            ]);

            $this->history(
                $issue,
                $oldStatus,
                'Resolved',
                $resolution,
                $userId
            );

            return $issue->fresh();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Internal
    |--------------------------------------------------------------------------
    */

    protected function getStatus(string $statusName): IssueStatus
    {
        $status = IssueStatus::query()
            ->where('status_name', $statusName)
            ->first();

        if (!$status) {
            throw new RuntimeException(
                "Issue status [{$statusName}] not configured."
            );
        }

        return $status;
    }

    protected function validateNotClosed(Issue $issue): void
    {
        if ($issue->isClosed()) {
            throw new RuntimeException(
                'Closed issue cannot be modified.'
            );
        }
    }

    protected function history(
        Issue $issue,
        ?string $oldStatus,
        string $newStatus,
        string $remarks,
        int $userId
    ): void {

        IssueStatusHistory::create([
            'issue_id' => $issue->issue_id,
            'old_status' => $oldStatus,
            'new_status' => $newStatus,
            'remarks' => $remarks,
            'changed_by' => $userId,
            'created_at' => now(),
        ]);
    }
}