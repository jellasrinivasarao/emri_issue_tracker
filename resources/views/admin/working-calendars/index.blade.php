@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-1">Working Calendars</h3>
            <p class="text-muted mb-0">Manage organisation-level working calendars.</p>
        </div>
        <a href="{{ route('admin.working-calendars.create') }}" class="btn btn-primary">
            + Create Calendar
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Organisation</th>
                            <th>Timezone</th>
                            <th>Effective From</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($calendars as $calendar)
                        <tr>
                            <td>{{ $calendar->calendar_id }}</td>
                            <td><strong>{{ $calendar->calendar_code }}</strong></td>
                            <td>{{ $calendar->calendar_name }}</td>
                            <td>{{ $calendar->organisation_id ?? 'All' }}</td>
                            <td>{{ $calendar->timezone }}</td>
                            <td>{{ optional($calendar->effective_from)->format('d-m-Y') ?? '-' }}</td>
                            <td>
                                @if($calendar->is_active)
                                <span class="badge bg-success">Active</span>
                                @else
                                <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.working-calendars.show', $calendar) }}"
                                    class="btn btn-sm btn-outline-info">View</a>
                                <a href="{{ route('admin.working-calendars.edit', $calendar) }}"
                                    class="btn btn-sm btn-outline-primary">Edit</a>
                                <form action="{{ route('admin.working-calendars.destroy', $calendar) }}" method="POST"
                                    class="d-inline" onsubmit="return confirm('Delete this calendar?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                No working calendars found.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $calendars->links() }}
            </div>
        </div>
    </div>
</div>
@endsection