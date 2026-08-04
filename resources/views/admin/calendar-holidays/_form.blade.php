<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ $action }}">
            @csrf
            @if($method !== 'POST') @method($method) @endif

            <input type="hidden" name="calendar_id"
                   value="{{ old('calendar_id', $holiday?->calendar_id ?? request('calendar_id')) }}">

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Holiday Date <span class="text-danger">*</span></label>
                    <input type="date" name="holiday_date"
                           value="{{ old('holiday_date', optional($holiday?->holiday_date)->format('Y-m-d')) }}"
                           class="form-control" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Holiday Code <span class="text-danger">*</span></label>
                    <input type="text" name="holiday_code"
                           value="{{ old('holiday_code', $holiday?->holiday_code) }}"
                           class="form-control" maxlength="50" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Holiday Type <span class="text-danger">*</span></label>
                    <select name="holiday_type" class="form-select" required>
                        @foreach(['PUBLIC','OPTIONAL','ORGANISATION','STATE','SPECIAL'] as $type)
                            <option value="{{ $type }}" @selected(old('holiday_type', $holiday?->holiday_type ?? 'PUBLIC') === $type)>
                                {{ $type }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-8">
                    <label class="form-label">Holiday Name <span class="text-danger">*</span></label>
                    <input type="text" name="holiday_name"
                           value="{{ old('holiday_name', $holiday?->holiday_name) }}"
                           class="form-control" maxlength="150" required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="is_active" class="form-select">
                        <option value="1" @selected((int)old('is_active', $holiday?->is_active ?? 1) === 1)>Active</option>
                        <option value="0" @selected((int)old('is_active', $holiday?->is_active ?? 1) === 0)>Inactive</option>
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="3" class="form-control">{{ old('description', $holiday?->description) }}</textarea>
                </div>

                <div class="col-12">
                    <div class="form-check">
                        <input type="hidden" name="is_working_day_override" value="0">
                        <input class="form-check-input" type="checkbox"
                               name="is_working_day_override"
                               value="1"
                               id="is_working_day_override"
                               @checked(old('is_working_day_override', $holiday?->is_working_day_override ?? false))>
                        <label class="form-check-label" for="is_working_day_override">
                            Override holiday and allow working on this date
                        </label>
                    </div>
                </div>

                <div class="col-12 d-flex gap-2">
                    <button class="btn btn-primary">{{ $buttonText }}</button>
                    <a href="{{ route('admin.working-calendars.show', old('calendar_id', $holiday?->calendar_id ?? request('calendar_id'))) }}"
                       class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
