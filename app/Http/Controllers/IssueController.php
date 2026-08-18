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
use App\Services\IssueRoutingService;
use App\Services\IssueWorkflowService;
use App\Services\IssueSlaService;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreIssueRequest;
use Illuminate\Support\Facades\Schema;


class IssueController extends Controller
{
    public function __construct(
        protected IssueService $issueService,
        protected IssueRoutingService $routingService,
        protected IssueWorkflowService $workflowService)
    {
        // Constructor code if needed
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
        $states = collect();
        $applications = collect();

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

        // Load states / projects / applications based on role and mappings
        try {
            $user = auth()->user();

            $roleIds = collect($user?->roles ?? collect())
                ->pluck('role_id')
                ->map(fn ($roleId) => (int) $roleId)
                ->filter()
                ->unique()
                ->values()
                ->all();

            if (empty($roleIds) && ! empty($user?->user_id)) {
                $roleIds = DB::table('map_user_role')
                    ->where('user_id', $user->user_id)
                    ->where('is_active', 1)
                    ->pluck('role_id')
                    ->map(fn ($roleId) => (int) $roleId)
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();
            }

            $roleNames = [];
            if (! empty($roleIds)) {
                $roleNames = DB::table('mst_role')
                    ->whereIn('role_id', $roleIds)
                    ->pluck('role_name')
                    ->map(fn ($roleName) => strtolower((string) $roleName))
                    ->all();
            } elseif (! empty($user?->role_id)) {
                $roleNames = [strtolower((string) DB::table('mst_role')->where('role_id', $user->role_id)->value('role_name'))];
            }

            $roleText = implode(' ', array_filter($roleNames));
            $isVendorRole = str_contains($roleText, 'vendor admin')
                || str_contains($roleText, 'vendor it')
                || str_contains($roleText, 'vendor');
            $isStateAdmin = str_contains($roleText, 'state admin');
            $isStateIt = str_contains($roleText, 'state it');
            $isStateRole = $isStateAdmin || $isStateIt || str_contains($roleText, 'state');

            $vendorId = Schema::hasColumn('mst_user', 'vendor_id') ? $user->vendor_id : null;

            // States
            $stateQuery = DB::table('mst_state as st')->select('st.state_id', 'st.state_name');
            if (Schema::hasColumn('mst_state', 'is_active')) {
                $stateQuery->where('st.is_active', 1);
            }

            if (($isStateAdmin || $isStateIt) && ! empty($user->state_id)) {
                $stateIds = array_filter(array_map('trim', explode(',', (string) $user->state_id)), fn ($id) => $id !== '');
                if (! empty($stateIds)) {
                    $states = $stateQuery->whereIn('st.state_id', $stateIds)->orderBy('st.state_name')->get();
                } else {
                    $states = collect();
                }
            } elseif ($isVendorRole) {
                if (! empty($vendorId)) {
                    $states = DB::table('map_vendor_state as m')
                        ->join('mst_state as st', 'm.state_id', '=', 'st.state_id')
                        ->select('st.state_id', 'st.state_name')
                        ->where('m.vendor_id', $vendorId)
                        ->where('m.is_active', 1)
                        ->when(Schema::hasColumn('mst_state', 'is_active'), fn ($query) => $query->where('st.is_active', 1))
                        ->distinct()
                        ->orderBy('st.state_name')
                        ->get();
                } else {
                    $states = collect();
                }
            } else {
                $states = $stateQuery->orderBy('st.state_name')->get();
            }

            // Projects: if a state filter is provided, prefer map_project_state mappings
            $projectQuery = DB::table('mst_project as pr')->select('pr.project_id', 'pr.project_name');
            if (Schema::hasColumn('mst_project', 'is_active')) {
                $projectQuery->where('pr.is_active', 1);
            }

            if ($isVendorRole) {
                if (! empty($vendorId)) {
                    $projectQuery->join('map_vendor_state as m', 'pr.project_id', '=', 'm.project_id')
                        ->where('m.vendor_id', $vendorId)
                        ->where('m.is_active', 1)
                        ->distinct();
                    if ($request->filled('state_id')) {
                        $projectQuery->where('m.state_id', $request->input('state_id'));
                    }
                } else {
                    $projectQuery->whereRaw('0 = 1');
                }
            } elseif ($request->filled('state_id')) {
                $stateId = (int) $request->input('state_id');
                $mappedProjectIds = DB::table('map_project_state')
                    ->where('state_id', $stateId)
                    ->where('is_active', 1)
                    ->distinct()
                    ->pluck('project_id')
                    ->filter()
                    ->all();

                if (! empty($mappedProjectIds)) {
                    $projectQuery->whereIn('pr.project_id', $mappedProjectIds);
                } else {
                    $projectQuery->where('pr.state_id', $stateId);
                }
            }

            $projects = $projectQuery->orderBy('pr.project_name')->get();

            // Applications: prefer map_project_application_module mapping when project selected
            $applicationQuery = DB::table('mst_application as a')->select('a.application_id', 'a.application_name');
            if (Schema::hasColumn('mst_application', 'is_active')) {
                $applicationQuery->where('a.is_active', 1);
            }

            if ($request->filled('project_id')) {
                $projectId = (int) $request->input('project_id');

                if ($isVendorRole && ! empty($vendorId)) {
                    $applicationIds = DB::table('map_vendor_state as m')
                        ->where('m.vendor_id', $vendorId)
                        ->where('m.project_id', $projectId)
                        ->where('m.is_active', 1)
                        ->when($request->filled('state_id'), function ($query) use ($request) {
                            return $query->where('m.state_id', $request->input('state_id'));
                        })
                        ->distinct()
                        ->pluck('m.application_id')
                        ->filter()
                        ->all();
                } else {
                    $applicationIds = DB::table('map_project_application_module')
                        ->where('project_id', $projectId)
                        ->where('is_active', 1)
                        ->distinct()
                        ->pluck('application_id')
                        ->filter()
                        ->all();
                }

                if (! empty($applicationIds)) {
                    $applicationQuery->whereIn('a.application_id', $applicationIds);
                } else {
                    $applicationQuery->whereRaw('0 = 1');
                }
            } else {
                $applicationQuery->whereRaw('0 = 1');
            }

            $applications = $applicationQuery->orderBy('a.application_name')->get();

        } catch (Throwable $e) {
            Log::warning('Unable to load states/projects/applications: ' . $e->getMessage());
        }


        return view('issues.index', compact(
            'issues',
            'statuses',
            'priorities',
            'services',
            'projects',
            'states',
            'applications'
        ));
    }



    /**
     * Raise new issue screen.
     */
    public function create()
    {

        // Determine available states based on user roles (Central Admin sees all)
        $user = auth()->user();

        $roleText = '';
        if (! empty($user?->roles)) {
            $roleText = implode(' ', collect($user->roles)->pluck('role_name')->map(fn($r)=>strtolower((string)$r))->all());
        } elseif (! empty($user?->role_id)) {
            $roleText = strtolower((string) DB::table('mst_role')->where('role_id', $user->role_id)->value('role_name'));
        }

        $isStateAdmin = str_contains($roleText, 'state admin');
        $isStateIt = str_contains($roleText, 'state it');
        $isStateRole = $isStateAdmin || $isStateIt || str_contains($roleText, 'state');

        $stateQuery = DB::table('mst_state as st')->select('st.state_id', 'st.state_name');
        if (Schema::hasColumn('mst_state', 'is_active')) {
            $stateQuery->where('st.is_active', 1);
        }

        if (($isStateAdmin || $isStateIt) && ! empty($user->state_id)) {
            $stateIds = array_filter(array_map('trim', explode(',', (string) $user->state_id)), fn ($id) => $id !== '');
            if (! empty($stateIds)) {
                $states = $stateQuery->whereIn('st.state_id', $stateIds)->orderBy('st.state_name')->get();
            } else {
                $states = collect();
            }
        } else {
            $states = $stateQuery->orderBy('st.state_name')->get();
        }


        $services = Service::where('is_active',1)
            ->orderBy('service_name')
            ->get();


        // Do not pre-populate projects/applications/modules — they'll be loaded via AJAX
        $projects = collect();
        $applications = collect();
        $modules = collect();


        

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



    public function createModalPopup()
    {

        // Determine available states based on user roles (Central Admin sees all)
        $user = auth()->user();

        $roleText = '';
        if (! empty($user?->roles)) {
            $roleText = implode(' ', collect($user->roles)->pluck('role_name')->map(fn($r)=>strtolower((string)$r))->all());
        } elseif (! empty($user?->role_id)) {
            $roleText = strtolower((string) DB::table('mst_role')->where('role_id', $user->role_id)->value('role_name'));
        }

        $isStateAdmin = str_contains($roleText, 'state admin');
        $isStateIt = str_contains($roleText, 'state it');
        $isStateRole = $isStateAdmin || $isStateIt || str_contains($roleText, 'state');

        $stateQuery = DB::table('mst_state as st')->select('st.state_id', 'st.state_name');
        if (Schema::hasColumn('mst_state', 'is_active')) {
            $stateQuery->where('st.is_active', 1);
        }

        if (($isStateAdmin || $isStateIt) && ! empty($user->state_id)) {
            $stateIds = array_filter(array_map('trim', explode(',', (string) $user->state_id)), fn ($id) => $id !== '');
            if (! empty($stateIds)) {
                $states = $stateQuery->whereIn('st.state_id', $stateIds)->orderBy('st.state_name')->get();
            } else {
                $states = collect();
            }
        } else {
            $states = $stateQuery->orderBy('st.state_name')->get();
        }


        $services = Service::where('is_active',1)
            ->orderBy('service_name')
            ->get();


        // Do not pre-populate projects/applications/modules — they'll be loaded via AJAX
        $projects = collect();
        $applications = collect();
        $modules = collect();


        

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


        return view('issues.create-popup',compact('states','services','projects','applications',
                'modules',
                'issueCategories',
                'priorities'
            )
        );
    }
/* by srinivas*/

    /**
     * Return projects for a given state (AJAX)
     */
    public function projectsByState(Request $request): JsonResponse
    {
        $request->validate([
            'state_id' => ['required','integer','exists:mst_state,state_id'],
        ]);

        // Prefer using map_project_state mapping if present
        $mappedProjectIds = DB::table('map_project_state')
            ->where('state_id', $request->input('state_id'))
            ->where('is_active', 1)
            ->distinct()
            ->pluck('project_id')
            ->filter()
            ->all();

        $projectQuery = DB::table('mst_project as pr')->select('pr.project_id', 'pr.project_name');
        if (Schema::hasColumn('mst_project', 'is_active')) {
            $projectQuery->where('pr.is_active', 1);
        }

        if (! empty($mappedProjectIds)) {
            $projectQuery->whereIn('pr.project_id', $mappedProjectIds);
        } else {
            // fallback to projects whose state_id matches
            $projectQuery->where('pr.state_id', $request->input('state_id'));
        }

        $projects = $projectQuery->orderBy('pr.project_name')->get();

        return response()->json($projects);
    }


    /**
     * Return applications for a given project (AJAX)
     */
    public function applicationsByProject(Request $request): JsonResponse
    {
        $request->validate([
            'project_id' => ['required','integer','exists:mst_project,project_id'],
        ]);

        $applicationIds = DB::table('map_project_application_module')
            ->where('project_id', $request->input('project_id'))
            ->where('is_active', 1)
            ->distinct()
            ->pluck('application_id')
            ->filter()
            ->all();

        if (empty($applicationIds)) {
            return response()->json([]);
        }

        $applicationQuery = DB::table('mst_application as a')->select('a.application_id', 'a.application_name')
            ->whereIn('a.application_id', $applicationIds);
        if (Schema::hasColumn('mst_application', 'is_active')) {
            $applicationQuery->where('a.is_active', 1);
        }

        $applications = $applicationQuery->orderBy('a.application_name')->get();

        return response()->json($applications);
    }


    /**
     * Return modules for a given project+application (AJAX)
     */
    public function modulesByApplication(Request $request): JsonResponse
    {
        $request->validate([
            'application_id' => ['required','integer','exists:mst_application,application_id'],
            'project_id' => ['nullable','integer','exists:mst_project,project_id'],
        ]);

        $mappingQuery = DB::table('map_project_application_module')->where('application_id', $request->input('application_id'))
            ->where('is_active', 1);

        if ($request->filled('project_id')) {
            $mappingQuery->where('project_id', $request->input('project_id'));
        }

        $moduleIds = $mappingQuery->distinct()->pluck('module_id')->filter()->all();

        if (empty($moduleIds)) {
            return response()->json([]);
        }

        $moduleQuery = DB::table('mst_module as m')->select('m.module_id', 'm.module_name')
            ->whereIn('m.module_id', $moduleIds);
        if (Schema::hasColumn('mst_module', 'is_active')) {
            $moduleQuery->where('m.is_active', 1);
        }

        $modules = $moduleQuery->orderBy('m.module_name')->get();

        return response()->json($modules);
    }



    /**
     * Store issue.
     */
    public function store(StoreIssueRequest $request, IssueRoutingService $routingService): RedirectResponse|JsonResponse
    {
        Log::info('╔════════════════════════════════════════════════╗');
        Log::info('║ ISSUE CREATE ROUTE CALLED - /issues/create    ║');
        Log::info('╚════════════════════════════════════════════════╝');
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
                $payload = [
                    'success' => true,
                    'message' => 'Issue raised successfully.',
                    'issue' => $issue,
                ];

                return response()->json($payload, 200);
            }

            Log::info('✓ ISSUE CREATION SUCCESSFUL IN CONTROLLER');
            Log::info('Redirecting to issues.show page with issue details');
            return redirect()
                ->route('issues.show', $issue)
                ->with('success', 'Issue raised successfully. Issue Number: ' . $issue->issue_number);

        } catch (Throwable $e) {
            #report($e);

            Log::error('╔════════════════════════════════════════════════╗');
            Log::error('║ ✗ ISSUE CREATE FAILED IN CONTROLLER            ║');
            Log::error('╚════════════════════════════════════════════════╝');
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
            'state',
            'project',
            'service',
            'application',
            'module',
            'category',
            'priority',
            'status',
            'raisedBy',
            'currentOwner',
            'currentAssignee',
            'team',
            'configuration',
            //'sla',
            'updates',
            'statusHistory',
            'histories',
            'attachments',
            'escalations',
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


            $this->workflowService->startWork(
                $issue,
                Auth::id()
            );

            return back()->with(
                'success',
                'Issue moved to In Progress.'
            );


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
                'min:5',
                'max:2000',
            ],
        ]);


        try {


         $this->workflowService->requestInformation(
                $issue,
                $request->message,
                Auth::id()
            );

            return back()->with(
                'success',
                'Information request submitted.'
            );

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

    public function escalateVendor(Request $request,Issue $issue)
    {

    $request->validate([
            'reason' => [
                'required',
                'string',
                'min:5',
                'max:2000',
            ],
        ]);
        try {
            
        $this->workflowService->escalateToVendor(
                $issue,
                $request->reason,
                Auth::id()
            );

            return back()->with(
                'success',
                'Issue escalated successfully.'
            );
            
            // DB::transaction(function () use ($issue) {

            //     /*
            //     |--------------------------------------------------------------------------
            //     | IMPORTANT
            //     |--------------------------------------------------------------------------
            //     |
            //     | Your existing IssueRoutingService should be called here.
            //     |
            //     */

            //     if (class_exists(
            //         \App\Services\IssueRoutingService::class
            //     )) {

            //         app(
            //             \App\Services\IssueRoutingService::class
            //         )->route($issue);
            //     }

            // });


            // return response()->json([
            //     'success' => true,
            //     'message' => 'Issue escalated successfully.',
            // ]);

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
                'min:10',
                'max:5000',
            ],
        ]);


        try {

        $this->workflowService->submitResolution(
                $issue,
                $request->resolution_summary,
                Auth::id()
            );

            return back()->with(
                'success',
                'Resolution submitted successfully.'
            );

            // DB::transaction(function () use (
            //     $request,
            //     $issue
            // ) {

            //     $statusId =
            //         $this->getStatusId('Resolved');

            //     if ($statusId) {

            //         $issue->status_id =
            //             $statusId;
            //     }

            //     $issue->resolution_summary =
            //         $request->resolution_summary;

            //     $issue->resolved_by =
            //         Auth::id();

            //     $issue->resolved_at =
            //         now();

            //     $issue->save();

            // });


            // return response()->json([
            //     'success' => true,
            //     'message' => 'Resolution submitted successfully.',
            // ]);

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


    private function normalizeAttachmentRelativePath(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        $normalized = str_replace('\\', '/', $path);
        $normalized = preg_replace('#^https?://[^/]+#', '', $normalized) ?? $normalized;
        $normalized = preg_replace('#^/+(public|storage)/#', '', $normalized);
        $normalized = preg_replace('#^app/(public/)?#', '', $normalized);
        $normalized = ltrim($normalized, '/');

        if ($normalized === '') {
            return null;
        }

        return $normalized;
    }

    private function resolveAttachmentFilePath(object $attachment): ?string
    {
        $pathCandidates = [];

        $basePath = $this->normalizeAttachmentRelativePath($attachment->file_path ?? null);
        if ($basePath) {
            $pathCandidates[] = $basePath;
        }

        if (!empty($attachment->stored_file_name)) {
            $pathCandidates[] = 'issues/' . ($attachment->issue_id ?? 0) . '/' . $attachment->stored_file_name;
            $pathCandidates[] = 'issue_attachments/' . ($attachment->issue_id ?? 0) . '/' . $attachment->stored_file_name;
            $pathCandidates[] = $attachment->stored_file_name;
        }

        $pathCandidates = array_values(array_unique(array_filter($pathCandidates, fn ($value) => !blank($value))));

        foreach ($pathCandidates as $candidate) {
            $diskPath = storage_path('app/public/' . ltrim($candidate, '/'));
            $fallbackDiskPath = storage_path('app/' . ltrim($candidate, '/'));

            foreach ([$diskPath, $fallbackDiskPath] as $checkPath) {
                if (is_file($checkPath)) {
                    return $checkPath;
                }
            }
        }

        return null;
    }

    private function getAttachmentResponseFile(string $filePath): string
    {
        $handle = fopen($filePath, 'rb');
        $header = fread($handle, 3);
        fclose($handle);

        if ($header !== "\xEF\xBB\xBF") {
            return $filePath;
        }

        $contents = file_get_contents($filePath);
        if ($contents === false) {
            return $filePath;
        }

        $contents = preg_replace('/^\xEF\xBB\xBF/', '', $contents);
        if ($contents === null) {
            return $filePath;
        }

        $tempPath = tempnam(sys_get_temp_dir(), 'attachment_');
        if ($tempPath === false) {
            return $filePath;
        }

        file_put_contents($tempPath, $contents);

        return $tempPath;
    }

    /*
    |--------------------------------------------------------------------------
    | Format Issue
    |--------------------------------------------------------------------------
    */

    private function formatIssue(Issue $issue): array
    {

    #$sla = $this->calculateSla($issue);

        $attachments = collect($issue->attachments ?? [])
            ->map(function ($attachment) {
                $attachmentId = $attachment->attachment_id ?? null;
                $fileName = $attachment->original_file_name ?? $attachment->stored_file_name ?? 'Attachment';
                $resolvedPath = $this->resolveAttachmentFilePath($attachment);
                $relativePath = $this->normalizeAttachmentRelativePath($attachment->file_path ?? null);

                return [
                    'attachment_id' => $attachmentId,
                    'issue_id' => $attachment->issue_id ?? null,
                    'original_file_name' => $fileName,
                    'stored_file_name' => $attachment->stored_file_name ?? basename((string) $relativePath),
                    'file_path' => $attachment->file_path ?? ($relativePath ? '/storage/' . $relativePath : null),
                    'file_size' => $attachment->file_size ?? null,
                    'file_type' => $attachment->file_type ?? null,
                    'uploaded_at' => $attachment->uploaded_at ?? null,
                    'exists' => $resolvedPath !== null,
                    'view_url' => $attachmentId ? route('attachment.view', ['id' => $attachmentId]) : null,
                    'preview_url' => $attachmentId ? route('attachment.preview', ['id' => $attachmentId]) : null,
                    'download_url' => $attachmentId ? route('attachment.download', ['id' => $attachmentId]) : null,
                ];
            })
            ->values()
            ->all();

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

            'attachments' =>
                $attachments,

            //     'sla_remaining_minutes' =>
            //     $sla['sla_remaining_minutes'],

            // 'sla_remaining_label' =>
            //     $sla['sla_remaining_label'],

        ];
    }




    public function addUpdate(
        Request $request,
        Issue $issue
    ) {

        $request->validate([
            'message' => [
                'required',
                'string',
                'min:2',
                'max:5000',
            ],
        ]);

        $issue->updates()->create([
            'update_type' => 'General Update',
            'update_message' => $request->message,
            'created_by' => Auth::id(),
            'created_at' => now(),
        ]);

        return back()->with(
            'success',
            'Issue update added.'
        );
    }


    public function uploadAttachment(
        Request $request,
        Issue $issue
    ) {

        $request->validate([
            'attachment' => [
                'required',
                'file',
                'max:10240',
                'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx,txt',
            ],
        ]);

        $file = $request->file('attachment');

        try {
            \Log::info('Starting attachment upload', [
                'issue_id' => $issue->issue_id,
                'file_name' => $file->getClientOriginalName(),
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType()
            ]);

            $path = $file->store(
                'issues/' . $issue->issue_id,
                'public'
            );

            if (!$path) {
                throw new \Exception('File store returned empty path');
            }

            \Log::info('File stored successfully', [
                'path' => $path,
                'full_path' => storage_path('app/public/' . $path)
            ]);

            $issue->attachments()->create([
                'original_file_name' => $file->getClientOriginalName(),
                'stored_file_name' => basename($path),
                'file_path' => '/storage/' . $path,
                'file_size' => $file->getSize(),
                'file_type' => $file->getMimeType(),
                'user_id' => Auth::id(),
                'uploaded_at' => now(),
            ]);

            \Log::info('Attachment record created successfully', [
                'issue_id' => $issue->issue_id,
                'file_path' => '/storage/' . $path
            ]);

            return back()->with(
                'success',
                'Attachment uploaded successfully.'
            );
        } catch (\Throwable $e) {
            \Log::error('Attachment upload failed', [
                'issue_id' => $issue->issue_id,
                'file_name' => $file->getClientOriginalName(),
                'error' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            return back()->with(
                'error',
                'Failed to upload attachment: ' . $e->getMessage()
            );
        }
    }

    /**
     * Preview attachment in browser
     */
    public function previewAttachment($id)
    {
        $attachment = DB::table('txn_issue_attachment')
            ->where('attachment_id', $id)
            ->first();

        if (!$attachment) {
            \Log::error('Preview attachment request failed - attachment record missing', [
                'attachment_id' => $id,
            ]);
            return abort(404, 'Attachment not found');
        }

        $filePath = $this->resolveAttachmentFilePath($attachment);

        \Log::info('Preview attachment request', [
            'attachment_id' => $id,
            'issue_id' => $attachment->issue_id ?? null,
            'stored_path' => $attachment->file_path ?? null,
            'stored_file_name' => $attachment->stored_file_name ?? null,
            'original_file_name' => $attachment->original_file_name ?? null,
            'resolved_path' => $filePath,
            'file_exists' => $filePath ? file_exists($filePath) : false,
        ]);

        if (!$filePath || !file_exists($filePath)) {
            \Log::error('Attachment file not found during preview', [
                'attachment_id' => $id,
                'issue_id' => $attachment->issue_id ?? null,
                'stored_path' => $attachment->file_path ?? null,
                'stored_file_name' => $attachment->stored_file_name ?? null,
                'original_file_name' => $attachment->original_file_name ?? null,
                'resolved_path' => $filePath,
            ]);
            return abort(404, 'File not found');
        }

        $relativePath = $this->normalizeAttachmentRelativePath($attachment->file_path ?? $attachment->stored_file_name ?? null);
        if (empty($relativePath)) {
            $relativePath = basename($filePath);
        }

        // Get file extension and determine type
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        
        // Use the app route that serves the file with the correct inline headers.
        // This works even when the public/storage symlink is missing.
        $publicUrl = route('attachment.view', ['id' => $id]);

        // Determine if we can preview this type
        $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif']);
        $isPdf = $ext === 'pdf';
        $isText = in_array($ext, ['txt', 'log']);
        $isOffice = in_array($ext, ['doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx']);

        \Log::info('Previewing attachment', [
            'attachment_id' => $id,
            'file_name' => $attachment->original_file_name,
            'extension' => $ext,
            'is_image' => $isImage,
            'is_pdf' => $isPdf,
            'is_text' => $isText,
            'is_office' => $isOffice
        ]);

        $data = [
            'attachment' => $attachment,
            'filePath' => $filePath,
            'publicUrl' => $publicUrl,
            'extension' => $ext,
            'isImage' => $isImage,
            'isPdf' => $isPdf,
            'isText' => $isText,
            'isOffice' => $isOffice,
            'downloadUrl' => route('attachment.download', ['id' => $id]),
        ];

        // Load and display as plain text if text file
        if ($isText) {
            $content = file_get_contents($filePath);
            $data['content'] = $content;
        }

        return view('attachments.preview', $data);
    }

    /**
     * View attachment file inline (for PDFs, images, etc.)
     */
    public function viewAttachment($id)
    {
        $attachment = DB::table('txn_issue_attachment')
            ->where('attachment_id', $id)
            ->first();

        if (!$attachment) {
            \Log::error('View attachment request failed - attachment record missing', [
                'attachment_id' => $id,
            ]);
            return abort(404, 'Attachment not found');
        }

        $filePath = $this->resolveAttachmentFilePath($attachment);

        \Log::info('View attachment request', [
            'attachment_id' => $id,
            'issue_id' => $attachment->issue_id ?? null,
            'stored_path' => $attachment->file_path ?? null,
            'stored_file_name' => $attachment->stored_file_name ?? null,
            'original_file_name' => $attachment->original_file_name ?? null,
            'resolved_path' => $filePath,
            'file_exists' => $filePath ? file_exists($filePath) : false,
        ]);

        if (!$filePath || !file_exists($filePath)) {
            \Log::error('Attachment file not found during view', [
                'attachment_id' => $id,
                'issue_id' => $attachment->issue_id ?? null,
                'stored_path' => $attachment->file_path ?? null,
                'stored_file_name' => $attachment->stored_file_name ?? null,
                'original_file_name' => $attachment->original_file_name ?? null,
                'resolved_path' => $filePath,
                'file_exists' => false
            ]);
            return abort(404, 'File not found');
        }

        // Get file extension
        $ext = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
        $isViewable = in_array($ext, ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'txt']);

        if ($isViewable) {
            // For viewable files, return with inline disposition
            $mimeType = $attachment->file_type;
            
            if (!$mimeType || $mimeType === 'application/octet-stream') {
                $mimeTypes = [
                    'pdf' => 'application/pdf',
                    'jpg' => 'image/jpeg',
                    'jpeg' => 'image/jpeg',
                    'png' => 'image/png',
                    'gif' => 'image/gif',
                    'txt' => 'text/plain',
                ];
                $mimeType = $mimeTypes[$ext] ?? 'application/octet-stream';
            }

            \Log::info('Viewing attachment', [
                'attachment_id' => $id,
                'file_name' => $attachment->original_file_name,
                'mime_type' => $mimeType,
                'file_path' => $filePath
            ]);

            $responseFile = $this->getAttachmentResponseFile($filePath);

            return response()->file($responseFile, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="' . $attachment->original_file_name . '"',
                'Cache-Control' => 'public, max-age=3600',
            ]);
        } else {
            // For non-viewable files (doc, docx, xls, xlsx), force download
            return response()->download($filePath, $attachment->original_file_name);
        }
    }

    /**
     * Download attachment file
     */
    public function downloadAttachment($id)
    {
        $attachment = DB::table('txn_issue_attachment')
            ->where('attachment_id', $id)
            ->first();

        if (!$attachment) {
            \Log::error('Download attachment request failed - attachment record missing', [
                'attachment_id' => $id,
            ]);
            return abort(404, 'Attachment not found');
        }

        $filePath = $this->resolveAttachmentFilePath($attachment);

        \Log::info('Download attachment request', [
            'attachment_id' => $id,
            'issue_id' => $attachment->issue_id ?? null,
            'stored_path' => $attachment->file_path ?? null,
            'stored_file_name' => $attachment->stored_file_name ?? null,
            'original_file_name' => $attachment->original_file_name ?? null,
            'resolved_path' => $filePath,
            'file_exists' => $filePath ? file_exists($filePath) : false,
        ]);

        if (!$filePath || !file_exists($filePath)) {
            \Log::error('Attachment file not found during download', [
                'attachment_id' => $id,
                'issue_id' => $attachment->issue_id ?? null,
                'stored_path' => $attachment->file_path ?? null,
                'stored_file_name' => $attachment->stored_file_name ?? null,
                'original_file_name' => $attachment->original_file_name ?? null,
                'resolved_path' => $filePath,
                'file_exists' => false
            ]);
            return abort(404, 'File not found');
        }

        $responseFile = $this->getAttachmentResponseFile($filePath);

        return response()->download($responseFile, $attachment->original_file_name ?? basename($filePath));
    }

}