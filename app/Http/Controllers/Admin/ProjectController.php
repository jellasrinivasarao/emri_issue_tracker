<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\Admin\ProjectRequest;
use App\Models\Project;
use App\Models\State;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;


class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        // $projects = Project::query()
        //     ->with('state')
        //     ->withCount([
        //         'applications',
        //         'services',
        //     ])
        //     ->orderBy('project_name')
        //     ->paginate(20);

        // return view(
        //     'admin.projects.index',
        //     compact('projects')
        // );


        $projects = Project::query()
        ->with('state')
        ->when(
            request('search'),
            function ($query, $search) {

                $query->where(function ($q) use ($search) {

                    $q->where(
                        'project_code',
                        'LIKE',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'project_name',
                        'LIKE',
                        "%{$search}%"
                    );

                });
            }
        )->when(
            request('status'),
            function ($query, $status) {

                if ($status === 'ACTIVE') {
                    $query->where('is_active', true);
                }

                if ($status === 'INACTIVE') {
                    $query->where('is_active', false);
                }

            }
        )
        ->orderBy('project_name')
        ->paginate(20)
        ->withQueryString();

    return view(
        'admin.projects.index',
        compact('projects')
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $states = State::query()
            ->where('is_active', true)
            ->orderBy('state_name')
            ->get([
                'state_id',
                'state_name',
                'state_code',
            ]);

        return view(
            'admin.projects.create',
            compact('states')
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        ProjectRequest $request
    ): RedirectResponse {

        DB::transaction(function () use ($request) {

            Project::create([
                'state_id' =>
                    $request->integer('state_id'),

                'project_code' =>
                    strtoupper(
                        trim($request->project_code)
                    ),

                'project_name' =>
                    trim($request->project_name),

                'project_description' =>
                    $request->project_description,

                'start_date' =>
                    $request->start_date,

                'end_date' =>
                    $request->end_date,
                    'project_status' =>
                    strtoupper(
                        $request->project_status
                    ),

                'is_active' =>
                    $request->boolean('is_active'),
            ]);
        });

        return redirect()
            ->route('admin.projects.index')
            ->with(
                'success',
                'Project created successfully.'
            );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project): View {

        $states = State::query()
            ->where('is_active', true)
            ->orWhere(
                'state_id',
                $project->state_id
            )
            ->orderBy('state_name')
            ->get([
                'state_id',
                'state_name',
                'state_code',
            ]);

        return view(
            'admin.projects.edit',
            compact(
                'project',
                'states'
            )
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        ProjectRequest $request,
        Project $project
    ): RedirectResponse {

        DB::transaction(function () use (
            $request,
            $project
        ) {

            $project->update([
                'state_id' =>
                    $request->integer('state_id'),

                'project_code' =>
                    strtoupper(
                        trim($request->project_code)
                    ),

                'project_name' =>
                    trim($request->project_name),

                'project_description' =>
                    $request->project_description,

                'start_date' =>
                    $request->start_date,

                'end_date' =>
                    $request->end_date,

                'project_status' =>
                    strtoupper(
                        $request->project_status
                    ),
                'is_active' =>
                    $request->boolean('is_active'),
            ]);
        });

        return redirect()
            ->route('admin.projects.index')
            ->with(
                'success',
                'Project updated successfully.'
            );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project): RedirectResponse {



        //     $dependencies = [
        //     'map_project_application' => 'Project/Application mappings',
        //     'map_project_service'     => 'Project/Service mappings',
        //     'cfg_issue_routing'       => 'Issue routing configuration',
        //     'cfg_sla_policy'          => 'SLA policies',
        //     'txn_issue'               => 'Issues',
        // ];

        // foreach ($dependencies as $table => $description) {

        //     if (
        //         DB::table($table)
        //             ->where('project_id', $project->project_id)
        //             ->exists()
        //     ) {
        //         return back()->with(
        //             'error',
        //             "Project cannot be deleted because {$description} exist."
        //         );
        //     }
        // }


        /*
         * Do not physically delete a project
         * if dependent mappings exist.
         */

        if (
            $project->applications()->exists()
        ) {
            return back()->with(
                'error',
                'Project cannot be deleted because applications are mapped to it.'
            );
        }

        if (
            $project->services()->exists()
        ) {
            return back()->with(
                'error',
                'Project cannot be deleted because services are mapped to it.'
            );
        }

        if (
            DB::table('txn_issue')
                ->where(
                    'project_id',
                    $project->project_id
                )
                ->exists()
        ) {
            return back()->with(
                'error',
                'Project cannot be deleted because issues are associated with it.'
            );
        }

        $project->delete();

        return redirect()
            ->route('admin.projects.index')
            ->with(
                'success',
                'Project deleted successfully.'
            );
    }
}