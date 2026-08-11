@extends('layouts.app')

@section('content')

<div class="container">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Working Schedules</h4>

        <a href="{{ route('working-schedules.create') }}" class="btn btn-primary">
            Add Working Schedule
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    <div class="card">
        <div class="card-body">

            <table class="table table-bordered table-striped">

                <thead>
                    <tr>
                        <th>#</th>
                        <th>Day</th>
                        <th>Schedule</th>
                        <th>Shift</th>
                        <th>Start</th>
                        <th>Break</th>
                        <th>End</th>
                        <th>Working Day</th>
                        <th>24 Hours</th>
                        <th>Effective From</th>
                        <th>Effective To</th>
                        <th>Status</th>
                        <th width="180">Action</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($schedules as $schedule)

                    <tr>

                        <td>
                            {{ $schedules->firstItem() + $loop->index }}
                        </td>

                        <td>
                            {{ $schedule->day_of_week }}
                        </td>

                        <td>
                            {{ $schedule->schedule_name ?? '-' }}
                        </td>

                        <td>
                            {{ $schedule->shift_name ?? '-' }}
                            @if($schedule->shift_no)
                            <small>
                                (#{{ $schedule->shift_no }})
                            </small>
                            @endif
                        </td>

                        <td>
                            {{ $schedule->start_time ?? '-' }}
                        </td>

                        <td>
                            @if($schedule->break_start && $schedule->break_end)
                            {{ $schedule->break_start }}
                            -
                            {{ $schedule->break_end }}
                            @else
                            -
                            @endif
                        </td>

                        <td>
                            {{ $schedule->end_time ?? '-' }}
                        </td>

                        <td>
                            @if($schedule->is_working_day)
                            <span class="badge bg-success">
                                Yes
                            </span>
                            @else
                            <span class="badge bg-secondary">
                                No
                            </span>
                            @endif
                        </td>

                        <td>
                            @if($schedule->is_24_hours)
                            <span class="badge bg-info">
                                Yes
                            </span>
                            @else
                            No
                            @endif
                        </td>

                        <td>
                            {{ optional($schedule->effective_from)->format('d-m-Y') ?? '-' }}
                        </td>

                        <td>
                            {{ optional($schedule->effective_to)->format('d-m-Y') ?? '-' }}
                        </td>

                        <td>
                            @if($schedule->is_active)
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
                                'working-schedules.edit',
                                $schedule->schedule_id
                            ) }}" class="btn btn-sm btn-warning">
                                Edit
                            </a>

                            <form action="{{ route(
                                    'working-schedules.toggle-status',
                                    $schedule->schedule_id
                                ) }}" method="POST" class="d-inline">

                                @csrf
                                @method('PATCH')

                                <button type="submit" class="btn btn-sm btn-secondary">
                                    {{ $schedule->is_active
                                        ? 'Deactivate'
                                        : 'Activate' }}
                                </button>

                            </form>

                            <form action="{{ route(
                                    'working-schedules.destroy',
                                    $schedule->schedule_id
                                ) }}" method="POST" class="d-inline" onsubmit="return confirm(
                                    'Are you sure you want to delete this schedule?'
                                );">

                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-sm btn-danger">
                                    Delete
                                </button>

                            </form>

                        </td>

                    </tr>

                    @empty

                    <tr>
                        <td colspan="13" class="text-center">
                            No working schedules found.
                        </td>
                    </tr>

                    @endforelse

                </tbody>

            </table>

            {{ $schedules->links() }}

        </div>
    </div>

</div>

@endsection