@extends('layouts.app')

@section('content')

<div class="container">

    <h3>Create Working Calendar</h3>

    <form method="POST" action="{{ route('admin.working-calendars.store') }}">

        @csrf

        <div class="row">

            <div class="col-md-6 mb-3">
                <label>Calendar Code</label>

                <input type="text" name="calendar_code" class="form-control" value="{{ old('calendar_code') }}"
                    required>

                @error('calendar_code')
                <div class="text-danger">
                    {{ $message }}
                </div>
                @enderror
            </div>

            <div class="col-md-6 mb-3">

                <label>Calendar Name</label>

                <input type="text" name="calendar_name" class="form-control" value="{{ old('calendar_name') }}"
                    required>

            </div>

            <div class="col-md-12 mb-3">

                <label>Description</label>

                <textarea name="description" class="form-control">{{ old('description') }}</textarea>

            </div>

            <div class="col-md-6 mb-3">

                <label>Organisation ID</label>

                <input type="number" name="organisation_id" class="form-control" value="{{ old('organisation_id') }}">

            </div>

            <div class="col-md-6 mb-3">

                <label>Timezone</label>

                <select name="timezone" class="form-control">

                    <option value="Asia/Kolkata">
                        Asia/Kolkata
                    </option>

                    <option value="Asia/Dubai">
                        Asia/Dubai
                    </option>

                    <option value="UTC">
                        UTC
                    </option>

                </select>

            </div>

            <div class="col-md-6 mb-3">

                <label>Effective From</label>

                <input type="date" name="effective_from" class="form-control">

            </div>

            <div class="col-md-6 mb-3">

                <label>Effective To</label>

                <input type="date" name="effective_to" class="form-control">

            </div>

            <div class="col-md-6">

                <label>
                    <input type="checkbox" name="is_active" value="1" checked>

                    Active
                </label>

            </div>

        </div>

        <button class="btn btn-primary">
            Save Calendar
        </button>

        <a href="{{ route('admin.working-calendars.index') }}" class="btn btn-secondary">
            Cancel
        </a>

    </form>

</div>

@endsection