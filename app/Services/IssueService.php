<?php

namespace App\Services;

use App\Models\Issue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

use App\Models\User;
use App\Models\IssueHistory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Interfaces\IssueRepositoryInterface;

class IssueService
{

    protected IssueRepositoryInterface $repository;

    public function __construct(IssueRepositoryInterface $repository) {
        $this->repository = $repository;
    }
    
    /**
     * Create New Issue
     */
    public function create(Request $request): Issue
    {
        DB::beginTransaction();

        try {

            $issue = new Issue();

            $issue->ticket_no = $this->generateTicketNumber();

            $issue->state_id = $request->state_id;
            $issue->service_id = $request->service_id;
            $issue->project_id = $request->project_id;
            $issue->application_id = $request->application_id;
            $issue->module_id = $request->module_id;

            $issue->issue_category_id = $request->issue_category_id;
            $issue->priority_id = $request->priority_id;

            $issue->subject = $request->subject;
            $issue->description = $request->description;

            $issue->occurred_date = $request->occurred_date;
            $issue->occurred_time = $request->occurred_time;

            $issue->affected_users = $request->affected_users;

            $issue->attachment = $this->uploadAttachment($request);

            $issue->status = 'Open';

            $issue->created_by = Auth::id();

            $issue->save();

            DB::commit();

            return $issue;

        } catch (\Exception $e) {

            DB::rollBack();

            Log::error($e);

            throw $e;
        }
    }

    /**
     * Update Issue
     */
    public function update(Request $request, Issue $issue): Issue
    {
        DB::beginTransaction();

        try {

            $issue->state_id = $request->state_id;
            $issue->service_id = $request->service_id;
            $issue->project_id = $request->project_id;
            $issue->application_id = $request->application_id;
            $issue->module_id = $request->module_id;

            $issue->issue_category_id = $request->issue_category_id;
            $issue->priority_id = $request->priority_id;

            $issue->subject = $request->subject;
            $issue->description = $request->description;

            $issue->occurred_date = $request->occurred_date;
            $issue->occurred_time = $request->occurred_time;

            $issue->affected_users = $request->affected_users;

            if ($request->hasFile('attachment')) {

                $this->deleteAttachment($issue);

                $issue->attachment = $this->uploadAttachment($request);

            }

            $issue->updated_by = Auth::id();

            $issue->save();

            DB::commit();

            return $issue;

        } catch (\Exception $e) {

            DB::rollBack();

            Log::error($e);

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

            $this->deleteAttachment($issue);

            $issue->delete();

            DB::commit();

            return true;

        } catch (\Exception $e) {

            DB::rollBack();

            Log::error($e);

            throw $e;
        }
    }

    /**
     * Generate Ticket Number
     */
    public function generateTicketNumber(): string
    {
        $last = Issue::latest('id')->first();

        $next = $last ? $last->id + 1 : 1;

        return sprintf(
            'ISS-%s-%06d',
            date('Y'),
            $next
        );
    }

    /**
     * Upload Attachment
     */
    protected function uploadAttachment(Request $request): ?string
    {
        if (!$request->hasFile('attachment')) {
            return null;
        }

        $file = $request->file('attachment');

        $filename = now()->format('YmdHis')
            .'_'
            .Str::random(8)
            .'.'
            .$file->getClientOriginalExtension();

        $destination = public_path('uploads/issues');

        if (!file_exists($destination)) {
            mkdir($destination, 0777, true);
        }

        $file->move($destination, $filename);

        return $filename;
    }

    /**
     * Delete Attachment
     */
    protected function deleteAttachment(Issue $issue): void
    {
        if (!$issue->attachment) {
            return;
        }

        $file = public_path('uploads/issues/'.$issue->attachment);

        if (file_exists($file)) {
            unlink($file);
        }
    }

    /**
     * Find Issue
     */
    public function find(int $id): Issue
    {
        return Issue::findOrFail($id);
    }

    /**
     * Get Issue List
     */
    public function list(array $filters = [])
    {
        $query = Issue::query();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['priority'])) {
            $query->where('priority_id', $filters['priority']);
        }

        if (!empty($filters['project'])) {
            $query->where('project_id', $filters['project']);
        }

        return $query
            ->with([
                'state',
                'service',
                'project',
                'application',
                'module',
                'category',
                'priority'
            ])
            ->latest()
            ->paginate(20);
    }


/**
 * Auto Assign Engineer
 */
public function assignEngineer(Issue $issue)
{
    $engineer = User::where('role', 'Support Engineer')
        ->where('status', 1)
        ->orderBy('current_ticket_count')
        ->first();

    if (!$engineer) {
        return false;
    }

    $issue->assigned_to = $engineer->id;

    $issue->assigned_at = now();

    $issue->status = 'Assigned';

    $issue->save();

    $engineer->increment('current_ticket_count');

    $this->addHistory(
        $issue,
        'Assigned',
        'Issue assigned to '.$engineer->name
    );

    return true;
}


/**
 * Change Issue Status
 */
public function changeStatus(
    Issue $issue,
    string $status,
    $remarks = null
)
{

    $oldStatus = $issue->status;

    $issue->status = $status;

    if ($status == 'Resolved') {

        $issue->resolved_at = now();

    }

    if ($status == 'Closed') {

        $issue->closed_at = now();

    }

    $issue->save();

    $this->addHistory(

        $issue,

        'Status Changed',

        "Status changed from {$oldStatus} to {$status}. {$remarks}"

    );

}

/**
 * SLA
 */
public function calculateSLA(Issue $issue)
{

    switch ($issue->priority->priority_name) {

        case 'Critical':

            return Carbon::parse($issue->created_at)
                ->addHours(2);

        case 'High':

            return Carbon::parse($issue->created_at)
                ->addHours(4);

        case 'Medium':

            return Carbon::parse($issue->created_at)
                ->addHours(8);

        default:

            return Carbon::parse($issue->created_at)
                ->addDay();

    }

}

/**
 * SLA Breached
 */
public function isSLABreached(Issue $issue)
{
    return now()->greaterThan(
        $this->calculateSLA($issue)
    );
}



/**
 * Activity History
 */
public function addHistory(
    Issue $issue,
    $action,
    $remarks = null
)
{

    IssueHistory::create([

        'issue_id'=>$issue->id,

        'action'=>$action,

        'remarks'=>$remarks,

        'performed_by'=>auth()->id(),

        'performed_at'=>now()

    ]);

}

/**
 * Email Notification
 */
public function notifyEngineer(Issue $issue)
{

    if(!$issue->assigned_to){

        return;

    }

    $user=User::find($issue->assigned_to);

    if(!$user){

        return;

    }

    Mail::raw(

        "Issue ".$issue->ticket_no." assigned to you.",

        function($mail) use($user){

            $mail->to($user->email)

                ->subject("New Issue Assigned");

        }

    );

}


/**
 * Timeline
 */
public function timeline(Issue $issue)
{

    return IssueHistory::

        where('issue_id',$issue->id)

        ->with('user')

        ->latest()

        ->get();

}



/**
 * Close
 */
public function close(Issue $issue)
{

    $issue->status='Closed';

    $issue->closed_at=now();

    $issue->save();

    $this->addHistory(

        $issue,

        'Closed',

        'Ticket Closed'

    );

}



/**
 * Reopen
 */
public function reopen(Issue $issue)
{

    $issue->status='Reopened';

    $issue->save();

    $this->addHistory(

        $issue,

        'Reopened',

        'Ticket Reopened'

    );

}


/**
 * Dashboard
 */
public function dashboard()
{

    return [

        'total'=>Issue::count(),

        'open'=>Issue::where('status','Open')->count(),

        'assigned'=>Issue::where('status','Assigned')->count(),

        'progress'=>Issue::where('status','In Progress')->count(),

        'resolved'=>Issue::where('status','Resolved')->count(),

        'closed'=>Issue::where('status','Closed')->count(),

        'critical'=>Issue::

            whereHas('priority',function($q){

                $q->where('priority_name','Critical');

            })->count(),

    ];

}
}