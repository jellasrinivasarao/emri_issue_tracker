<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Throwable;

use App\Models\Issue;
use App\Models\SupportTeam;
use App\Models\IssueRoutingConfiguration;
use App\Models\State;
use App\Models\Service;
use App\Models\Project;
use App\Models\Application;
use App\Models\Priority;
use App\Models\IssueCategory;
use App\Models\Module;

use App\Services\IssueService;
#use App\Services\IssueRoutingService;
use App\Services\IssueRouting\IssueRoutingService;

use App\Services\IssueSlaService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;


use App\Http\Requests\StoreIssueRequest;

class IssueController extends Controller
{
    public function __construct(protected IssueService $issueService,protected IssueRoutingService $routingService) {
    }



    // public function modal()
    // {
    //     $states = State::where('is_active',1)
    //         ->orderBy('state_name')
    //         ->get();

    //     $services = Service::where('is_active',1)
    //         ->orderBy('service_name')
    //         ->get();

    //     $projects = Project::where('is_active',1)
    //         ->orderBy('project_name')
    //         ->get();

    //     $applications = Application::where('is_active',1)
    //         ->orderBy('application_name')
    //         ->get();

    //     $issueCategories = IssueCategory::where('is_active',1)
    //         ->orderBy('category_name')
    //         ->get();

    //     $priorities = Priority::where('is_active',1)
    //         ->orderBy('priority_name')
    //         ->get();

    //     return view('issues.partials.raise-issue-form', [
    //         'states'          => $states,
    //         'services'        => $services,
    //         'projects'        => $projects,
    //         'applications'    => $applications,
    //         'issueCategories' => $issueCategories,
    //         'priorities'      => $priorities,
    //     ]);
    // }


    /**
     * Issue listing.
     */
    public function index1(Request $request)
    {
        $query = Issue::query()
            ->with([
                'configuration',
                'team'
            ]);


        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where(
                    'issue_number',
                    'LIKE',
                    "%{$search}%"
                );

                $q->orWhere(
                    'subject',
                    'LIKE',
                    "%{$search}%"
                );

            });
        }


        if ($request->filled('status')) {

            $query->where(
                'status',
                $request->status
            );
        }


        if ($request->filled('priority')) {

            $query->where(
                'priority',
                $request->priority
            );
        }


        // if (Auth::check()) {

        //     $query->where(
        //         'created_by',
        //         Auth::id()
        //     );
        // }


        $issues = $query
            ->orderByDesc('issue_id')
            ->paginate(25)
            ->withQueryString();


        return view('issues.index',compact('issues')
        );
    }


    public function index(Request $request)
    {
                $query = Issue::query();

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where('issue_number', 'LIKE', "%{$search}%")
                    ->orWhere('issue_title', 'LIKE', "%{$search}%")
                    ->orWhere('issue_description', 'LIKE', "%{$search}%");

            });
        }

        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        if ($request->filled('status_id')) {
            $query->where(
                'status_id',
                $request->status_id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Priority
        |--------------------------------------------------------------------------
        */

        if ($request->filled('priority_id')) {
            $query->where(
                'priority_id',
                $request->priority_id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Service
        |--------------------------------------------------------------------------
        */

        if ($request->filled('service_id')) {
            $query->where(
                'service_id',
                $request->service_id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Project
        |--------------------------------------------------------------------------
        */

        if ($request->filled('project_id')) {
            $query->where(
                'project_id',
                $request->project_id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $issues = $query
            ->orderByDesc('issue_id')
            ->paginate(10)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Filter Masters
        |--------------------------------------------------------------------------
        |
        | These use your existing models.
        |
        */

        $statuses = collect();
        $priorities = collect();
        $services = collect();
        $projects = collect();

        /*
        |--------------------------------------------------------------------------
        | Load masters safely
        |--------------------------------------------------------------------------
        */

        try {

            if (class_exists(\App\Models\IssueStatus::class)) {

                $statuses = \App\Models\IssueStatus::query()
                    ->where('is_active', 1)
                    ->orderBy('status_name')
                    ->get();

            }

        } catch (Throwable $e) {
            Log::warning('Unable to load issue statuses: ' . $e->getMessage());
        }


        try {

            if (class_exists(\App\Models\Priority::class)) {

                $priorities = \App\Models\Priority::query()
                    ->where('is_active', 1)
                    ->orderBy('priority_name')
                    ->get();

            }

        } catch (Throwable $e) {
            Log::warning('Unable to load priorities: ' . $e->getMessage());
        }


        try {

            if (class_exists(\App\Models\Service::class)) {

                $services = \App\Models\Service::query()
                    ->where('is_active', 1)
                    ->orderBy('service_name')
                    ->get();

            }

        } catch (Throwable $e) {
            Log::warning('Unable to load services: ' . $e->getMessage());
        }


        try {

            if (class_exists(\App\Models\Project::class)) {

                $projects = \App\Models\Project::query()
                    ->where('is_active', 1)
                    ->orderBy('project_name')
                    ->get();

            }

        } catch (Throwable $e) {
            Log::warning('Unable to load projects: ' . $e->getMessage());
        }


        return view('issues.index', compact(
            'issues',
            'statuses',
            'priorities',
            'services',
            'projects'
        ));
    }



    /**
     * Raise new issue screen.
     */
    public function create()
    {

        $states = State::where('is_active',1)
            ->orderBy('state_name')
            ->get();


        $services = Service::where('is_active',1)
            ->orderBy('service_name')
            ->get();


        $projects = Project::where('is_active',1)
            ->orderBy('project_name')
            ->get();


        $applications = Application::where('is_active',1)
            ->orderBy('application_name')
            ->get();


        $modules = Module::where('is_active',1)
            ->orderBy('module_name')
            ->get();

        $issueCategories = IssueCategory::where('is_active',1)
            ->orderBy('category_name')
            ->get();

        $priorities = Priority::where('is_active',1)
            ->orderBy('priority_name')
            ->get();


        // $issueCategories = [
        //     'Application Issue',
        //     'Infrastructure Issue',
        //     'Network Issue',
        //     'Access Issue',
        //     'Data Issue',
        //     'Hardware Issue',
        //     'Other',
        // ];


        // $priorities = [
        //     'LOW',
        //     'MEDIUM',
        //     'HIGH',
        //     'CRITICAL',
        // ];

        #var_dump($services);


        return view('issues.create',compact('states','services','projects','applications',
                'modules',
                'issueCategories',
                'priorities'
            )
        );
    }



    /**
     * Store issue.
     */
    public function store(StoreIssueRequest $request, IssueRoutingService $routingService): RedirectResponse|JsonResponse
    {
        Log::info("Raise Issue Request >>>>", ['response' => json_encode($request->all())]);

        try {

            
            $issue = $this->issueService->create($request->validated(),$request->file('attachment'));

            if($issue){
                 $routingService->route($issue);
            }
            
            /**
             *  To Enable SLA Config
             */
            
            // $issueSlaService = app(IssueSlaService::class);
            // $issueSlaService->createForIssue($issue);

            Log::info('Has File', [
                'hasFile' => $request->hasFile('attachment'),
                'file' => $request->file('attachment'),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Issue raised successfully.',
                    'issue' => $issue,
                ], 200);
            }

            return redirect()
                ->route('issues.show', $issue)
                ->with('success', 'Issue raised successfully. Issue Number: ' . $issue->issue_number);

        } catch (Throwable $e) {
            #report($e);

            Log::error('Issue create failed', [
                'message' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to raise issue. Please try again.',
                    'error' => $e->getMessage(),
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Unable to raise issue. Please try again.');
        }

    }





    /**
     * Show issue.
     */
    public function show(Issue $issues)
    {

            try {

            $issues->load([
                'service',
                'project',
                'application',
                'module',
                'priority',
                'status',
            ]);

             $teams = SupportTeam::where('is_active',1)
            ->orderBy('support_level')
            ->orderBy('team_name')
            ->get();

            return response()->json([
                'success' => true,
                'issue' => $this->formatIssue($issues),
            ]);

        } catch (Throwable $e) {

            Log::error(
                'Issue details failed',
                [
                    'issue_id' => $issues->issue_id,
                    'error' => $e->getMessage(),
                ]
            );

            return response()->json([
                'success' => false,
                'message' => 'Unable to load issue details.',
            ], 500);
        }

    }





    /**
     * Update issue.
     */
    public function update(Request $request, Issue $issue)
    {

        $validated = $request->validate([


            'issue_category'=>[
                'nullable',
                'string',
                'max:100'
            ],


            'issue_type'=>[
                'nullable',
                'string',
                'max:100'
            ],


            'subject'=>[
                'required',
                'string',
                'max:255'
            ],


            'description'=>[
                'nullable',
                'string'
            ],


            'priority'=>[
                'required',
                Rule::in([
                    'LOW',
                    'MEDIUM',
                    'HIGH',
                    'CRITICAL'
                ])
            ]

        ]);



        $this->issueService->update(
            $issue,
            $validated
        );



        return back()->with(
            'success',
            'Issue updated successfully.'
        );

    }





    public function assign(Request $request, Issue $issue)
    {

        $validated = $request->validate([

            'support_team_id'=>[
                'required',
                'integer'
            ],


            'remarks'=>[
                'nullable',
                'string',
                'max:1000'
            ]

        ]);



        $this->routingService->manuallyRoute(
            $issue,
            $validated['support_team_id'],
            $validated['remarks'] ?? null
        );



        return back()->with(
            'success',
            'Issue assigned successfully.'
        );

    }





    public function resolve(Request $request, Issue $issue)
    {

        $this->issueService->resolve(
            $issue,
            $request->input('remarks')
        );


        return back()->with(
            'success',
            'Issue resolved successfully.'
        );

    }





    public function close(Request $request, Issue $issue)
    {

        $this->issueService->close(
            $issue,
            $request->input('remarks')
        );


        return back()->with(
            'success',
            'Issue closed successfully.'
        );

    }



    /*
    |--------------------------------------------------------------------------
    | Start Work
    |--------------------------------------------------------------------------
    */

    public function startWork(Issue $issue)
    {
        try {

            DB::transaction(function () use ($issue) {

                /*
                |--------------------------------------------------------------------------
                | Change status to In Progress
                |--------------------------------------------------------------------------
                |
                | Your status table should contain:
                |
                | In Progress
                |
                */

                $statusId = $this->getStatusId('In Progress');

                if ($statusId) {

                    $issue->status_id = $statusId;
                }

                /*
                |--------------------------------------------------------------------------
                | Current User
                |--------------------------------------------------------------------------
                */

                if (Auth::check()) {

                    $issue->current_owner_user_id =
                        Auth::id();

                }

                $issue->save();

            });


            return response()->json([
                'success' => true,
                'message' => 'Work started successfully.',
            ]);

        } catch (Throwable $e) {

            Log::error(
                'Start work failed',
                [
                    'issue_id' => $issue->issue_id,
                    'error' => $e->getMessage(),
                ]
            );

            return response()->json([
                'success' => false,
                'message' => 'Unable to start work.',
            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Request Information
    |--------------------------------------------------------------------------
    */

    public function requestInformation(
        Request $request,
        Issue $issue
    ) {

        $request->validate([
            'message' => [
                'required',
                'string',
                'max:2000',
            ],
        ]);


        try {

            DB::transaction(function () use (
                $request,
                $issue
            ) {

                $statusId =
                    $this->getStatusId('Pending');

                if ($statusId) {

                    $issue->status_id =
                        $statusId;
                }

                $issue->save();

                /*
                |--------------------------------------------------------------------------
                | Add update record here
                |--------------------------------------------------------------------------
                |
                | Example:
                |
                | IssueUpdate::create(...)
                |
                | Keep this section for your existing
                | issue update/history table.
                |
                */

            });


            return response()->json([
                'success' => true,
                'message' => 'Information request submitted.',
            ]);

        } catch (Throwable $e) {

            Log::error(
                'Request information failed',
                [
                    'issue_id' => $issue->issue_id,
                    'error' => $e->getMessage(),
                ]
            );

            return response()->json([
                'success' => false,
                'message' => 'Unable to request information.',
            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Escalate Vendor
    |--------------------------------------------------------------------------
    */

    public function escalateVendor(Issue $issue)
    {
        try {

            DB::transaction(function () use ($issue) {

                /*
                |--------------------------------------------------------------------------
                | IMPORTANT
                |--------------------------------------------------------------------------
                |
                | Your existing IssueRoutingService should be called here.
                |
                */

                if (class_exists(
                    \App\Services\IssueRoutingService::class
                )) {

                    app(
                        \App\Services\IssueRoutingService::class
                    )->route($issue);
                }

            });


            return response()->json([
                'success' => true,
                'message' => 'Issue escalated successfully.',
            ]);

        } catch (Throwable $e) {

            Log::error(
                'Vendor escalation failed',
                [
                    'issue_id' => $issue->issue_id,
                    'error' => $e->getMessage(),
                ]
            );

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Submit Resolution
    |--------------------------------------------------------------------------
    */

    public function submitResolution(
        Request $request,
        Issue $issue
    ) {

        $request->validate([
            'resolution_summary' => [
                'required',
                'string',
                'max:5000',
            ],
        ]);


        try {

            DB::transaction(function () use (
                $request,
                $issue
            ) {

                $statusId =
                    $this->getStatusId('Resolved');

                if ($statusId) {

                    $issue->status_id =
                        $statusId;
                }

                $issue->resolution_summary =
                    $request->resolution_summary;

                $issue->resolved_by =
                    Auth::id();

                $issue->resolved_at =
                    now();

                $issue->save();

            });


            return response()->json([
                'success' => true,
                'message' => 'Resolution submitted successfully.',
            ]);

        } catch (Throwable $e) {

            Log::error(
                'Submit resolution failed',
                [
                    'issue_id' => $issue->issue_id,
                    'error' => $e->getMessage(),
                ]
            );

            return response()->json([
                'success' => false,
                'message' => 'Unable to submit resolution.',
            ], 500);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Status Helper
    |--------------------------------------------------------------------------
    */

    private function getStatusId(string $statusName): ?int
    {
        try {

            if (!class_exists(
                \App\Models\IssueStatus::class
            )) {

                return null;
            }


            $status =
                \App\Models\IssueStatus::query()
                    ->whereRaw(
                        'LOWER(status_name) = ?',
                        [
                            strtolower($statusName)
                        ]
                    )
                    ->first();


            return $status?->status_id;

        } catch (Throwable $e) {

            return null;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Format Issue
    |--------------------------------------------------------------------------
    */

    private function formatIssue(Issue $issue): array
    {
        return [

            'issue_id' =>
                $issue->issue_id,

            'issue_number' =>
                $issue->issue_number,

            'issue_title' =>
                $issue->issue_title,

            'issue_description' =>
                $issue->issue_description,

            'state' =>
                $issue->state ?? null,

            'service_id' =>
                $issue->service_id,

            'service_name' =>
                $issue->service?->service_name
                    ?? $issue->service?->name
                    ?? $issue->service_id,

            'project_id' =>
                $issue->project_id,

            'project_name' =>
                $issue->project?->project_name
                    ?? $issue->project?->name
                    ?? $issue->project_id,

            'application_id' =>
                $issue->application_id,

            'application_name' =>
                $issue->application?->application_name
                    ?? $issue->application?->name
                    ?? $issue->application_id,

            'module_id' =>
                $issue->module_id,

            'module_name' =>
                $issue->module?->module_name
                    ?? $issue->module?->name
                    ?? $issue->module_id,

            'priority_id' =>
                $issue->priority_id,

            'priority_name' =>
                $issue->priority?->priority_name
                    ?? $issue->priority?->name
                    ?? '-',

            'status_id' =>
                $issue->status_id,

            'status_name' =>
                $issue->status?->status_name
                    ?? $issue->status?->name
                    ?? '-',

            'raised_at' =>
                optional($issue->raised_at)
                    ->format('d M Y H:i'),

            'resolved_at' =>
                optional($issue->resolved_at)
                    ->format('d M Y H:i'),

            'owner_user_id' =>
                $issue->current_owner_user_id,

            'resolution_summary' =>
                $issue->resolution_summary,

        ];
    }

}