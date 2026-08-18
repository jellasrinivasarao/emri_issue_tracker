<?php

namespace App\Http\Controllers;

use App\Models\Requirement;
use App\Models\Project;
use App\Models\State;
use App\Services\RequirementService;
use Illuminate\Http\Request;

class RequirementController extends Controller
{
    public function __construct(
        protected RequirementService $requirementService
    ) {
    }

    /**
     * Requirement listing.
     */
    public function index(Request $request)
    {
        $requirements = Requirement::query()
            ->with([
                'state:id,name',
                'project:id,name',
            ])

            // State filter
            ->when(
                $request->filled('state'),
                fn ($query) =>
                    $query->where('state_id', $request->state)
            )

            // Status filter
            ->when(
                $request->filled('status'),
                fn ($query) =>
                    $query->where('status', $request->status)
            )

            // Search
            ->when(
                $request->filled('search'),
                function ($query) use ($request) {

                    $search = $request->search;

                    $query->where(function ($q) use ($search) {

                        $q->where('title', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%");

                    });
                }
            )

            // Date range
            ->when(
                $request->filled('from_date'),
                fn ($query) =>
                    $query->whereDate(
                        'created_at',
                        '>=',
                        $request->from_date
                    )
            )

            ->when(
                $request->filled('to_date'),
                fn ($query) =>
                    $query->whereDate(
                        'created_at',
                        '<=',
                        $request->to_date
                    )
            )

            ->latest()
            ->paginate(15)
            ->withQueryString();

        $states = State::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        $projects = Project::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('requirements.index', compact(
            'requirements',
            'states',
            'projects'
        ));
    }


    /**
     * Show create form.
     */
    public function create()
    {
        $states = State::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        $projects = Project::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('requirements.create', compact(
            'states',
            'projects'
        ));
    }


    /**
     * Store new requirement.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'required',
                'string',
            ],

            'state_id' => [
                'required',
                'integer',
                'exists:states,id',
            ],

            'project_id' => [
                'required',
                'integer',
                'exists:projects,id',
            ],

            'brd_raised_by' => [
                'nullable',
                'string',
                'max:255',
            ],

            'ho_it_team' => [
                'required',
                'boolean',
            ],

            'brd_document' => [
                'required',
                'file',
                'mimes:pdf,docx,xlsx',
                'max:25600',
            ],

            'received_at' => [
                'nullable',
                'date',
            ],

            'requested_to_vendor_at' => [
                'nullable',
                'date',
            ],

            'additional_details' => [
                'nullable',
                'string',
            ],

        ]);

        $requirement = $this->requirementService->create(
            $validated,
            $request->file('brd_document')
        );

        return redirect()
            ->route('requirements.show', $requirement)
            ->with(
                'success',
                'Requirement submitted to Achala successfully.'
            );
    }


    /**
     * Requirement details.
     */
    public function show(Requirement $requirement)
    {
        $requirement->load([
            'state',
            'project',
            'files',
            'statusHistory.user',
            'clarifications.user',
        ]);

        return view(
            'requirements.show',
            compact('requirement')
        );
    }


    /**
     * Edit requirement.
     */
    public function edit(Requirement $requirement)
    {
        $states = State::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        $projects = Project::query()
            ->orderBy('name')
            ->get(['id', 'name']);

        return view(
            'requirements.edit',
            compact(
                'requirement',
                'states',
                'projects'
            )
        );
    }


    /**
     * Update requirement.
     */
    public function update(
        Request $request,
        Requirement $requirement
    ) {
        $validated = $request->validate([

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'required',
                'string',
            ],

            'state_id' => [
                'required',
                'integer',
                'exists:states,id',
            ],

            'project_id' => [
                'required',
                'integer',
                'exists:projects,id',
            ],

            'additional_details' => [
                'nullable',
                'string',
            ],

        ]);

        $this->requirementService->update(
            $requirement,
            $validated
        );

        return redirect()
            ->route(
                'requirements.show',
                $requirement
            )
            ->with(
                'success',
                'Requirement updated successfully.'
            );
    }
}