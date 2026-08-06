<?php

namespace App\Repositories;

use App\Interfaces\IssueRepositoryInterface;
use App\Models\Issue;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class IssueRepository implements IssueRepositoryInterface
{
    protected Issue $model;

    /**
     * Constructor
     */
    public function __construct(Issue $model)
    {
        $this->model = $model;
    }

    /**
     * Common Relationships
     */
    protected function relations(): array
    {
        return [
            'state',
            'service',
            'project',
            'application',
            'module',
            'category',
            'priority',
            'creator',
            'updater',
        ];
    }

    /**
     * Get All Records
     */
    public function all(): Collection
    {
        return $this->model
            ->with($this->relations())
            ->latest()
            ->get();
    }

    /**
     * Pagination
     */
    public function paginate(
        array $filters = [],
        int $perPage = 15
    ): LengthAwarePaginator {

        $query = $this->model
            ->with($this->relations());

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['priority'])) {
            $query->where('priority_id', $filters['priority']);
        }

        if (!empty($filters['project'])) {
            $query->where('project_id', $filters['project']);
        }

        if (!empty($filters['service'])) {
            $query->where('service_id', $filters['service']);
        }

        if (!empty($filters['state'])) {
            $query->where('state_id', $filters['state']);
        }

        if (!empty($filters['ticket_no'])) {
            $query->where(
                'ticket_no',
                'LIKE',
                "%{$filters['ticket_no']}%"
            );
        }

        return $query
            ->latest()
            ->paginate($perPage);
    }

    /**
     * Find
     */
    public function find(int $id): ?Issue
    {
        return $this->model
            ->with($this->relations())
            ->find($id);
    }

    /**
     * Find Or Fail
     */
    public function findOrFail(int $id): Issue
    {
        return $this->model
            ->with($this->relations())
            ->findOrFail($id);
    }

    /**
     * Find By Ticket
     */
    public function findByTicket(string $ticketNo): ?Issue
    {
        return $this->model
            ->with($this->relations())
            ->where('ticket_no', $ticketNo)
            ->first();
    }

    /**
     * Latest Record
     */
    public function latest(): ?Issue
    {
        return $this->model
            ->latest('id')
            ->first();
    }

    /**
     * Create
     */
    public function create(array $data): Issue
    {
        DB::beginTransaction();

        try {

            $issue = $this->model->create($data);

            DB::commit();

            return $issue;

        } catch (\Exception $e) {

            DB::rollBack();

            Log::error(
                'Issue Create Error : '.$e->getMessage()
            );

            throw $e;
        }
    }

    /**
     * Update
     */
    public function update(
        Issue $issue,
        array $data
    ): bool {

        DB::beginTransaction();

        try {

            $status = $issue->update($data);

            DB::commit();

            return $status;

        } catch (\Exception $e) {

            DB::rollBack();

            Log::error(
                'Issue Update Error : '.$e->getMessage()
            );

            throw $e;
        }
    }

    /**
     * Delete
     */
    public function delete(Issue $issue): bool
    {
        DB::beginTransaction();

        try {

            $status = $issue->delete();

            DB::commit();

            return $status;

        } catch (\Exception $e) {

            DB::rollBack();

            Log::error(
                'Issue Delete Error : '.$e->getMessage()
            );

            throw $e;
        }
    }



        /**
     * Search Issues
     */
    public function search(string $keyword): Collection
    {
        return $this->model
            ->with($this->relations())
            ->where(function ($query) use ($keyword) {

                $query->where('ticket_no', 'LIKE', "%{$keyword}%")
                    ->orWhere('subject', 'LIKE', "%{$keyword}%")
                    ->orWhere('description', 'LIKE', "%{$keyword}%");

            })
            ->latest()
            ->get();
    }

    /**
     * Advanced Filter
     */
    public function filter(array $filters): Collection
    {
        $query = $this->model
            ->with($this->relations());

        if (!empty($filters['state_id'])) {

            $query->where('state_id', $filters['state_id']);

        }

        if (!empty($filters['service_id'])) {

            $query->where('service_id', $filters['service_id']);

        }

        if (!empty($filters['project_id'])) {

            $query->where('project_id', $filters['project_id']);

        }

        if (!empty($filters['application_id'])) {

            $query->where('application_id', $filters['application_id']);

        }

        if (!empty($filters['module_id'])) {

            $query->where('module_id', $filters['module_id']);

        }

        if (!empty($filters['priority_id'])) {

            $query->where('priority_id', $filters['priority_id']);

        }

        if (!empty($filters['issue_category_id'])) {

            $query->where(
                'issue_category_id',
                $filters['issue_category_id']
            );

        }

        if (!empty($filters['status'])) {

            $query->where('status', $filters['status']);

        }

        if (!empty($filters['from_date'])) {

            $query->whereDate(
                'created_at',
                '>=',
                $filters['from_date']
            );

        }

        if (!empty($filters['to_date'])) {

            $query->whereDate(
                'created_at',
                '<=',
                $filters['to_date']
            );

        }

        return $query
            ->latest()
            ->get();
    }

    /**
     * Open Issues
     */
    public function open(): Collection
    {
        return $this->model
            ->with($this->relations())
            ->where('status', 'Open')
            ->latest()
            ->get();
    }

    /**
     * Closed Issues
     */
    public function closed(): Collection
    {
        return $this->model
            ->with($this->relations())
            ->where('status', 'Closed')
            ->latest()
            ->get();
    }

    /**
     * Assigned Issues
     */
    public function assignedTo(int $userId): Collection
    {
        return $this->model
            ->with($this->relations())
            ->where('assigned_to', $userId)
            ->latest()
            ->get();
    }

    /**
     * Created By User
     */
    public function createdBy(int $userId): Collection
    {
        return $this->model
            ->with($this->relations())
            ->where('created_by', $userId)
            ->latest()
            ->get();
    }

    /**
     * Recent Issues
     */
    public function recent(int $limit = 10): Collection
    {
        return $this->model
            ->with($this->relations())
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Count By Status
     */
    public function countByStatus(string $status): int
    {
        return $this->model
            ->where('status', $status)
            ->count();
    }

    /**
     * Count By Priority
     */
    public function countByPriority(int $priorityId): int
    {
        return $this->model
            ->where('priority_id', $priorityId)
            ->count();
    }
}