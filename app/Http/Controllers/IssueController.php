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

use App\Services\IssueService;
use App\Services\IssueRoutingService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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


    //     // $modules = Module::where('is_active',1)
    //     //     ->orderBy('module_name')
    //     //     ->get();

    //     $issueCategories = IssueCategory::where('is_active',1)
    //         ->orderBy('category_name')
    //         ->get();

    //     $priorities = Priority::where('is_active',1)
    //         ->orderBy('priority_name')
    //         ->get();


    //     // $issueCategories = [
    //     //     'Application Issue',
    //     //     'Infrastructure Issue',
    //     //     'Network Issue',
    //     //     'Access Issue',
    //     //     'Data Issue',
    //     //     'Hardware Issue',
    //     //     'Other',
    //     // ];


    //     // $priorities = [
    //     //     'LOW',
    //     //     'MEDIUM',
    //     //     'HIGH',
    //     //     'CRITICAL',
    //     // ];

        
    //     return view('issues.partials.raise-issue-form', [
    //     'states'   => $states,
    //     'services' => $services,
    //     'projects'   => $projects,
    //     'applications' => $applications,
    //     'issueCategories'   => $issueCategories,
    //     'priorities' => $priorities,
        
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


        // $modules = Module::where('is_active',1)
        //     ->orderBy('module_name')
        //     ->get();

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


        return view('issues.create',compact('states','services','projects','applications',
                //'modules',
                'issueCategories',
                'priorities'
            )
        );
    }



    /**
     * Store issue.
     */
    public function store(Request $request)
    {


        Log::info("Raise Issue Request >>>>", ['response' => json_encode($request->all())]);
        $validated = $request->validate(

            [

                'state_id' => [
                    'required',
                    'integer'
                ],


                'service_id' => [
                    'required',
                    'integer'
                ],


                'project_id' => [
                    'required',
                    'integer'
                ],


                'application_id' => [
                    'required',
                    'integer'
                ],


                'module_id' => [
                    'nullable',
                    'integer'
                ],


                'support_config_id' => [
                    'required',
                    'integer'
                ],


                'issue_category' => [
                    'required',
                    'string',
                    'max:100'
                ],


                'issue_type' => [
                    'nullable',
                    'string',
                    'max:100'
                ],


                'subject' => [
                    'required',
                    'string',
                    'max:255'
                ],


                'description' => [
                    'required',
                    'string'
                ],


                'priority' => [
                    'required',
                    Rule::in([
                        'LOW',
                        'MEDIUM',
                        'HIGH',
                        'CRITICAL'
                    ])
                ],


                'attachment' => [
                    'nullable',
                    'file',
                    'mimes:jpg,jpeg,png,pdf,doc,docx,xls,xlsx',
                    'max:10240'
                ]

            ],

            [

                'state_id.required' =>
                    'Please select state.',

                'service_id.required' =>
                    'Please select service.',

                'project_id.required' =>
                    'Please select project.',

                'application_id.required' =>
                    'Please select application.',

                'support_config_id.required' =>
                    'Please select support configuration.',

                'subject.required' =>
                    'Please enter issue subject.',

                'description.required' =>
                    'Please enter issue description.',

            ]

        );



        try {


            #$issue = $this->issueService->create($validated,$request->file('attachment'),Auth::id());

            $issue = $this->issueService->create($request);

            #$this->routingService->routeIssue($issue);



            return redirect()
                ->route(
                    'issues.show',
                    $issue
                )
                ->with(
                    'success',
                    'Issue raised successfully. Issue Number: '
                    .$issue->issue_number
                );



        }
        catch(Throwable $e)
        {

            report($e);


            return back()
                ->withInput()
                ->with(
                    'error',
                    'Unable to raise issue. Please try again.'.$e->getMessage(),
                );

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