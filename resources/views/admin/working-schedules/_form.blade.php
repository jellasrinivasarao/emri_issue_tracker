<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ $action }}">
            @csrf
            @if($method !== 'POST') @method($method) @endif

            <input type="hidden" name="calendar_id"
                   value="{{ old('calendar_id', $schedule?->calendar_id ?? request('calendar_id')) }}">

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Day of Week <span class="text-danger">*</span></label>
                    <select name="day_of_week" class="form-select" required>
                        @foreach([1=>'Monday',2=>'Tuesday',3=>'Wednesday',4=>'Thursday',5=>'Friday',6=>'Saturday',7=>'Sunday'] as $value => $label)
                            <option value="{{ $value }}" @selected((int)old('day_of_week', $schedule?->day_of_week) === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Sequence</label>
                    <input type="number" min="1" name="sequence_no"
                           value="{{ old('sequence_no', $schedule?->sequence_no ?? 1) }}"
                           class="form-control" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Schedule Name</label>
                    <input type="text" name="schedule_name"
                           value="{{ old('schedule_name', $schedule?->schedule_name) }}"
                           class="form-control" maxlength="100">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Start Time</label>
                    <input type="time" name="start_time"
                           value="{{ old('start_time', $schedule?->start_time) }}"
                           class="form-control">
                </div>

                <div class="col-md-6">
                    <label class="form-label">End Time</label>
                    <input type="time" name="end_time"
                           value="{{ old('end_time', $schedule?->end_time) }}"
                           class="form-control">
                </div>

                <div class="col-12">
                    <div class="form-check">
                        <input type="hidden" name="is_working_day" value="0">
                        <input class="form-check-input" type="checkbox" name="is_working_day"
                               value="1" id="is_working_day"
                               @checked(old('is_working_day', $schedule?->is_working_day ?? true))>
                        <label class="form-check-label" for="is_working_day">Working Day</label>
                    </div>

                    <div class="form-check">
                        <input type="hidden" name="is_24_hours" value="0">
                        <input class="form-check-input" type="checkbox" name="is_24_hours"
                               value="1" id="is_24_hours"
                               @checked(old('is_24_hours', $schedule?->is_24_hours ?? false))>
                        <label class="form-check-label" for="is_24_hours">24 Hours</label>
                    </div>

                    <div class="form-check">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" name="is_active"
                               value="1" id="is_active"
                               @checked(old('is_active', $schedule?->is_active ?? true))>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>

                <div class="col-12 d-flex gap-2">
                    <button class="btn btn-primary">{{ $buttonText }}</button>
                    <a href="{{ route('admin.working-calendars.show', old('calendar_id', $schedule?->calendar_id ?? request('calendar_id'))) }}"
                       class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
