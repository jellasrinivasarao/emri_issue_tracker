<div class="row">

    <div class="col-md-6 mb-3">

        <label class="form-label">
            Organisation
            <span class="text-danger">*</span>
        </label>

        <select name="organisation_id" class="form-select @error('organisation_id') is-invalid @enderror" required>

            <option value="">
                Select Organisation
            </option>

            @foreach($organisations as $organisation)

            <option value="{{ $organisation->organisation_id }}" @selected( old( 'organisation_id' , $state->
                organisation_id ?? ''
                ) ==
                $organisation->organisation_id
                )
                >
                {{ $organisation->organisation_name }}
            </option>

            @endforeach

        </select>

        @error('organisation_id')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
        @enderror

    </div>


    <div class="col-md-6 mb-3">

        <label class="form-label">
            State Code
            <span class="text-danger">*</span>
        </label>

        <input type="text" name="state_code" value="{{ old(
                'state_code',
                $state->state_code ?? ''
            ) }}" class="form-control @error('state_code') is-invalid @enderror" maxlength="50" required>

        @error('state_code')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
        @enderror

    </div>


    <div class="col-md-6 mb-3">

        <label class="form-label">
            State Name
            <span class="text-danger">*</span>
        </label>

        <input type="text" name="state_name" value="{{ old(
                'state_name',
                $state->state_name ?? ''
            ) }}" class="form-control @error('state_name') is-invalid @enderror" maxlength="150" required>

        @error('state_name')
        <div class="invalid-feedback">
            {{ $message }}
        </div>
        @enderror

    </div>


    <div class="col-md-6 mb-3">

        <label class="form-label">
            Short Name
        </label>

        <input type="text" name="state_short_name" value="{{ old(
                'state_short_name',
                $state->state_short_name ?? ''
            ) }}" class="form-control" maxlength="20">

    </div>


    <div class="col-md-6 mb-3">

        <div class="form-check form-switch">

            <input type="hidden" name="is_active" value="0">

            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" @checked(
                old( 'is_active' , $state->is_active ?? true
            )
            )
            >

            <label class="form-check-label" for="is_active">
                Active
            </label>

        </div>

    </div>

</div>