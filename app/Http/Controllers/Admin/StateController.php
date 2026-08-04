<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\Admin\StateRequest;
use App\Models\Organisation;
use App\Models\State;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class StateController extends Controller
{
    public function index(): View
    {
        // $states = State::query()
        //     ->with('organisation')
        //     ->withCount('projects')
        //     ->orderBy('state_name')
        //     ->paginate(20);

        $states = State::query()
            ->with('organisation')
            ->withCount('projects')

            ->when(
                request('search'),
                function ($query, $search) {

                    $search = trim($search);

                    $query->where(function ($q) use ($search) {

                        $q->where(
                            'state_code',
                            'LIKE',
                            "%{$search}%"
                        )

                        ->orWhere(
                            'state_name',
                            'LIKE',
                            "%{$search}%"
                        )

                        ->orWhere(
                            'state_short_name',
                            'LIKE',
                            "%{$search}%"
                        );

                    });
                     }
            )

            ->when(
                request('organisation_id'),
                function ($query, $organisationId) {

                    $query->where(
                        'organisation_id',
                        $organisationId
                    );
                }
            )
             ->when(
                request('status') !== null &&
                request('status') !== '',
                function ($query) {

                    $query->where(
                        'is_active',
                        request('status')
                    );
                }
            )

            ->orderBy('state_name')

            ->paginate(20)

            ->withQueryString();

       $organisations = Organisation::query()
            ->where('is_active', true)
            ->orderBy('organisation_name')
            ->get([
                'organisation_id',
                'organisation_code',
                'organisation_name',
            ]);


        return view('admin.states.index',compact('states','organisations'));
    }

     public function create(): View
    {
        
        $organisations = Organisation::query()
            ->where('is_active', true)
            ->orderBy('organisation_name')
            ->get([
                'organisation_id',
                'organisation_code',
                'organisation_name',
            ]);

        return view(
            'admin.states.create',
            compact('organisations')
        );
    }
    
    public function store(StateRequest $request): RedirectResponse {

        State::create([
            'organisation_id' =>
                $request->organisation_id,

            'state_code' =>
                strtoupper($request->state_code),

            'state_name' =>
                $request->state_name,

            'state_short_name' =>
                strtoupper(
                    $request->state_short_name
                ),

            'is_active' =>
                $request->boolean('is_active'),
        ]);

        return redirect()
            ->route('admin.states.index')
            ->with(
                'success',
                'State created successfully.'
            );
    }

    public function edit(State $state): View
    {
        $organisations = Organisation::query()
            ->where('is_active', true)
            ->orderBy('organisation_name')
            ->get();

        return view(
            'admin.states.edit',
            compact(
                'state',
                'organisations'
            )
        );
    }

    public function update(
        StateRequest $request,
        State $state
    ): RedirectResponse {

        $state->update([
            'organisation_id' =>
                $request->organisation_id,

            'state_code' =>
                strtoupper($request->state_code),

            'state_name' =>
                $request->state_name,

            'state_short_name' =>
                strtoupper(
                    $request->state_short_name
                ),

            'is_active' =>
                $request->boolean('is_active'),
        ]);
     return redirect()
            ->route('admin.states.index')
            ->with(
                'success',
                'State updated successfully.'
            );
    }

    public function destroy(State $state): RedirectResponse {

        if ($state->projects()->exists()) {
            return back()->with(
                'error',
                'State cannot be deleted because projects are mapped to it.'
            );
        }
        $state->delete();

        return redirect()
            ->route('admin.states.index')
            ->with(
                'success',
                'State deleted successfully.'
            );
    }
}