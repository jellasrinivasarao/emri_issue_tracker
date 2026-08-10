<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\Admin\OrganisationTypeRequest;
use App\Models\OrganisationType;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class OrganisationTypeController extends Controller
{
    public function index(): View
    {
        $items = OrganisationType::query()
            ->withCount('organisations')
            ->orderBy('organisation_type_name')
            ->paginate(20);

        return view('admin.organisation-types.index',compact('items'));
    }

    public function create(): View
    {
        return view('admin.organisation-types.create');
    }

    public function store(OrganisationTypeRequest $request): RedirectResponse {

        OrganisationType::create($request->validated());

        return redirect()->route('admin.organisation-types.index')->with('success','Organisation type created successfully.');
    }

    public function edit(OrganisationType $organisationType): View {

        return view('admin.organisation-types.edit',compact('organisationType'));
    }

    public function update(OrganisationTypeRequest $request,OrganisationType $organisationType): RedirectResponse {

        $organisationType->update(
            $request->validated()
        );

        return redirect()
            ->route('admin.organisation-types.index')
            ->with(
                'success',
                'Organisation type updated successfully.'
            );
    }

    public function destroy(OrganisationType $organisationType): RedirectResponse {

        if ($organisationType->organisations()->exists()) {
            return back()->with(
                'error',
                'Cannot delete this organisation type because organisations are mapped to it.'
            );
        }
        
        $organisationType->delete();

        return redirect()
            ->route('admin.organisation-types.index')
            ->with(
                'success',
                'Organisation type deleted successfully.'
            );
    }
}