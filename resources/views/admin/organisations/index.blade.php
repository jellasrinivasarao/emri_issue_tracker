@extends('layouts.admin')

@section('title', 'Organisation Master')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h4 class="mb-1">
                Organisation Master
            </h4>

            <small class="text-muted">
                Manage organisations and organisational hierarchy
            </small>

        </div>

        <a href="{{ route('admin.organisations.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i>
            Add Organisation
        </a>

    </div>


    {{-- Flash Messages --}}
    @if(session('success'))

    <div class="alert alert-success alert-dismissible fade show">

        {{ session('success') }}

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

    </div>

    @endif


    @if(session('error'))

    <div class="alert alert-danger alert-dismissible fade show">

        {{ session('error') }}

        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>

    </div>

    @endif


    {{-- Search --}}
    <div class="card mb-3">

        <div class="card-body">

            <form method="GET" action="{{ route('admin.organisations.index') }}">

                <div class="row g-3">

                    {{-- Search --}}
                    <div class="col-md-4">

                        <label class="form-label">
                            Search
                        </label>

                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="Code, name or short name">

                    </div>


                    {{-- Organisation Type --}}
                    <div class="col-md-3">

                        <label class="form-label">
                            Organisation Type
                        </label>

                        <select name="organisation_type_id" class="form-select">

                            <option value="">
                                All Types
                            </option>

                            @foreach(
                            $organisationTypes
                            as $type
                            )

                            <option value="{{ $type->organisation_type_id }}" @selected( request( 'organisation_type_id'
                                )==$type->organisation_type_id
                                )
                                >

                                {{ $type->organisation_type_name }}

                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Status --}}
                    <div class="col-md-2">

                        <label class="form-label">
                            Status
                        </label>

                        <select name="status" class="form-select">

                            <option value="">
                                All
                            </option>

                            <option value="1" @selected( request('status')==='1' )>
                                Active
                            </option>

                            <option value="0" @selected( request('status')==='0' )>
                                Inactive
                            </option>

                        </select>

                    </div>


                    {{-- Buttons --}}
                    <div class="col-md-3 d-flex align-items-end">

                        <button type="submit" class="btn btn-primary me-2">
                            Search
                        </button>

                        <a href="{{ route(
                                'admin.organisations.index'
                            ) }}" class="btn btn-outline-secondary">
                            Reset
                        </a>

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- Table --}}
    <div class="card">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead>

                        <tr>

                            <th width="60">
                                #
                            </th>

                            <th>
                                Code
                            </th>

                            <th>
                                Organisation
                            </th>

                            <th>
                                Type
                            </th>

                            <th>
                                Contact
                            </th>

                            <th>
                                Location
                            </th>

                            <th>
                                Status
                            </th>

                            <th width="180">
                                Actions
                            </th>

                        </tr>

                    </thead>

                    <tbody>

                        @forelse(
                        $organisations
                        as $organisation
                        )

                        <tr>

                            <td>

                                {{
                                    $organisations->firstItem()
                                    + $loop->index
                                }}

                            </td>


                            <td>

                                <span class="badge bg-secondary">

                                    {{
                                        $organisation
                                            ->organisation_code
                                    }}

                                </span>

                            </td>


                            <td>

                                <strong>

                                    {{
                                        $organisation
                                            ->organisation_name
                                    }}

                                </strong>

                                @if(
                                $organisation->short_name
                                )

                                <br>

                                <small class="text-muted">

                                    {{
                                            $organisation
                                                ->short_name
                                        }}

                                </small>

                                @endif

                            </td>


                            <td>

                                {{
                                    $organisation
                                        ->organisationType
                                        ?->organisation_type_name
                                    ?? '-'
                                }}

                            </td>


                            <td>

                                @if($organisation->email)

                                <div>
                                    {{ $organisation->email }}
                                </div>

                                @endif

                                @if($organisation->mobile)

                                <small class="text-muted">

                                    {{
                                            $organisation->mobile
                                        }}

                                </small>

                                @endif

                                @if(
                                !$organisation->email &&
                                !$organisation->mobile
                                )

                                -

                                @endif

                            </td>


                            <td>

                                {{
                                    $organisation->city
                                    ?? '-'
                                }}

                                @if(
                                $organisation->state_name
                                )

                                <br>

                                <small class="text-muted">

                                    {{
                                            $organisation->state_name
                                        }}

                                </small>

                                @endif

                            </td>


                            <td>

                                @if(
                                $organisation->is_active
                                )

                                <span class="badge bg-success">
                                    Active
                                </span>

                                @else

                                <span class="badge bg-danger">
                                    Inactive
                                </span>

                                @endif

                            </td>


                            <td>

                                <a href="{{ route(
                                        'admin.organisations.edit',
                                        $organisation
                                    ) }}" class="btn btn-sm btn-outline-primary">
                                    Edit
                                </a>


                                <form method="POST" action="{{ route(
                                        'admin.organisations.destroy',
                                        $organisation
                                    ) }}" class="d-inline" onsubmit="
                                        return confirm(
                                            'Are you sure you want to delete this organisation?'
                                        );
                                    ">

                                    @csrf

                                    @method('DELETE')

                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        Delete
                                    </button>

                                </form>

                            </td>

                        </tr>

                        @empty

                        <tr>

                            <td colspan="8" class="text-center text-muted py-4">
                                No organisations found.
                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            <div class="mt-3">

                {{ $organisations->links() }}

            </div>

        </div>

    </div>

</div>

@endsection