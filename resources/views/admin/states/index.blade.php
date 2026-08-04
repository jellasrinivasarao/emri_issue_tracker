@extends('layouts.admin')

@section('title', 'State Master')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="mb-1">
                State Master
            </h4>

            <small class="text-muted">
                Manage states mapped to organisations
            </small>
        </div>

        <a href="{{ route('admin.states.create') }}" class="btn btn-primary">+ Add State</a>

    </div>


    <div class="card">

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover">

                    <thead>

                        <tr>
                            <th>#</th>
                            <th>Organisation</th>
                            <th>Code</th>
                            <th>State Name</th>
                            <th>Short Name</th>
                            <th>Status</th>
                            <th width="150">Action</th>
                        </tr>

                    </thead>

                    <tbody>

                        @forelse($states as $state)

                        <tr>

                            <td>
                                {{ $states->firstItem() + $loop->index }}
                            </td>

                            <td>
                                {{ $state->organisation?->organisation_name ?? '-' }}
                            </td>

                            <td>
                                <span class="badge bg-secondary">
                                    {{ $state->state_code }}
                                </span>
                            </td>

                            <td>
                                {{ $state->state_name }}
                            </td>

                            <td>
                                {{ $state->state_short_name ?? '-' }}
                            </td>

                            <td>

                                @if($state->is_active)

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
                                        'admin.states.edit',
                                        $state
                                    ) }}" class="btn btn-sm btn-outline-primary">
                                    Edit
                                </a>

                                <form method="POST" action="{{ route(
                                        'admin.states.destroy',
                                        $state
                                    ) }}" class="d-inline" onsubmit="
                                        return confirm(
                                            'Are you sure you want to delete this state?'
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

                            <td colspan="7" class="text-center text-muted">
                                No states found.
                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            <div class="mt-3">

                {{ $states->links() }}

            </div>

        </div>

    </div>

</div>

@endsection