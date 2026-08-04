<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WorkingCalendar;
use App\Services\IssueRoutingService;

class IssueController extends Controller
{
    public function __construct(protected IssueRoutingService $routingService) {}

    public function determineRoute(Request $request)
    {
        $request->validate([
            'calendar_id' => [
                'required',
                'integer',
            ],

            'ho_intervention_required' => [
                'required',
                'boolean',
            ],
        ]);

        $calendar = WorkingCalendar::query()
            ->where(
                'calendar_id',
                $request->calendar_id
            )
            ->where('is_active', true)
            ->firstOrFail();

        $result = $this->routingService->determineRoute(
            $calendar,
            (bool) $request->ho_intervention_required
        );

        return response()->json([
            'success' => true,
            'data' => $result,
        ]);
    }
}