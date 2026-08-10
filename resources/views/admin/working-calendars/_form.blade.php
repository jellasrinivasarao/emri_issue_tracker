<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ $action }}" method="POST">
            @csrf
            @if($method !== 'POST')
                @method($method)
            @endif

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Calendar Code <span class="text-danger">*</span></label>
                    <input type="text" name="calendar_code"
                           value="{{ old('calendar_code', $calendar?->calendar_code) }}"
                           class="form-control @error('calendar_code') is-invalid @enderror"
                           maxlength="50" required>
                    @error('calendar_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Calendar Name <span class="text-danger">*</span></label>
                    <input type="text" name="calendar_name"
                           value="{{ old('calendar_name', $calendar?->calendar_name) }}"
                           class="form-control @error('calendar_name') is-invalid @enderror"
                           maxlength="150" required>
                    @error('calendar_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="3"
                              class="form-control @error('description') is-invalid @enderror">{{ old('description', $calendar?->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Organisation ID</label>
                    <input type="number" name="organisation_id"
                           value="{{ old('organisation_id', $calendar?->organisation_id) }}"
                           class="form-control @error('organisation_id') is-invalid @enderror">
                    @error('organisation_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Timezone <span class="text-danger">*</span></label>
                    <select name="timezone" class="form-select @error('timezone') is-invalid @enderror" required>
                        @foreach(['Asia/Kolkata', 'Asia/Dubai', 'Asia/Singapore', 'Europe/London', 'America/New_York', 'UTC'] as $timezone)
                            <option value="{{ $timezone }}"
                                @selected(old('timezone', $calendar?->timezone ?? 'Asia/Kolkata') === $timezone)>
                                {{ $timezone }}
                            </option>
                        @endforeach
                    </select>
                    @error('timezone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Version</label>
                    <input type="number" value="{{ $calendar?->version_no ?? 1 }}"
                           class="form-control" readonly>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Effective From</label>
                    <input type="date" name="effective_from"
                           value="{{ old('effective_from', optional($calendar?->effective_from)->format('Y-m-d')) }}"
                           class="form-control @error('effective_from') is-invalid @enderror">
                    @error('effective_from')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Effective To</label>
                    <input type="date" name="effective_to"
                           value="{{ old('effective_to', optional($calendar?->effective_to)->format('Y-m-d')) }}"
                           class="form-control @error('effective_to') is-invalid @enderror">
                    @error('effective_to')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <div class="form-check">
                        <input type="hidden" name="is_active" value="0">
                        <input class="form-check-input" type="checkbox" name="is_active"
                               value="1" id="is_active"
                               @checked(old('is_active', $calendar?->is_active ?? true))>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>

                <div class="col-12 d-flex gap-2">
                    <button class="btn btn-primary">{{ $buttonText }}</button>
                    <a href="{{ route('admin.working-calendars.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
