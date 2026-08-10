@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3>Working Schedules</h3>
            <p class="text-muted mb-0">
                Calendar: {{ $calendar->calendar_name }} ({{ $calendar->calendar_code }})
            </p>
        </div>
        <a href="{{ route('admin.working-schedules.create', ['calendar_id' => $calendar->calendar_id]) }}"
           class="btn btn-primary">+ Add Schedule</a>
    </div>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th>Day</th>
                        <th>Sequence</th>
                        <th>Schedule</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Working</th>
                        <th>24 Hours</th>
                        <th class="text-end">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($schedules as $schedule)
                        <tr>
                            <td>{{ ['','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'][$schedule->day_of_week] ?? $schedule->day_of_week }}</td>
                            <td>{{ $schedule->sequence_no }}</td>
                            <td>{{ $schedule->schedule_name ?? '-' }}</td>
                            <td>{{ $schedule->start_time ?? '-' }}</td>
                            <td>{{ $schedule->end_time ?? '-' }}</td>
                            <td>{{ $schedule->is_working_day ? 'Yes' : 'No' }}</td>
                            <td>{{ $schedule->is_24_hours ? 'Yes' : 'No' }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.working-schedules.edit', $schedule) }}"
                                   class="btn btn-sm btn-outline-primary">Edit</a>
                                <form method="POST"
                                      action="{{ route('admin.working-schedules.destroy', $schedule) }}"
                                      class="d-inline"
                                      onsubmit="return confirm('Delete this schedule?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center py-4 text-muted">No schedules found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            {{ $schedules->links() }}
        </div>
    </div>
</div>
@endsection
