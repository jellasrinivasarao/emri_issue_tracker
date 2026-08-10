@extends('layouts.admin')

@section('title', 'Project Master')

@section('content')

<div class="container-fluid">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>

            <h4 class="mb-1">
                Project Master
            </h4>

            <small class="text-muted">
                Manage projects and state assignments
            </small>

        </div>

        <a href="{{ route('admin.projects.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i>
            Add Project
        </a>

    </div>


    {{-- Search --}}
    <div class="card mb-3">

        <div class="card-body">

            <form method="GET" action="{{ route('admin.projects.index') }}">

                <div class="row g-3">

                    <div class="col-md-4">

                        <label class="form-label">
                            Search
                        </label>

                        <input type="text" name="search" value="{{ request('search') }}" class="form-control"
                            placeholder="Project code or name">

                    </div>

                    <div class="col-md-3">

                        <label class="form-label">
                            Status
                        </label>

                        <select name="status" class="form-select">

                            <option value="">
                                All
                            </option>

                            <option value="ACTIVE" @selected( request('status')==='ACTIVE' )>
                                Active
                            </option>

                            <option value="INACTIVE" @selected( request('status')==='INACTIVE' )>
                                Inactive
                            </option>

                        </select>

                    </div>

                    <div class="col-md-3 d-flex align-items-end">

                        <button type="submit" class="btn btn-primary me-2">
                            Search
                        </button>

                        <a href="{{ route('admin.projects.index') }}" class="btn btn-outline-secondary">
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
                                Project Code
                            </th>

                            <th>
                                Project Name
                            </th>

                            <th>
                                State
                            </th>

                            <th>
                                Start Date
                            </th>

                            <th>
                                End Date
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

                        @forelse($projects as $project)

                        <tr>

                            <td>
                                {{
                                    $projects->firstItem()
                                    + $loop->index
                                }}
                            </td>

                            <td>

                                <span class="badge bg-secondary">

                                    {{ $project->project_code }}

                                </span>

                            </td>

                            <td>

                                <strong>
                                    {{ $project->project_name }}
                                </strong>

                            </td>

                            <td>

                                {{ $project->state?->state_name ?? '-' }}

                            </td>

                            <td>

                                {{
                                    $project->start_date
                                        ? $project->start_date->format('d-m-Y')
                                        : '-'
                                }}

                            </td>

                            <td>

                                {{
                                    $project->end_date
                                        ? $project->end_date->format('d-m-Y')
                                        : '-'
                                }}

                            </td>

                            <td>

                                @if($project->is_active)

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
                                        'admin.projects.edit',
                                        $project
                                    ) }}" class="btn btn-sm btn-outline-primary">
                                    Edit
                                </a>

                                <form method="POST" action="{{ route(
                                        'admin.projects.destroy',
                                        $project
                                    ) }}" class="d-inline" onsubmit="
                                        return confirm(
                                            'Are you sure you want to delete this project?'
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
                                No projects found.
                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            <div class="mt-3">

                {{ $projects->links() }}

            </div>

        </div>

    </div>

</div>

@endsection