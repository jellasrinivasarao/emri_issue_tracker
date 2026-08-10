<?php

namespace App\Http\Controllers;

use App\Models\IssueRoutingRule;
use Illuminate\Http\Request;

class IssueRoutingRuleController extends Controller
{
    public function index()
    {
        $rules = IssueRoutingRule::query()
            ->orderByDesc('routing_priority')
            ->orderByDesc('routing_rule_id')
            ->get();

        return view(
            'admin.issue-routing-rules.index',
            [
                'rules' => $rules,
                'title' => 'Issue Routing Rules',
                'description' => 'Configure enterprise issue routing rules.',
            ]
        );
    }

    public function store(Request $request)
    {
        $validated = $this->validateRequest($request);

        $validated['created_by'] = auth()->id();

        IssueRoutingRule::create($validated);

        return back()->with(
            'success',
            'Issue routing rule created successfully.'
        );
    }

    public function update(
        Request $request,
        IssueRoutingRule $issueRoutingRule
    ) {
        $validated = $this->validateRequest(
            $request,
            $issueRoutingRule->routing_rule_id
        );

        $validated['updated_by'] = auth()->id();

        $issueRoutingRule->update($validated);

        return back()->with(
            'success',
            'Issue routing rule updated successfully.'
        );
    }

    public function toggle(
        IssueRoutingRule $issueRoutingRule
    ) {
        $issueRoutingRule->update([
            'is_active' => ! $issueRoutingRule->is_active,
            'updated_by' => auth()->id(),
        ]);

        return back()->with(
            'success',
            'Issue routing rule status updated.'
        );
    }

    private function validateRequest(
        Request $request,
        ?int $id = null
    ): array {

        return $request->validate([
            'rule_code' => [
                'required',
                'string',
                'max:50',
                'unique:mst_issue_routing_rule,rule_code,' .
                    ($id ?? 'NULL') .
                    ',routing_rule_id',
            ],

            'rule_name' => [
                'required',
                'string',
                'max:150',
            ],

            'project_id' => [
                'required',
                'integer',
            ],

            'support_configuration_id' => [
                'nullable',
                'integer',
            ],

            'issue_category_id' => [
                'nullable',
                'integer',
            ],

            'issue_type_id' => [
                'nullable',
                'integer',
            ],

            'priority_id' => [
                'nullable',
                'integer',
            ],

            'support_level' => [
                'required',
                'integer',
                'min:1',
                'max:10',
            ],

            'support_team_id' => [
                'required',
                'integer',
            ],

            'sla_hours' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'routing_priority' => [
                'required',
                'integer',
                'min:1',
            ],

            'description' => [
                'nullable',
                'string',
            ],
        ]);
    }
}