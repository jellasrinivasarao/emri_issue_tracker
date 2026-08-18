<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateVendorRequirementRequest;
use App\Models\Requirement;
use App\Services\RequirementService;
use Illuminate\Http\Request;

class VendorRequirementController extends Controller
{
    public function __construct(
        protected RequirementService $service
    ) {
    }


    public function index(Request $request)
    {
        $requirements = Requirement::query()
            ->where(
                'assigned_vendor_id',
                auth()->id()
            )
            ->with([
                'state',
                'project',
            ])
            ->latest()
            ->paginate(15);


        return view(
            'vendor.dashboard',
            compact('requirements')
        );
    }


    public function edit(
        Requirement $requirement
    ) {

        abort_unless(
            $requirement->assigned_vendor_id
                === auth()->id(),
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


    public function update(
        UpdateVendorRequirementRequest $request,
        Requirement $requirement
    ) {

        abort_unless(
            $requirement->assigned_vendor_id
                === auth()->id(),
            403
        );


        $this->service->updateVendorDetails(
            $requirement,
            $request->validated()
        );


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