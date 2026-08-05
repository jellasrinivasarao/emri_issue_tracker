<?php

namespace App\Http\Controllers;

use App\Models\ProjectSupportConfiguration;
use App\Services\SupportConfigurationService;
use App\Services\ProjectSupportConfigurationService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProjectSupportConfigurationController extends Controller
{
    public function __construct(protected ProjectSupportConfigurationService $service) {}

    public function index(Request $request)
    {
        // $configurations = ProjectSupportConfiguration::query()
        //     ->orderByDesc('support_configuration_id')
        //     ->get();

        $configurations = $this->service->getAll();

        return view(
            'project-support-configurations.index',
            [
                'configurations' => $configurations,
                'title' => 'Project Support Configuration',
                'description' => 'Manage project support configuration and automatic issue routing.',
            ]
        );
    }

    public function create()
    {
        return view('project-support-configurations.create',['title' => 'Create Support Configuration']);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => ['required', 'integer'],
            'config_code' => [
                'required',
                'string',
                'max:50',
                'unique:mst_project_support_configuration,config_code',
            ],
            'config_name' => ['required', 'string', 'max:150'],
           // 'configuration_code' => ['required', 'string', 'max:50'],
           // 'configuration_name' => ['required', 'string', 'max:150'],
            'default_support_level' => ['required', 'integer', 'min:1', 'max:10'],
            'default_priority' => [
                'required',
                Rule::in(['LOW', 'MEDIUM', 'HIGH', 'CRITICAL']),
            ],
            'default_team_type' => ['required', 'string', 'max:30'],
            'sla_hours' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'auto_routing_enabled' => ['nullable', 'boolean'],
        ]);
        
        $validated['auto_routing_enabled'] = $request->boolean('auto_routing_enabled');

        $this->service->create($validated);

        return back()->with(
            'success',
            'Project support configuration created successfully.'
        );

        // return redirect()
        //     ->route('project.support')
        //     ->with('success', 'Support configuration created successfully.');
    }



    public function edit(ProjectSupportConfiguration $projectSupportConfiguration)
    {
        return view('project-support-configurations.edit',['title' => 'Edit Support Configuration','configuration' => $projectSupportConfiguration]);
    }

    public function update(
        Request $request,
        ProjectSupportConfiguration $projectSupportConfiguration
    ) {
        $validated = $request->validate([
            'project_id' => ['nullable', 'integer'],
            'config_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique(
                    'mst_project_support_configuration',
                    'config_code'
                )->ignore(
                    $projectSupportConfiguration->support_config_id,
                    'support_config_id'
                ),
            ],
            'config_name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'default_priority' => [
                'required',
                Rule::in(['LOW', 'MEDIUM', 'HIGH', 'CRITICAL']),
            ],
            'auto_routing_enabled' => ['nullable', 'boolean'],

           // 'project_id' => ['required', 'integer'],
           // 'configuration_code' => ['required', 'string', 'max:50'],
           // 'configuration_name' => ['required', 'string', 'max:150'],
            //'default_support_level' => ['required', 'integer', 'min:1', 'max:10'],
           // 'default_team_type' => ['required', 'string', 'max:30'],
            //'sla_hours' => ['nullable', 'numeric', 'min:0'],
            //'description' => ['nullable', 'string'],
        ]);

        $validated['auto_routing_enabled'] =$request->boolean('auto_routing_enabled');

        $this->service->update(
            $projectSupportConfiguration,
            $validated
        );

        return back()->with(
            'success',
            'Project support configuration updated successfully.'
        );

        // return redirect()
        //     ->route('project.support')
        //     ->with('success', 'Support configuration updated successfully.');
    }

    public function toggle(ProjectSupportConfiguration $projectSupportConfiguration) {
        $this->service->toggle($projectSupportConfiguration);

        return back()->with(
            'success',
            'Project support configuration status updated.'
        );
    }


    public function show(ProjectSupportConfiguration $projectSupportConfiguration) {
        $projectSupportConfiguration->load('routingRules.team');

        return view(
            'project-support-configurations.show',
            [
                'title' => 'Support Configuration Details',
                'configuration' => $projectSupportConfiguration,
            ]
        );
    }
}