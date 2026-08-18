<?php

namespace App\Http\Controllers;

use App\Models\Requirement;
use App\Models\State;
use App\Models\Project;

use Illuminate\Http\Request;

class RequirementController extends Controller
{
    public function index(Request $request)
    {
        $query = Requirement::query();

        $this->filters(
            $query,
            $request
        );


        $stats = [

            'total' =>
                (clone $query)->count(),

            'in_progress' =>
                (clone $query)
                    ->where(
                        'status',
                        'In Progress'
                    )
                    ->count(),

            'uat' =>
                (clone $query)
                    ->whereIn(
                        'status',
                        [
                            'UAT Requested',
                            'UAT In Progress',
                            'UAT Completed',
                        ]
                    )
                    ->count(),

            'production' =>
                (clone $query)
                    ->where(
                        'status',
                        'Moved to Production'
                    )
                    ->count(),

            'clarifications' =>
                (clone $query)
                    ->where(
                        'status',
                        'Clarification Pending'
                    )
                    ->count(),
        ];


        $requirements = (clone $query)
            ->with([
                'state:id,name',
                'project:id,name',
            ])
            ->latest()
            ->paginate(15)
            ->withQueryString();


        return view(
            'dashboard.index',
            compact(
                'requirements',
                'stats'
            )
        );
    }




    public function create()
    {
        $states = State::query()
            ->orderBy('state_name')
            ->get(['state_id', 'state_name']);

        $projects = Project::query()
            ->orderBy('project_name')
            ->get(['project_id', 'project_name']);

        return view('requirements.create', compact(
            'states',
            'projects'
        ));
    }


    public function store(Request $request)
    {
        Log::info("Raise Issue Request >>>>", ['response' => json_encode($request->all())]);

        try {


            

        }catch (Throwable $e) {
            #report($e);

            Log::error('╔════════════════════════════════════════════════╗');
            Log::error('║ ✗ ISSUE CREATE FAILED IN CONTROLLER            ║');
            Log::error('╚════════════════════════════════════════════════╝');
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



    private function filters(
        $query,
        Request $request
    ): void {

        $query

            ->when(
                $request->filled('state'),
                fn ($q) =>
                    $q->where(
                        'state_id',
                        $request->state
                    )
            )

            ->when(
                $request->filled('status'),
                fn ($q) =>
                    $q->where(
                        'status',
                        $request->status
                    )
            )

            ->when(
                $request->filled('search'),
                function ($q) use ($request) {

                    $search =
                        $request->search;

                    $q->where(function ($query) use (
                        $search
                    ) {

                        $query
                            ->where(
                                'title',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'requirement_no',
                                'like',
                                "%{$search}%"
                            )
                            ->orWhere(
                                'description',
                                'like',
                                "%{$search}%"
                            );
                    });
                }
            )

            ->when(
                $request->filled('from_date'),
                fn ($q) =>
                    $q->whereDate(
                        'created_at',
                        '>=',
                        $request->from_date
                    )
            )

            ->when(
                $request->filled('to_date'),
                fn ($q) =>
                    $q->whereDate(
                        'created_at',
                        '<=',
                        $request->to_date
                    )
            );
    }


    public function downloadFile(
    Requirement $requirement,
    $file
    ) {
        $requirementFile =
            $requirement->files()
                ->where('id', $file)
                ->firstOrFail();


        abort_unless(
            auth()->check(),
            403
        );


        return \Storage::disk('private')
            ->download(
                $requirementFile->file_path,
                $requirementFile->original_name
            );
    }


}