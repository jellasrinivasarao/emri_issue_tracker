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
        $issues = Issue::with([
                'state',
                'service',
                'project',
                'application',
                'module',
                'category',
                'priority'
            ])
            ->latest()
            ->paginate(15);
        return view('issues.index',compact('issues'));
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
    public function store(StoreIssueRequest $request): RedirectResponse|JsonResponse
    {
        Log::info("Raise Issue Request >>>>", ['response' => json_encode($request->all())]);

        try {

            
            $issue = $this->issueService->create($request->validated());

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
    public function show(Issue $issue)
    {

        $issue->load([
            'configuration',
            'team',
            'assignments',
            'histories',
            'attachments'
        ]);



        $teams = SupportTeam::where('is_active',1)
            ->orderBy('support_level')
            ->orderBy('team_name')
            ->get();



        return view(
            'issues.show',
            compact(
                'issue',
                'teams'
            )
        );

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

}