<div class="row">

    {{-- Organisation Type --}}
    <div class="col-md-6 mb-3">

        <label class="form-label">

            Organisation Type

            <span class="text-danger">*</span>

        </label>

        <select name="organisation_type_id" class="form-select @error('organisation_type_id') is-invalid @enderror"
            required>

            <option value="">
                Select Organisation Type
            </option>

            @foreach(
            $organisationTypes
            as $type
            )

            <option value="{{ $type->organisation_type_id }}" @selected( old( 'organisation_type_id' , $organisation->
                organisation_type_id
                ?? ''
                )
                == $type->organisation_type_id
                )
                >

                {{ $type->organisation_type_name }}

                ({{ $type->organisation_type_code }})

            </option>

            @endforeach

        </select>

        @error('organisation_type_id')

        <div class="invalid-feedback">
            {{ $message }}
        </div>

        @enderror

    </div>


    {{-- Code --}}
    <div class="col-md-6 mb-3">

        <label class="form-label">

            Organisation Code

            <span class="text-danger">*</span>

        </label>

        <input type="text" name="organisation_code" value="{{ old(
                'organisation_code',
                $organisation->organisation_code ?? ''
            ) }}" class="form-control @error('organisation_code') is-invalid @enderror" maxlength="30" required>

        @error('organisation_code')

        <div class="invalid-feedback">
            {{ $message }}
        </div>

        @enderror

    </div>


    {{-- Name --}}
    <div class="col-md-8 mb-3">

        <label class="form-label">

            Organisation Name

            <span class="text-danger">*</span>

        </label>

        <input type="text" name="organisation_name" value="{{ old(
                'organisation_name',
                $organisation->organisation_name ?? ''
            ) }}" class="form-control @error('organisation_name') is-invalid @enderror" maxlength="200" required>

        @error('organisation_name')

        <div class="invalid-feedback">
            {{ $message }}
        </div>

        @enderror

    </div>


    {{-- Short Name --}}
    <div class="col-md-4 mb-3">

        <label class="form-label">
            Short Name
        </label>

        <input type="text" name="short_name" value="{{ old(
                'short_name',
                $organisation->short_name ?? ''
            ) }}" class="form-control @error('short_name') is-invalid @enderror" maxlength="100">

        @error('short_name')

        <div class="invalid-feedback">
            {{ $message }}
        </div>

        @enderror

    </div>


    {{-- Email --}}
    <div class="col-md-6 mb-3">

        <label class="form-label">
            Email
        </label>

        <input type="email" name="email" value="{{ old(
                'email',
                $organisation->email ?? ''
            ) }}" class="form-control @error('email') is-invalid @enderror" maxlength="150">

        @error('email')

        <div class="invalid-feedback">
            {{ $message }}
        </div>

        @enderror

    </div>


    {{-- Mobile --}}
    <div class="col-md-6 mb-3">

        <label class="form-label">
            Mobile
        </label>

        <input type="text" name="mobile" value="{{ old(
                'mobile',
                $organisation->mobile ?? ''
            ) }}" class="form-control @error('mobile') is-invalid @enderror" maxlength="20">

        @error('mobile')

        <div class="invalid-feedback">
            {{ $message }}
        </div>

        @enderror

    </div>


    {{-- Address Line 1 --}}
    <div class="col-md-6 mb-3">

        <label class="form-label">
            Address Line 1
        </label>

        <input type="text" name="address_line1" value="{{ old(
                'address_line1',
                $organisation->address_line1 ?? ''
            ) }}" class="form-control @error('address_line1') is-invalid @enderror" maxlength="250">

        @error('address_line1')

        <div class="invalid-feedback">
            {{ $message }}
        </div>

        @enderror

    </div>


    {{-- Address Line 2 --}}
    <div class="col-md-6 mb-3">

        <label class="form-label">
            Address Line 2
        </label>

        <input type="text" name="address_line2" value="{{ old(
                'address_line2',
                $organisation->address_line2 ?? ''
            ) }}" class="form-control @error('address_line2') is-invalid @enderror" maxlength="250">

        @error('address_line2')

        <div class="invalid-feedback">
            {{ $message }}
        </div>

        @enderror

    </div>


    {{-- City --}}
    <div class="col-md-4 mb-3">

        <label class="form-label">
            City
        </label>

        <input type="text" name="city" value="{{ old(
                'city',
                $organisation->city ?? ''
            ) }}" class="form-control" maxlength="100">

    </div>


    {{-- State --}}
    <div class="col-md-4 mb-3">

        <label class="form-label">
            State
        </label>

        <input type="text" name="state_name" value="{{ old(
                'state_name',
                $organisation->state_name ?? ''
            ) }}" class="form-control" maxlength="100">

    </div>


    {{-- Country --}}
    <div class="col-md-4 mb-3">

        <label class="form-label">

            Country

            <span class="text-danger">*</span>

        </label>

        <input type="text" name="country" value="{{ old(
                'country',
                $organisation->country ?? 'India'
            ) }}" class="form-control" maxlength="100" required>

    </div>


    {{-- Pincode --}}
    <div class="col-md-4 mb-3">

        <label class="form-label">
            Pincode
        </label>

        <input type="text" name="pincode" value="{{ old(
                'pincode',
                $organisation->pincode ?? ''
            ) }}" class="form-control" maxlength="20">

    </div>


    {{-- Active --}}
    <div class="col-md-4 mb-3">

        <label class="form-label">
            Record Status
        </label>

        <div class="form-check form-switch mt-2">

            <input type="hidden" name="is_active" value="0">

            <input type="checkbox" name="is_active" value="1" id="organisation_is_active" class="form-check-input"
                @checked( old( 'is_active' , $organisation->is_active ?? true
            )
            )
            >

            <label class="form-check-label" for="organisation_is_active">
                Active
            </label>

        </div>

    </div>

</div>