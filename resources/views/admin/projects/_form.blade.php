<div class="row">

    {{-- State --}}
    <div class="col-md-6 mb-3">

        <label class="form-label">

            State

            <span class="text-danger">
                *
            </span>

        </label>

        <select name="state_id" class="form-select @error('state_id') is-invalid @enderror" required>

            <option value="">
                Select State
            </option>

            @foreach($states as $state)

            <option value="{{ $state->state_id }}" @selected( old( 'state_id' , $project->state_id ?? ''
                ) == $state->state_id
                )
                >

                {{ $state->state_name }}
                ({{ $state->state_code }})

            </option>

            @endforeach

        </select>

        @error('state_id')

        <div class="invalid-feedback">
            {{ $message }}
        </div>

        @enderror

    </div>


    {{-- Project Code --}}
    <div class="col-md-6 mb-3">

        <label class="form-label">

            Project Code

            <span class="text-danger">
                *
            </span>

        </label>

        <input type="text" name="project_code" value="{{ old(
                'project_code',
                $project->project_code ?? ''
            ) }}" class="form-control @error('project_code') is-invalid @enderror" maxlength="50" required>

        @error('project_code')

        <div class="invalid-feedback">
            {{ $message }}
        </div>

        @enderror

    </div>


    {{-- Project Name --}}
    <div class="col-md-12 mb-3">

        <label class="form-label">

            Project Name

            <span class="text-danger">
                *
            </span>

        </label>

        <input type="text" name="project_name" value="{{ old(
                'project_name',
                $project->project_name ?? ''
            ) }}" class="form-control @error('project_name') is-invalid @enderror" maxlength="200" required>

        @error('project_name')

        <div class="invalid-feedback">
            {{ $message }}
        </div>

        @enderror

    </div>


    {{-- Description --}}
    <div class="col-md-12 mb-3">

        <label class="form-label">
            Project Description
        </label>

        <textarea name="project_description" rows="4"
            class="form-control @error('project_description') is-invalid @enderror">{{ old(
            'project_description',
            $project->project_description ?? ''
        ) }}</textarea>

        @error('project_description')

        <div class="invalid-feedback">
            {{ $message }}
        </div>

        @enderror

    </div>


    {{-- Start Date --}}
    <div class="col-md-6 mb-3">

        <label class="form-label">
            Start Date
        </label>

        <input type="date" name="start_date" value="{{ old(
                'start_date',
                isset($project) && $project->start_date
                    ? $project->start_date->format('Y-m-d')
                    : ''
            ) }}" class="form-control @error('start_date') is-invalid @enderror">

        @error('start_date')

        <div class="invalid-feedback">
            {{ $message }}
        </div>

        @enderror

    </div>


    {{-- End Date --}}
    <div class="col-md-6 mb-3">

        <label class="form-label">
            End Date
        </label>

        <input type="date" name="end_date" value="{{ old(
                'end_date',
                isset($project) && $project->end_date
                    ? $project->end_date->format('Y-m-d')
                    : ''
            ) }}" class="form-control @error('end_date') is-invalid @enderror">

        @error('end_date')

        <div class="invalid-feedback">
            {{ $message }}
        </div>

        @enderror

    </div>


    {{-- Project Status --}}
    <div class="col-md-6 mb-3">

        <label class="form-label">

            Project Status

            <span class="text-danger">
                *
            </span>

        </label>

        <select name="project_status" class="form-select @error('project_status') is-invalid @enderror" required>

            @php

            $statuses = [
            'ACTIVE' => 'Active',
            'INACTIVE' => 'Inactive',
            'COMPLETED' => 'Completed',
            'SUSPENDED' => 'Suspended',
            ];

            @endphp

            @foreach($statuses as $value => $label)

            <option value="{{ $value }}" @selected( old( 'project_status' , $project->project_status ?? 'ACTIVE'
                ) === $value
                )
                >
                {{ $label }}
            </option>

            @endforeach

        </select>

        @error('project_status')

        <div class="invalid-feedback">
            {{ $message }}
        </div>

        @enderror

    </div>


    {{-- Active --}}
    <div class="col-md-6 mb-3">

        <label class="form-label">
            Record Status
        </label>

        <div class="form-check form-switch mt-2">

            <input type="hidden" name="is_active" value="0">

            <input type="checkbox" name="is_active" value="1" id="is_active" class="form-check-input" @checked(
                old( 'is_active' , $project->is_active ?? true
            )
            )
            >

            <label class="form-check-label" for="is_active">
                Active
            </label>

        </div>

    </div>

</div>