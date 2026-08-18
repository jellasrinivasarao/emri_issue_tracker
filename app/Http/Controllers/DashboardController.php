<?php

namespace App\Http\Controllers;

use App\Models\Requirement;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $baseQuery = Requirement::query();


        /*
         * Apply filters.
         */
        $this->applyFilters(
            $baseQuery,
            $request
        );


        /*
         * KPI values.
         */
        $stats = [

            'total' =>
                (clone $baseQuery)->count(),

            'in_progress' =>
                (clone $baseQuery)
                    ->where('status', 'In Progress')
                    ->count(),

            'uat' =>
                (clone $baseQuery)
                    ->whereIn('status', [
                        'UAT Requested',
                        'UAT In Progress',
                        'UAT Completed',
                    ])
                    ->count(),

            'production' =>
                (clone $baseQuery)
                    ->where('status', 'Moved to Production')
                    ->count(),

            'clarifications' =>
                (clone $baseQuery)
                    ->where(
                        'status',
                        'Clarification Pending'
                    )
                    ->count(),
        ];


        /*
         * Main table.
         */
        $requirements = (clone $baseQuery)
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


    private function applyFilters(
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

                    $search = $request->search;

                    $q->where(function ($query) use ($search) {

                        $query
                            ->where(
                                'title',
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