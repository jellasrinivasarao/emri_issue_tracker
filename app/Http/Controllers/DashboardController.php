<?php

namespace App\Http\Controllers;

use App\Models\Requirement;
use App\Models\State;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {


        $states = State::query()
        ->where('is_active', true)
        ->orderBy('state_name')
        ->get([
            'state_id',
            'state_name',
        ]);

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
                'state:state_id,state_name',
                'project:project_id,project_name',
            ])
            ->latest()
            ->paginate(15)
            ->withQueryString();


        return view(
            'dashboard.index',
            compact(
                'requirements',
                'stats',
                'states'
            )
        );
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
}