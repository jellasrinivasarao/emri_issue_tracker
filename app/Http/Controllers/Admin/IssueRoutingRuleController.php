<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\HoIt;
use App\Models\IssueRoutingRule;
use App\Models\Project;
use App\Models\ProjectSupportConfiguration;
use App\Models\State;
use App\Models\SupportTeam;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class IssueRoutingRuleController extends Controller
{
    /**
     * Display routing rules.
     */
    public function index(Request $request)
    {
        $query = IssueRoutingRule::query()
            ->with([
                'supportConfig',
                'supportTeam',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->where('rule_code', 'LIKE', "%{$search}%")
                    ->orWhere('rule_name', 'LIKE', "%{$search}%")
                    ->orWhere('issue_category', 'LIKE', "%{$search}%")
                    ->orWhere('issue_type', 'LIKE', "%{$search}%")
                    ->orWhere('priority', 'LIKE', "%{$search}%");
            });
        }

        /*
        |--------------------------------------------------------------------------
        | Project Filter
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
        | Support Configuration Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('support_config_id')) {

            $query->where(
                'support_config_id',
                $request->support_config_id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Application Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('application_id')) {

            $query->where(
                'application_id',
                $request->application_id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | State Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('state_id')) {

            $query->where(
                'state_id',
                $request->state_id
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Routing Level
        |--------------------------------------------------------------------------
        */

        if ($request->filled('routing_level')) {

            $query->where(
                'routing_level',
                $request->routing_level
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Active / Inactive
        |--------------------------------------------------------------------------
        */

        if ($request->filled('is_active')) {

            $query->where(
                'is_active',
                $request->is_active
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Default Rule
        |--------------------------------------------------------------------------
        */

        if ($request->filled('is_default')) {

            $query->where(
                'is_default',
                $request->is_default
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $rules = $query
            ->orderBy('project_id')
            ->orderBy('routing_level')
            ->orderByDesc('is_default')
            ->orderBy('rule_name')
            ->paginate(15)
            ->withQueryString();

        /*
        |--------------------------------------------------------------------------
        | Dropdown Data
        |--------------------------------------------------------------------------
        */

        $projects = Project::query()
            ->where('is_active', 1)
            ->orderBy('project_name')
            ->get([
                'project_id',
                'project_name',
                'project_code',
            ]);

        $supportConfigs = ProjectSupportConfiguration::query()
            ->where('is_active', 1)
            ->orderBy('configuration_name')
            ->get([
                'support_configuration_id',
                'configuration_name',
                'configuration_code',
                'project_id',
            ]);

        return view(
            'admin.issue-routing-rules.index',
            compact(
                'rules',
                'projects',
                'supportConfigs'
            )
        );
    }


    /**
     * Show create form.
     */
    public function create()
    {
        $projects = Project::query()
            ->where('is_active', 1)
            ->orderBy('project_name')
            ->get([
                'project_id',
                'project_name',
                'project_code',
            ]);

        $supportTeams = SupportTeam::query()
            ->where('is_active', 1)
            ->orderBy('team_name')
            ->get([
                'support_team_id',
                'team_name',
                'team_code',
                'team_type',
                'support_level',
            ]);

        return view(
            'admin.issue-routing-rules.create',
            compact(
                'projects',
                'supportTeams'
            )
        );
    }


    /**
     * Store new routing rule.
     */
    public function store(Request $request)
    {
        $validated = $this->validateRule(
            $request
        );

        try {

            DB::beginTransaction();

            /*
            |--------------------------------------------------------------------------
            | Prepare Target
            |--------------------------------------------------------------------------
            */

            $this->prepareRoutingTarget(
                $request,
                $validated
            );

            /*
            |--------------------------------------------------------------------------
            | Default Rule
            |--------------------------------------------------------------------------
            */

            if (
                isset($validated['is_default']) &&
                $validated['is_default'] == 1
            ) {

                $this->clearExistingDefaultRules(
                    $validated
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Audit
            |--------------------------------------------------------------------------
            */

            $validated['created_by'] =
                auth()->id();

            $validated['updated_by'] =
                auth()->id();

            /*
            |--------------------------------------------------------------------------
            | Create
            |--------------------------------------------------------------------------
            */

            $rule = IssueRoutingRule::create(
                $validated
            );

            DB::commit();

            return redirect()
                ->route(
                    'admin.issue-routing-rules.index'
                )
                ->with(
                    'success',
                    'Issue routing rule created successfully.'
                );

        } catch (\Throwable $e) {

            DB::rollBack();

            report($e);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Unable to create issue routing rule.'
                );
        }
    }


    /**
     * Display routing rule.
     */
    public function show(
        IssueRoutingRule $issueRoutingRule
    ) {
        $issueRoutingRule->load([
            'supportConfig',
            'supportTeam',
        ]);

        return view(
            'admin.issue-routing-rules.show',
            compact(
                'issueRoutingRule'
            )
        );
    }


    /**
     * Show edit form.
     */
    public function edit(
        IssueRoutingRule $issueRoutingRule
    ) {
        $projects = Project::query()
            ->where('is_active', 1)
            ->orderBy('project_name')
            ->get([
                'project_id',
                'project_name',
                'project_code',
            ]);

        $supportTeams = SupportTeam::query()
            ->where('is_active', 1)
            ->orderBy('team_name')
            ->get([
                'support_team_id',
                'team_name',
                'team_code',
                'team_type',
                'support_level',
            ]);

        /*
        |--------------------------------------------------------------------------
        | Existing Target Type
        |--------------------------------------------------------------------------
        */

        $targetType = null;

        if (!empty($issueRoutingRule->hoit_id)) {

            $targetType = 'HOIT';

        } elseif (!empty($issueRoutingRule->vendor_id)) {

            $targetType = 'VENDOR';
        }

        return view(
            'admin.issue-routing-rules.edit',
            compact(
                'issueRoutingRule',
                'projects',
                'supportTeams',
                'targetType'
            )
        );
    }


    /**
     * Update routing rule.
     */
    public function update(
        Request $request,
        IssueRoutingRule $issueRoutingRule
    ) {
        $validated = $this->validateRule(
            $request,
            $issueRoutingRule
        );

        try {

            DB::beginTransaction();

            /*
            |--------------------------------------------------------------------------
            | Prepare Target
            |--------------------------------------------------------------------------
            */

            $this->prepareRoutingTarget(
                $request,
                $validated
            );

            /*
            |--------------------------------------------------------------------------
            | Default Rule
            |--------------------------------------------------------------------------
            */

            if (
                isset($validated['is_default']) &&
                $validated['is_default'] == 1
            ) {

                $this->clearExistingDefaultRules(
                    $validated,
                    $issueRoutingRule->routing_rule_id
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Audit
            |--------------------------------------------------------------------------
            */

            $validated['updated_by'] =
                auth()->id();

            /*
            |--------------------------------------------------------------------------
            | Update
            |--------------------------------------------------------------------------
            */

            $issueRoutingRule->update(
                $validated
            );

            DB::commit();

            return redirect()
                ->route(
                    'admin.issue-routing-rules.index'
                )
                ->with(
                    'success',
                    'Issue routing rule updated successfully.'
                );

        } catch (\Throwable $e) {

            DB::rollBack();

            report($e);

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Unable to update issue routing rule.'
                );
        }
    }


    /**
     * Delete routing rule.
     */
    public function destroy(
        IssueRoutingRule $issueRoutingRule
    ) {
        try {

            /*
            |--------------------------------------------------------------------------
            | Prevent deleting active rule
            |--------------------------------------------------------------------------
            */

            if ($issueRoutingRule->is_active) {

                return back()->with(
                    'error',
                    'Please deactivate the routing rule before deleting it.'
                );
            }

            $issueRoutingRule->delete();

            return redirect()
                ->route(
                    'admin.issue-routing-rules.index'
                )
                ->with(
                    'success',
                    'Issue routing rule deleted successfully.'
                );

        } catch (\Throwable $e) {

            report($e);

            return back()->with(
                'error',
                'Unable to delete issue routing rule.'
            );
        }
    }


    /**
     * Activate / Deactivate routing rule.
     */
    public function toggle(
        IssueRoutingRule $issueRoutingRule
    ) {
        try {

            $issueRoutingRule->update([
                'is_active' =>
                    !$issueRoutingRule->is_active,

                'updated_by' =>
                    auth()->id(),
            ]);

            $message = $issueRoutingRule->is_active
                ? 'Routing rule activated successfully.'
                : 'Routing rule deactivated successfully.';

            return back()->with(
                'success',
                $message
            );

        } catch (\Throwable $e) {

            report($e);

            return back()->with(
                'error',
                'Unable to update routing rule status.'
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | DEPENDENT DROPDOWNS
    |--------------------------------------------------------------------------
    */


    /**
     * Project → Support Configuration
     */
    public function supportConfigs(
        Request $request
    ) {
        $request->validate([
            'project_id' => [
                'required',
                'integer',
            ],
        ]);

        $configs =
            ProjectSupportConfiguration::query()
                ->where(
                    'project_id',
                    $request->project_id
                )
                ->where(
                    'is_active',
                    1
                )
                ->orderBy(
                    'configuration_name'
                )
                ->get([
                    'support_configuration_id',
                    'configuration_code',
                    'configuration_name',
                    'project_id',
                ]);

        return response()->json([
            'success' => true,
            'data' => $configs,
        ]);
    }


    /**
     * Project + Support Config → Application
     */
    public function applications(
        Request $request
    ) {
        $request->validate([
            'project_id' => [
                'required',
                'integer',
            ],

            'support_config_id' => [
                'nullable',
                'integer',
            ],
        ]);

        $query = Application::query()
            ->where(
                'project_id',
                $request->project_id
            )
            ->where(
                'is_active',
                1
            );

        if ($request->filled('support_config_id')) {

            $query->where(
                'support_config_id',
                $request->support_config_id
            );
        }

        $applications = $query
            ->orderBy('application_name')
            ->get([
                'application_id',
                'application_code',
                'application_name',
                'project_id',
                'support_config_id',
            ]);

        return response()->json([
            'success' => true,
            'data' => $applications,
        ]);
    }


    /**
     * Application → State
     */
    public function states(
        Request $request
    ) {
        $request->validate([
            'application_id' => [
                'required',
                'integer',
            ],
        ]);

        $states = State::query()
            ->where(
                'application_id',
                $request->application_id
            )
            ->where(
                'is_active',
                1
            )
            ->orderBy('state_name')
            ->get([
                'state_id',
                'state_code',
                'state_name',
                'application_id',
            ]);

        return response()->json([
            'success' => true,
            'data' => $states,
        ]);
    }


    /**
     * Project + Application + State
     * → Vendor / HO IT
     */
    public function targets(
        Request $request
    ) {
        $request->validate([
            'project_id' => [
                'required',
                'integer',
            ],

            'application_id' => [
                'nullable',
                'integer',
            ],

            'state_id' => [
                'nullable',
                'integer',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Vendors
        |--------------------------------------------------------------------------
        */

        $vendors = Vendor::query()
            ->where(
                'is_active',
                1
            )
            ->orderBy(
                'vendor_name'
            )
            ->get([
                'vendor_id',
                'vendor_code',
                'vendor_name',
            ]);

        /*
        |--------------------------------------------------------------------------
        | HO IT
        |--------------------------------------------------------------------------
        */

        $hoIts = HoIt::query()
            ->where(
                'is_active',
                1
            )
            ->orderBy(
                'hoit_name'
            )
            ->get([
                'hoit_id',
                'hoit_code',
                'hoit_name',
            ]);

        return response()->json([
            'success' => true,

            'data' => [
                'vendors' => $vendors,
                'hoits'   => $hoIts,
            ],
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | PRIVATE METHODS
    |--------------------------------------------------------------------------
    */


    /**
     * Validate routing rule.
     */
    private function validateRule(
        Request $request,
        ?IssueRoutingRule $rule = null
    ): array {

        $ruleCodeRule = Rule::unique(
            'mst_issue_routing_rule',
            'rule_code'
        );

        if ($rule) {

            $ruleCodeRule->ignore(
                $rule->routing_rule_id,
                'routing_rule_id'
            );
        }

        return $request->validate([

            /*
            |--------------------------------------------------------------------------
            | Configuration
            |--------------------------------------------------------------------------
            */

            'support_config_id' => [
                'nullable',
                'integer',
            ],

            'support_team_id' => [
                'nullable',
                'integer',
            ],

            /*
            |--------------------------------------------------------------------------
            | Rule
            |--------------------------------------------------------------------------
            */

            'rule_code' => [
                'required',
                'string',
                'max:50',
                $ruleCodeRule,
            ],

            'rule_name' => [
                'required',
                'string',
                'max:150',
            ],

            /*
            |--------------------------------------------------------------------------
            | Issue Matching
            |--------------------------------------------------------------------------
            */

            'issue_category' => [
                'nullable',
                'string',
                'max:100',
            ],

            'issue_type' => [
                'nullable',
                'string',
                'max:100',
            ],

            'priority' => [
                'nullable',
                'string',
                'max:20',
            ],

            /*
            |--------------------------------------------------------------------------
            | Routing
            |--------------------------------------------------------------------------
            */

            'routing_level' => [
                'required',
                'integer',
                'min:1',
            ],

            'is_default' => [
                'nullable',
                'boolean',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],

            /*
            |--------------------------------------------------------------------------
            | Dependencies
            |--------------------------------------------------------------------------
            */

            'project_id' => [
                'required',
                'integer',
            ],

            'state_id' => [
                'nullable',
                'integer',
            ],

            'application_id' => [
                'nullable',
                'integer',
            ],

            /*
            |--------------------------------------------------------------------------
            | Targets
            |--------------------------------------------------------------------------
            */

            'vendor_id' => [
                'nullable',
                'integer',
            ],

            'hoit_id' => [
                'nullable',
                'integer',
            ],

            /*
            |--------------------------------------------------------------------------
            | UI Target
            |--------------------------------------------------------------------------
            */

            'target_type' => [
                'nullable',
                Rule::in([
                    'HOIT',
                    'VENDOR',
                ]),
            ],

            'target_id' => [
                'nullable',
                'integer',
            ],
        ]);
    }


    /**
     * Prepare HO IT / Vendor target.
     *
     * Only one target can be selected.
     */
    private function prepareRoutingTarget(
        Request $request,
        array &$validated
    ): void {

        $targetType =
            $request->input('target_type');

        $targetId =
            $request->input('target_id');

        /*
        |--------------------------------------------------------------------------
        | Reset both targets
        |--------------------------------------------------------------------------
        */

        $validated['vendor_id'] = null;
        $validated['hoit_id'] = null;

        /*
        |--------------------------------------------------------------------------
        | HO IT
        |--------------------------------------------------------------------------
        */

        if (
            $targetType === 'HOIT' &&
            !empty($targetId)
        ) {

            $validated['hoit_id'] =
                (int) $targetId;

            $validated['vendor_id'] =
                null;

            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Vendor
        |--------------------------------------------------------------------------
        */

        if (
            $targetType === 'VENDOR' &&
            !empty($targetId)
        ) {

            $validated['vendor_id'] =
                (int) $targetId;

            $validated['hoit_id'] =
                null;

            return;
        }
    }


    /**
     * Clear existing default rules.
     *
     * Default is unique within:
     *
     * Project
     * Support Configuration
     * Application
     * State
     * Routing Level
     */
    private function clearExistingDefaultRules(
        array $data,
        ?int $excludeRuleId = null
    ): void {

        $query = IssueRoutingRule::query();

        /*
        |--------------------------------------------------------------------------
        | Project
        |--------------------------------------------------------------------------
        */

        $query->where(
            'project_id',
            $data['project_id']
        );

        /*
        |--------------------------------------------------------------------------
        | Support Configuration
        |--------------------------------------------------------------------------
        */

        if (
            array_key_exists(
                'support_config_id',
                $data
            )
        ) {

            if (
                is_null(
                    $data['support_config_id']
                )
            ) {

                $query->whereNull(
                    'support_config_id'
                );

            } else {

                $query->where(
                    'support_config_id',
                    $data['support_config_id']
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Application
        |--------------------------------------------------------------------------
        */

        if (
            array_key_exists(
                'application_id',
                $data
            )
        ) {

            if (
                is_null(
                    $data['application_id']
                )
            ) {

                $query->whereNull(
                    'application_id'
                );

            } else {

                $query->where(
                    'application_id',
                    $data['application_id']
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | State
        |--------------------------------------------------------------------------
        */

        if (
            array_key_exists(
                'state_id',
                $data
            )
        ) {

            if (
                is_null(
                    $data['state_id']
                )
            ) {

                $query->whereNull(
                    'state_id'
                );

            } else {

                $query->where(
                    'state_id',
                    $data['state_id']
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Routing Level
        |--------------------------------------------------------------------------
        */

        $query->where(
            'routing_level',
            $data['routing_level']
        );

        /*
        |--------------------------------------------------------------------------
        | Exclude Current Rule
        |--------------------------------------------------------------------------
        */

        if ($excludeRuleId) {

            $query->where(
                'routing_rule_id',
                '!=',
                $excludeRuleId
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        */

        $query->update([
            'is_default' => 0,
            'updated_by' => auth()->id(),
        ]);
    }
}