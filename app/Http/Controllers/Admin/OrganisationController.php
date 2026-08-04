<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\Admin\OrganisationRequest;
use App\Models\Organisation;
use App\Models\OrganisationType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrganisationController extends Controller
{
     public function index(): View
    {
        $organisations = Organisation::query()
            ->with('organisationType')
            ->withCount([
                'states',
                'headOffices',
                'vendors',
                'supportGroups',
                'calendars',
            ])
            ->when(
                request('search'),
                function ($query, $search) {

                    $query->where(function ($q) use ($search) {

                        $q->where(
                            'organisation_code',
                            'LIKE',
                            "%{$search}%"
                        )
                        ->orWhere(
                            'organisation_name',
                            'LIKE',"%{$search}%"
                        )
                        ->orWhere(
                            'short_name',
                            'LIKE',
                            "%{$search}%"
                        );

                    });
                }
            )
            ->when(
                request('organisation_type_id'),
                function ($query, $typeId) {

                    $query->where(
                        'organisation_type_id',
                        $typeId
                    );
                }
            )->when(
                request('status') !== null &&
                request('status') !== '',
                function ($query) {

                    $query->where(
                        'is_active',
                        request('status')
                    );
                }
            )
            ->orderBy('organisation_name')
            ->paginate(20)
            ->withQueryString();

        $organisationTypes = OrganisationType::query()
            ->where('is_active', true)
            ->orderBy('organisation_type_name')
            ->get([
                'organisation_type_id',
                'organisation_type_code',
                'organisation_type_name',
            ]);

        return view(
            'admin.organisations.index',
            compact(
                'organisations',
                'organisationTypes'
            )
        );
    }



    public function create(): View
    {
        $organisationTypes = OrganisationType::query()
            ->where('is_active', true)
            ->orderBy('organisation_type_name')
            ->get([
                'organisation_type_id',
                'organisation_type_code',
                'organisation_type_name',
            ]);

        return view(
            'admin.organisations.create',
            compact('organisationTypes')
        );
    }

    public function store(
        OrganisationRequest $request
    ): RedirectResponse {

        DB::transaction(function () use ($request) {

            Organisation::create([

                'organisation_type_id' =>
                    $request->integer(
                        'organisation_type_id'
                    ),

                'organisation_code' =>
                    strtoupper(
                        trim(
                            $request->organisation_code
                        )
                    ),

                'organisation_name' =>
                    trim(
                        $request->organisation_name
                    ),
                    'short_name' =>
                    $request->filled('short_name')
                        ? trim($request->short_name)
                        : null,

                'email' =>
                    $request->filled('email')
                        ? strtolower(
                            trim($request->email)
                        )
                        : null,

                'mobile' =>
                    $request->filled('mobile')
                        ? trim($request->mobile)
                        : null,

                'address_line1' =>
                    $request->address_line1,

                'address_line2' =>
                    $request->address_line2,

                'city' =>
                    $request->city,

                'state_name' =>
                    $request->state_name,

                'country' =>
                    $request->country,

                'pincode' =>
                    $request->pincode,

                'is_active' =>
                    $request->boolean('is_active'),

                     'created_by' =>
                    auth()->id(),
            ]);
        });

        return redirect()
            ->route('admin.organisations.index')
            ->with(
                'success',
                'Organisation created successfully.'
            );
    }


    public function edit(
        Organisation $organisation
    ): View {

        $organisationTypes = OrganisationType::query()
            ->where('is_active', true)
            ->orWhere(
                'organisation_type_id',
                $organisation->organisation_type_id
            )
            ->orderBy('organisation_type_name')
            ->get([
                'organisation_type_id',
                'organisation_type_code',
                'organisation_type_name',
            ]);

        return view(
            'admin.organisations.edit',
            compact(
                'organisation',
                'organisationTypes'
            )
        );
    }


     public function update(
        OrganisationRequest $request,
        Organisation $organisation
    ): RedirectResponse {

        DB::transaction(function () use (
            $request,
            $organisation
        ) {

            $organisation->update([

                'organisation_type_id' =>
                    $request->integer(
                        'organisation_type_id'
                    ),

                'organisation_code' =>
                    strtoupper(
                        trim(
                            $request->organisation_code
                        )
                    ),

                'organisation_name' =>
                    trim(
                        $request->organisation_name
                    ),

                'short_name' =>
                    $request->filled('short_name')
                        ? trim($request->short_name)
                        : null,

                'email' => $request->filled('email')
                        ? strtolower(
                            trim($request->email)
                        )
                        : null,

                'mobile' =>
                    $request->filled('mobile')
                        ? trim($request->mobile)
                        : null,

                'address_line1' =>
                    $request->address_line1,

                'address_line2' =>
                    $request->address_line2,

                'city' =>
                    $request->city,

                'state_name' =>
                    $request->state_name,

                'country' =>
                    $request->country,

                'pincode' =>
                    $request->pincode,

                'is_active' =>
                    $request->boolean('is_active'),

                'updated_by' =>
                    auth()->id(),
            ]);

            });

        return redirect()
            ->route('admin.organisations.index')
            ->with(
                'success',
                'Organisation updated successfully.'
            );
    }


    public function destroy(Organisation $organisation): RedirectResponse {

        /*
         * Organisation is a parent master.
         *
         * Do not allow deletion when dependent
         * master/configuration records exist.
         */

        $dependencies = [

            'mst_head_office'
                => 'Head Office',

            'mst_support_group'
                => 'Support Group',

            'mst_vendor'
                => 'Vendor',

            'mst_working_calendar'
                => 'Working Calendar',

            'mst_state'
                => 'State',
        ];

         foreach ($dependencies as $table => $name) {

            if (
                DB::table($table)
                    ->where(
                        'organisation_id',
                        $organisation->organisation_id
                    )
                    ->exists()
            ) {

                return back()->with(
                    'error',
                    "Organisation cannot be deleted because {$name} records are associated with it."
                );
            }
        }

        if (
            DB::table('txn_issue')
                ->where(
                    'current_owner_organisation_id',
                    $organisation->organisation_id
                )
                ->exists()
        ) {

            return back()->with(
                'error',
                'Organisation cannot be deleted because issues are associated with it.'
            );
        }

        $organisation->delete();

        return redirect()
            ->route('admin.organisations.index')
            ->with(
                'success',
                'Organisation deleted successfully.'
            );
    }



    

}