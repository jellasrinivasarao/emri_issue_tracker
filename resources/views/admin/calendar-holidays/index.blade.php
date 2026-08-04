@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3>Calendar Holidays</h3>
            <p class="text-muted mb-0">
                Calendar: {{ $calendar->calendar_name }} ({{ $calendar->calendar_code }})
            </p>
        </div>
        <a href="{{ route('admin.calendar-holidays.create', ['calendar_id' => $calendar->calendar_id]) }}"
           class="btn btn-primary">+ Add Holiday</a>
    </div>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>Date</th>
                        <th>Code</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Override</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($holidays as $holiday)
                        <tr>
                            <td>{{ optional($holiday->holiday_date)->format('d-m-Y') }}</td>
                            <td>{{ $holiday->holiday_code }}</td>
                            <td>{{ $holiday->holiday_name }}</td>
                            <td>{{ $holiday->holiday_type }}</td>
                            <td>{{ $holiday->is_working_day_override ? 'Yes' : 'No' }}</td>
                            <td>{{ $holiday->is_active ? 'Active' : 'Inactive' }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.calendar-holidays.edit', $holiday) }}"
                                   class="btn btn-sm btn-outline-primary">Edit</a>
                                <form method="POST"
                                      action="{{ route('admin.calendar-holidays.destroy', $holiday) }}"
                                      class="d-inline"
                                      onsubmit="return confirm('Delete this holiday?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-4 text-muted">No holidays found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            {{ $holidays->links() }}
        </div>
    </div>
</div>
@endsection
