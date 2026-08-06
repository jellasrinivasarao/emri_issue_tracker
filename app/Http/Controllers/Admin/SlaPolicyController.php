<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSlaPolicyRequest;
use App\Http\Requests\UpdateSlaPolicyRequest;

use App\Models\Application;
use App\Models\Priority;
use App\Models\Project;
use App\Models\Service;
use App\Models\SlaPolicy;
use App\Models\WorkingCalendar;

use App\Services\SlaPolicyService;

use Illuminate\Http\Request;

class SlaPolicyController extends Controller
{
    protected SlaPolicyService $service;

    public function __construct(SlaPolicyService $service) {
        $this->service = $service;
    }

        /**
     * -----------------------------------------------------
     * Index
     * -----------------------------------------------------
     */

    public function index(Request $request)
    {
        $query = SlaPolicy::query()

            ->with([
                'project',
                'application',
                'service',
                'priority',
                'calendar'
            ]);

        /*
        |--------------------------------------------------------------------------
        | Search
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = trim($request->search);

            $query->where(function ($q) use ($search) {

                $q->whereHas('project', function ($p) use ($search) {
                    $p->where('project_name', 'like', "%{$search}%");
                })

                ->orWhereHas('application', function ($a) use ($search) {
                    $a->where('application_name', 'like', "%{$search}%");
                })

                ->orWhereHas('service', function ($s) use ($search) {
                    $s->where('service_name', 'like', "%{$search}%");
                })

                ->orWhereHas('priority', function ($p) use ($search) {
                    $p->where('priority_name', 'like', "%{$search}%");
                });

            });

        }

        /*
        |--------------------------------------------------------------------------
        | Filters
        |--------------------------------------------------------------------------
        */

        if ($request->filled('project_id')) {

            $query->whereProjectId(
                $request->project_id
            );

        }

        if ($request->filled('application_id')) {

            $query->whereApplicationId(
                $request->application_id
            );

        }

        if ($request->filled('service_id')) {

            $query->whereServiceId(
                $request->service_id
            );

        }

        if ($request->filled('priority_id')) {

            $query->wherePriorityId(
                $request->priority_id
            );

        }

        if ($request->filled('is_active')) {

            $query->whereIsActive(
                $request->is_active
            );

        }

        $policies = $query

            ->latest('sla_policy_id')

            ->paginate(15)

            ->withQueryString();

        return view(
            'admin.sla-policies.index',

            [

                'policies' => $policies,

                'projects' => Project::orderBy('project_name')->get(),

                'applications' => Application::orderBy('application_name')->get(),

                'services' => Service::orderBy('service_name')->get(),

                'priorities' => Priority::orderBy('display_order')->get(),

            ]

        );
    }

    /**
     * -----------------------------------------------------
     * Create
     * -----------------------------------------------------
     */

    public function create()
    {
        return view(

            'admin.sla-policies.create',

            [

                'projects' => Project::orderBy('project_name')->get(),

                'applications' => Application::orderBy('application_name')->get(),

                'services' => Service::orderBy('service_name')->get(),

                'priorities' => Priority::orderBy('display_order')->get(),

                'calendars' => WorkingCalendar::orderBy('calendar_name')->get(),

            ]

        );
    }

    /**
     * -----------------------------------------------------
     * Store
     * -----------------------------------------------------
     */

    public function store(
        StoreSlaPolicyRequest $request
    ) {

        /*
        |--------------------------------------------------------------------------
        | Duplicate Policy Check
        |--------------------------------------------------------------------------
        */

        $exists = SlaPolicy::whereProjectId(
                $request->project_id
            )

            ->whereApplicationId(
                $request->application_id
            )

            ->whereServiceId(
                $request->service_id
            )

            ->wherePriorityId(
                $request->priority_id
            )

            ->exists();

        if ($exists) {

            return back()

                ->withInput()

                ->withErrors([

                    'project_id' =>

                    'SLA Policy already exists.'

                ]);

        }

        $this->service->create(

            $request->validated()

        );

        return redirect()

            ->route('sla-policies.index')

            ->with(

                'success',

                'SLA Policy created successfully.'

            );

    }

    /**
     * -----------------------------------------------------
     * Edit
     * -----------------------------------------------------
     */

    public function edit(
        SlaPolicy $sla_policy
    ) {

        return view(

            'admin.sla-policies.edit',

            [

                'policy' => $sla_policy,

                'projects' => Project::orderBy('project_name')->get(),

                'applications' => Application::orderBy('application_name')->get(),

                'services' => Service::orderBy('service_name')->get(),

                'priorities' => Priority::orderBy('display_order')->get(),

                'calendars' => WorkingCalendar::orderBy('calendar_name')->get(),

            ]

        );

    }

    /**
     * -----------------------------------------------------
     * Update
     * -----------------------------------------------------
     */

    public function update(

        UpdateSlaPolicyRequest $request,

        SlaPolicy $sla_policy

    ) {

        /*
        |--------------------------------------------------------------------------
        | Duplicate Validation
        |--------------------------------------------------------------------------
        */

        $exists = SlaPolicy::whereProjectId(
                $request->project_id
            )

            ->whereApplicationId(
                $request->application_id
            )

            ->whereServiceId(
                $request->service_id
            )

            ->wherePriorityId(
                $request->priority_id
            )

            ->where(
                'sla_policy_id',
                '!=',
                $sla_policy->sla_policy_id
            )

            ->exists();

        if ($exists) {

            return back()

                ->withInput()

                ->withErrors([

                    'project_id' =>

                    'Duplicate SLA Policy.'

                ]);

        }

        $this->service->update(

            $sla_policy,

            $request->validated()

        );

        return redirect()

            ->route('sla-policies.index')

            ->with(

                'success',

                'Policy Updated Successfully.'

            );

    }

    /**
     * -----------------------------------------------------
     * Delete
     * -----------------------------------------------------
     */

    public function destroy(
        SlaPolicy $sla_policy
    ) {

        $this->service->delete(
            $sla_policy
        );

        return redirect()

            ->route('admin.sla-policies.index')

            ->with(

                'success',

                'Policy Deleted.'

            );

    }



    #######################

    public function checkDuplicate(Request $request)
    {
        $query = SlaPolicy::query()
            ->where('project_id', $request->project_id)
            ->where('application_id', $request->application_id)
            ->where('service_id', $request->service_id)
            ->where('priority_id', $request->priority_id);

        if ($request->filled('sla_policy_id')) {

            $query->where(
                'sla_policy_id',
                '!=',
                $request->sla_policy_id
            );

        }

        $exists = $query->exists();

        return response()->json([
            'exists' => $exists
        ]);
    }
    
}