@extends('layouts.admin')

@section('title', 'Create State')

@section('content')

<div class="container-fluid">

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">
                Create State
            </h5>
        </div>

        <div class="card-body">

            <form method="POST" action="{{ route('admin.states.store') }}">

                @csrf

                @include(
                'admin.states._form'
                )

                <div class="mt-4">

                    <button type="submit" class="btn btn-primary">
                        Save
                    </button>

                    <a href="{{ route('admin.states.index') }}" class="btn btn-secondary">
                        Cancel
                    </a>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection