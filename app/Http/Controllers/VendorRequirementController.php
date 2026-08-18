<?php

namespace App\Http\Controllers;

use App\Models\Requirement;
use App\Services\RequirementService;
use Illuminate\Http\Request;

class VendorRequirementController extends Controller
{
    public function __construct(
        protected RequirementService $requirementService
    ) {
    }


    /**
     * Vendor assigned requirements.
     */
    public function index(Request $request)
    {
        $requirements = Requirement::query()
            ->with([
                'state:id,name',
                'project:id,name',
            ])

            ->where(
                'assigned_vendor_id',
                auth()->id()
            )

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

            ->latest()
            ->paginate(15)
            ->withQueryString();


        return view(
            'vendor.dashboard',
            compact('requirements')
        );
    }


    /**
     * Vendor update screen.
     */
    public function edit(
        Requirement $requirement
    ) {

        abort_unless(
            $requirement->assigned_vendor_id === auth()->id(),
            403
        );


        $requirement->load([
            'state',
            'project',
            'files',
        ]);


        return view(
            'vendor.requirements.edit',
            compact('requirement')
        );
    }


    /**
     * Vendor submits timeline/status.
     */
    public function update(
        Request $request,
        Requirement $requirement
    ) {

        abort_unless(
            $requirement->assigned_vendor_id === auth()->id(),
            403
        );


        $validated = $request->validate([

            'man_days' => [
                'required',
                'numeric',
                'min:0',
            ],

            'timeline' => [
                'required',
                'string',
            ],

            'delivery_status' => [
                'required',
                'string',
                'in:Requirements Understood,Development Started,Development Completed,Moved to UAT,UAT Completed,Moved to Production,On Hold',
            ],

            'remarks' => [
                'nullable',
                'string',
            ],

        ]);


        $requirement->update([

            'man_days' =>
                $validated['man_days'],

            'timeline' =>
                $validated['timeline'],

            'delivery_status' =>
                $validated['delivery_status'],

            'vendor_remarks' =>
                $validated['remarks'] ?? null,

        ]);


        /*
         * Map vendor delivery status
         * to application lifecycle status.
         */
        $statusMap = [

            'Requirements Understood' =>
                'Sent to Vendor',

            'Development Started' =>
                'In Progress',

            'Development Completed' =>
                'In Progress',

            'Moved to UAT' =>
                'UAT Requested',

            'UAT Completed' =>
                'UAT Completed',

            'Moved to Production' =>
                'Moved to Production',

            'On Hold' =>
                'On Hold',
        ];


        if (isset(
            $statusMap[$validated['delivery_status']]
        )) {

            $this->requirementService->changeStatus(

                $requirement,

                $statusMap[
                    $validated['delivery_status']
                ],

                $validated['remarks'] ?? null
            );
        }


        return redirect()
            ->route(
                'vendor.requirements.edit',
                $requirement
            )
            ->with(
                'success',
                'Requirement update submitted successfully.'
            );
    }
}