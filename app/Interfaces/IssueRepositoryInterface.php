<?php

namespace App\Interfaces;

use App\Models\Issue;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface IssueRepositoryInterface
{
    /**
     * Get paginated issues.
     */
    public function paginate(array $filters = [],int $perPage = 15): LengthAwarePaginator;

    /**
     * Get all issues.
     */
    public function all(): Collection;

    /**
     * Find issue by ID.
     */
    public function find(int $id): ?Issue;

    /**
     * Find issue by ID or fail.
     */
    public function findOrFail(int $id): Issue;

    /**
     * Find issue by ticket number.
     */
    public function findByTicket(string $ticketNo): ?Issue;

    /**
     * Create new issue.
     */
    public function create(array $data): Issue;

    /**
     * Update issue.
     */
    public function update(Issue $issue, array $data): bool;

    /**
     * Delete issue.
     */
    public function delete(Issue $issue): bool;

    /**
     * Get latest ticket.
     */
    public function latest(): ?Issue;

    /**
     * Dashboard statistics.
     */
    public function dashboard(): array;

    /**
     * Count issues by status.
     */
    public function countByStatus(string $status): int;

    /**
     * Count issues by priority.
     */
    public function countByPriority(int $priorityId): int;

    /**
     * Get issues assigned to user.
     */
    public function assignedTo(int $userId): Collection;
    

    /**
     * Get issues created by user.
     */
    public function createdBy(int $userId): Collection;

    /**
     * Get open issues.
     */
    public function open(): Collection;

    /**
     * Get closed issues.
     */
    public function closed(): Collection;

    /**
     * Get overdue SLA issues.
     */
    public function slaBreached(): Collection;

    /**
     * Search issues.
     */
    public function search(string $keyword): Collection;

    /**
     * Filter issues.
     */
    public function filter(array $filters): Collection;

    /**
     * Change issue status.
     */
    public function updateStatus(
        Issue $issue,
        string $status
    ): bool;

    /**
     * Assign issue.
     */
    public function assign(
        Issue $issue,
        int $userId
    ): bool;

    /**
     * Get recent issues.
     */
    public function recent(
        int $limit = 10
    ): Collection;

    /**
     * Bulk delete.
     */
    public function bulkDelete(array $ids): bool;

    /**
     * Bulk status update.
     */
    public function bulkStatusUpdate(array $ids,string $status): bool;
}