@csrf

<div class="row">

    {{-- Calendar --}}
    <div class="col-md-4 mb-3">
        <label class="form-label">
            Calendar <span class="text-danger">*</span>
        </label>

        <input type="number" name="calendar_id" class="form-control @error('calendar_id') is-invalid @enderror" value="{{ old(
                'calendar_id',
                $workingSchedule->calendar_id ?? 1
            ) }}" required>

        @error('calendar_id')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
        @enderror
    </div>


    {{-- Day --}}
    <div class="col-md-4 mb-3">

        <label class="form-label">
            Day <span class="text-danger">*</span>
        </label>

        <select name="day_of_week" class="form-select @error('day_of_week') is-invalid @enderror" required>

            @foreach([
            'MONDAY',
            'TUESDAY',
            'WEDNESDAY',
            'THURSDAY',
            'FRIDAY',
            'SATURDAY',
            'SUNDAY'
            ] as $day)

            <option value="{{ $day }}" @selected( old( 'day_of_week' , $workingSchedule->day_of_week ?? ''
                ) === $day
                )
                >
                {{ $day }}
            </option>

            @endforeach

        </select>

        @error('day_of_week')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
        @enderror

    </div>


    {{-- Schedule Name --}}
    <div class="col-md-4 mb-3">

        <label class="form-label">
            Schedule Name
        </label>

        <input type="text" name="schedule_name" class="form-control" maxlength="100" value="{{ old(
                'schedule_name',
                $workingSchedule->schedule_name ?? 'General Shift'
            ) }}">

    </div>


    {{-- Shift Name --}}
    <div class="col-md-4 mb-3">

        <label class="form-label">
            Shift Name
        </label>

        <input type="text" name="shift_name" class="form-control" maxlength="50" value="{{ old(
                'shift_name',
                $workingSchedule->shift_name ?? 'General'
            ) }}">

    </div>


    {{-- Shift No --}}
    <div class="col-md-2 mb-3">

        <label class="form-label">
            Shift No
        </label>

        <input type="number" name="shift_no" min="1" max="255" class="form-control" value="{{ old(
                'shift_no',
                $workingSchedule->shift_no ?? 1
            ) }}">

    </div>


    {{-- Sequence --}}
    <div class="col-md-2 mb-3">

        <label class="form-label">
            Sequence No
        </label>

        <input type="number" name="sequence_no" min="1" class="form-control" value="{{ old(
                'sequence_no',
                $workingSchedule->sequence_no ?? 1
            ) }}">

    </div>


    {{-- Start --}}
    <div class="col-md-3 mb-3">

        <label class="form-label">
            Start Time
        </label>

        <input type="time" name="start_time" class="form-control" value="{{ old(
                'start_time',
                isset($workingSchedule)
                    ? substr($workingSchedule->start_time ?? '', 0, 5)
                    : '09:00'
            ) }}">

    </div>


    {{-- End --}}
    <div class="col-md-3 mb-3">

        <label class="form-label">
            End Time
        </label>

        <input type="time" name="end_time" class="form-control" value="{{ old(
                'end_time',
                isset($workingSchedule)
                    ? substr($workingSchedule->end_time ?? '', 0, 5)
                    : '18:00'
            ) }}">

    </div>


    {{-- Break Start --}}
    <div class="col-md-3 mb-3">

        <label class="form-label">
            Break Start
        </label>

        <input type="time" name="break_start" class="form-control" value="{{ old(
                'break_start',
                isset($workingSchedule)
                    ? substr($workingSchedule->break_start ?? '', 0, 5)
                    : '13:00'
            ) }}">

    </div>


    {{-- Break End --}}
    <div class="col-md-3 mb-3">

        <label class="form-label">
            Break End
        </label>

        <input type="time" name="break_end" class="form-control" value="{{ old(
                'break_end',
                isset($workingSchedule)
                    ? substr($workingSchedule->break_end ?? '', 0, 5)
                    : '14:00'
            ) }}">

    </div>


    {{-- Effective From --}}
    <div class="col-md-3 mb-3">

        <label class="form-label">
            Effective From
        </label>

        <input type="date" name="effective_from" class="form-control" value="{{ old(
                'effective_from',
                isset($workingSchedule)
                    ? optional($workingSchedule->effective_from)
                        ->format('Y-m-d')
                    : '2026-01-01'
            ) }}">

    </div>


    {{-- Effective To --}}
    <div class="col-md-3 mb-3">

        <label class="form-label">
            Effective To
        </label>

        <input type="date" name="effective_to" class="form-control" value="{{ old(
                'effective_to',
                isset($workingSchedule)
                    ? optional($workingSchedule->effective_to)
                        ->format('Y-m-d')
                    : ''
            ) }}">

    </div>


    {{-- Working Day --}}
    <div class="col-md-2 mb-3">

        <label class="form-label d-block">
            Working Day
        </label>

        <div class="form-check form-switch">

            <input type="hidden" name="is_working_day" value="0">

            <input class="form-check-input" type="checkbox" name="is_working_day" value="1"
                @checked(old( 'is_working_day' , $workingSchedule->is_working_day ?? 1
            ))
            >

            <label class="form-check-label">
                Working
            </label>

        </div>

    </div>


    {{-- 24 Hours --}}
    <div class="col-md-2 mb-3">

        <label class="form-label d-block">
            24 Hours
        </label>

        <div class="form-check form-switch">

            <input type="hidden" name="is_24_hours" value="0">

            <input class="form-check-input" type="checkbox" name="is_24_hours" value="1" @checked(old( 'is_24_hours' ,
                $workingSchedule->is_24_hours ?? 0
            ))
            >

            <label class="form-check-label">
                Yes
            </label>

        </div>

    </div>


    {{-- Active --}}
    <div class="col-md-2 mb-3">

        <label class="form-label d-block">
            Status
        </label>

        <div class="form-check form-switch">

            <input type="hidden" name="is_active" value="0">

            <input class="form-check-input" type="checkbox" name="is_active" value="1" @checked(old( 'is_active' ,
                $workingSchedule->is_active ?? 1
            ))
            >

            <label class="form-check-label">
                Active
            </label>

        </div>

    </div>

</div>

<button type="submit" class="btn btn-primary">
    {{ isset($workingSchedule) ? 'Update' : 'Save' }}
</button>

<a href="{{ route('working-schedules.index') }}" class="btn btn-secondary">
    Cancel
</a>