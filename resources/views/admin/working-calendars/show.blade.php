@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3>{{ $calendar->calendar_name }}</h3>
            <p class="text-muted mb-0">{{ $calendar->calendar_code }}</p>
        </div>
        <div>
            <a href="{{ route('admin.working-calendars.edit', $calendar) }}" class="btn btn-primary">Edit</a>
            <a href="{{ route('admin.working-calendars.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-header"><strong>Calendar Details</strong></div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5">ID</dt><dd class="col-sm-7">{{ $calendar->calendar_id }}</dd>
                        <dt class="col-sm-5">Code</dt><dd class="col-sm-7">{{ $calendar->calendar_code }}</dd>
                        <dt class="col-sm-5">Name</dt><dd class="col-sm-7">{{ $calendar->calendar_name }}</dd>
                        <dt class="col-sm-5">Organisation</dt><dd class="col-sm-7">{{ $calendar->organisation_id ?? 'All' }}</dd>
                        <dt class="col-sm-5">Timezone</dt><dd class="col-sm-7">{{ $calendar->timezone }}</dd>
                        <dt class="col-sm-5">Effective</dt>
                        <dd class="col-sm-7">
                            {{ optional($calendar->effective_from)->format('d-m-Y') ?? '-' }}
                            to
                            {{ optional($calendar->effective_to)->format('d-m-Y') ?? 'Open' }}
                        </dd>
                        <dt class="col-sm-5">Version</dt><dd class="col-sm-7">{{ $calendar->version_no }}</dd>
                        <dt class="col-sm-5">Status</dt>
                        <dd class="col-sm-7">
                            @if($calendar->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between">
                    <strong>Working Schedule</strong>
                    <a href="{{ route('admin.working-schedules.create', ['calendar_id' => $calendar->calendar_id]) }}"
                       class="btn btn-sm btn-primary">Add Schedule</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                            <tr>
                                <th>Day</th>
                                <th>Time</th>
                                <th>Working</th>
                                <th>24 Hours</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($calendar->schedules as $schedule)
                                <tr>
                                    <td>{{ ['','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'][$schedule->day_of_week] ?? $schedule->day_of_week }}</td>
                                    <td>
                                        @if($schedule->is_24_hours)
                                            24 Hours
                                        @elseif($schedule->start_time && $schedule->end_time)
                                            {{ $schedule->start_time }} - {{ $schedule->end_time }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ $schedule->is_working_day ? 'Yes' : 'No' }}</td>
                                    <td>{{ $schedule->is_24_hours ? 'Yes' : 'No' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center py-3 text-muted">No schedules configured.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between">
                    <strong>Calendar Holidays</strong>
                    <a href="{{ route('admin.calendar-holidays.create', ['calendar_id' => $calendar->calendar_id]) }}"
                       class="btn btn-sm btn-primary">Add Holiday</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm mb-0">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Override Working Day</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($calendar->holidays as $holiday)
                                <tr>
                                    <td>{{ optional($holiday->holiday_date)->format('d-m-Y') }}</td>
                                    <td>{{ $holiday->holiday_code }}</td>
                                    <td>{{ $holiday->holiday_name }}</td>
                                    <td>{{ $holiday->holiday_type }}</td>
                                    <td>{{ $holiday->is_working_day_override ? 'Yes' : 'No' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center py-3 text-muted">No holidays configured.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
