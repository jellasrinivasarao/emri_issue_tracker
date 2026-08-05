<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkingCalendar;
use App\Services\IssueRoutingService;

use App\Models\Issue;
use App\Models\ProjectSupportConfiguration;
use App\Models\IssueRoutingConfiguration;
use App\Models\SupportTeam;

use App\Services\IssueService;

use Illuminate\Validation\Rule;

class IssueController extends Controller
{
    public function __construct(protected IssueService $issueService,protected IssueRoutingService $routingService) {}

     public function index()
    {
        
        // $issues = Issue::with(['configuration','team',])
        //     ->latest('issue_id')
        //     ->get();

         $configurations = IssueRoutingConfiguration::query()
        ->where('is_active', 1)
        ->orderBy('priority')
        ->get();
        
        #return view('issues.index', compact('issues'));

         return view('issues.index', ['configurations' => $configurations,]);
    }

    public function create()
    {
        $configurations = ProjectSupportConfiguration::where(
            'is_active',
            1
        )
            ->orderBy('config_name')
            ->get();

        return view(
            'issues.create',
            compact('configurations')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => ['nullable', 'integer'],
            'support_config_id' => ['required', 'integer'],
            'issue_category' => ['nullable', 'string', 'max:100'],
            'issue_type' => ['nullable', 'string', 'max:100'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => [
                'required',
                Rule::in([
                    'LOW',
                    'MEDIUM',
                    'HIGH',
                    'CRITICAL',
                ]),
            ],
        ]);

        $issue = $this->issueService->create($validated);

        $this->routingService->routeIssue($issue);


        return redirect()
            ->route('issues.show', $issue)
            ->with(
                'success',
                'Issue created and routing engine processed successfully.'
            );
    }


    public function show(Issue $issue)
    {
        $issue->load([
            'configuration',
            'team',
            'assignments',
            'histories',
        ]);

        $teams = SupportTeam::where('is_active', 1)
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

    public function update(Request $request, Issue $issue)
    {
        $validated = $request->validate([
            'issue_category' => ['nullable', 'string', 'max:100'],
            'issue_type' => ['nullable', 'string', 'max:100'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => [
                'required',
                Rule::in([
                    'LOW',
                    'MEDIUM',
                    'HIGH',
                    'CRITICAL',
                ]),
            ],
        ]);

        $this->issueService->update($issue, $validated);

        return back()->with(
            'success',
            'Issue updated successfully.'
        );
    }
    

    public function assign(Request $request, Issue $issue)
    {
        $validated = $request->validate([
            'support_team_id' => ['required', 'integer'],
            'remarks' => ['nullable', 'string', 'max:1000'],
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

    // public function determineRoute(Request $request)
    // {
    //     $request->validate([
    //         'calendar_id' => [
    //             'required',
    //             'integer',
    //         ],

    //         'ho_intervention_required' => [
    //             'required',
    //             'boolean',
    //         ],
    //     ]);

    //     $calendar = WorkingCalendar::query()
    //         ->where(
    //             'calendar_id',
    //             $request->calendar_id
    //         )
    //         ->where('is_active', true)
    //         ->firstOrFail();

    //     $result = $this->routingService->determineRoute(
    //         $calendar,
    //         (bool) $request->ho_intervention_required
    //     );

    //     return response()->json([
    //         'success' => true,
    //         'data' => $result,
    //     ]);
    // }
}