<?php

namespace App\Http\Controllers;

use App\Models\IssueRoutingRule;
use App\Models\ProjectSupportConfiguration;
use App\Models\SupportTeam;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\IssueRoutingService;
use App\Models\IssueRoutingConfiguration;

class IssueRoutingController extends Controller
{

    public function __construct(protected IssueRoutingService $issueRoutingService) {}
    
    public function index1()
    {
        $rules = IssueRoutingRule::with([
            'configuration',
            'team',
        ])
            ->orderBy('routing_level')
            ->orderBy('rule_name')
            ->get();

        $configurations = ProjectSupportConfiguration::where(
            'is_active',
            1
        )
            ->orderBy('config_name')
            ->get();

        $teams = SupportTeam::where('is_active', 1)
            ->orderBy('support_level')
            ->orderBy('team_name')
            ->get();

        return view(
            'admin.issue-routing-rules.index',
            compact(
                'rules',
                'configurations',
                'teams'
            )
        );
    }


    /**
     * Issue Routing Configuration listing.
     */
    public function index(Request $request)
    {
        echo __LINE__;
        $query = IssueRoutingConfiguration::query();

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('configuration_code', 'like', "%{$search}%")
                    ->orWhere('configuration_name', 'like', "%{$search}%")
                    ->orWhere('routing_level', 'like', "%{$search}%");
            });
        }

        if ($request->filled('routing_level')) {
            $query->where(
                'routing_level',
                $request->routing_level
            );
        }



        if ($request->filled('is_active')) {
            $query->where(
                'is_active',
                (int) $request->is_active
            );
        }

        $configurations = $query
            ->orderBy('priority')
            ->orderBy('configuration_name')
            ->paginate(25)
            ->withQueryString();

            dd($configurations);

        return view(
            'admin.issue-routing.index',
            [
                'configurations' => $configurations,
                'title' => 'Issue Routing Configuration',
                'description' => 'Configure enterprise issue routing between HO IT Level 1 and Vendor Level 2.',
            ]
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'support_config_id' => ['required', 'integer'],
            'support_team_id' => ['required', 'integer'],
            'rule_code' => [
                'required',
                'string',
                'max:50',
                'unique:mst_issue_routing_rule,rule_code',
            ],
            'rule_name' => ['required', 'string', 'max:150'],
            'issue_category' => ['nullable', 'string', 'max:100'],
            'issue_type' => ['nullable', 'string', 'max:100'],
            'priority' => [
                'nullable',
                Rule::in([
                    'LOW',
                    'MEDIUM',
                    'HIGH',
                    'CRITICAL',
                ]),
            ],
            'routing_level' => ['required', 'integer', 'min:1'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $validated['is_default'] = $request->boolean('is_default');
        $validated['is_active'] = true;
        $validated['created_by'] = auth()->id();

        IssueRoutingRule::create($validated);

        return back()->with(
            'success',
            'Routing rule created successfully.'
        );
    }

    public function update(Request $request, IssueRoutingRule $issueRoutingRule)
    {
        $validated = $request->validate([
            'support_config_id' => ['required', 'integer'],
            'support_team_id' => ['required', 'integer'],
            'rule_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique(
                    'mst_issue_routing_rule',
                    'rule_code'
                )->ignore(
                    $issueRoutingRule->routing_rule_id,
                    'routing_rule_id'
                ),
            ],
            'rule_name' => ['required', 'string', 'max:150'],
            'issue_category' => ['nullable', 'string', 'max:100'],
            'issue_type' => ['nullable', 'string', 'max:100'],
            'priority' => [
                'nullable',
                Rule::in([
                    'LOW',
                    'MEDIUM',
                    'HIGH',
                    'CRITICAL',
                ]),
            ],
            'routing_level' => ['required', 'integer', 'min:1'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $validated['is_default'] = $request->boolean('is_default');
        $validated['updated_by'] = auth()->id();

        $issueRoutingRule->update($validated);

        return back()->with(
            'success',
            'Routing rule updated successfully.'
        );
    }

    public function toggle(IssueRoutingRule $issueRoutingRule)
    {
        $issueRoutingRule->update([
            'is_active' => ! $issueRoutingRule->is_active,
            'updated_by' => auth()->id(),
        ]);

        return back()->with(
            'success',
            'Routing rule status updated successfully.'
        );
    }
}