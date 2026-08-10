@extends('layouts.admin')

@section('title', 'Edit State')

@section('content')

<div class="container-fluid">

    <div class="card">

        <div class="card-header">
            <h5 class="mb-0">
                Edit State
            </h5>
        </div>

        <div class="card-body">

            <form method="POST" action="{{ route('admin.states.update',$state) }}">

                @csrf

                @method('PUT')

                @include('admin.states._form')

                <div class="mt-4">

                    <button type="submit" class="btn btn-primary">
                        Update
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