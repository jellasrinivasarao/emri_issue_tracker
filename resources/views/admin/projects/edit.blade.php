@extends('layouts.admin')

@section('title', 'Edit Project')

@section('content')

<div class="container-fluid">

    <div class="card">

        <div class="card-header">

            <h5 class="mb-0">
                Edit Project
            </h5>

        </div>

        <div class="card-body">

            <form method="POST" action="{{ route(
                    'admin.projects.update',
                    $project
                ) }}">

                @csrf

                @method('PUT')

                @include(
                'admin.projects._form'
                )

                <div class="mt-4">

                    <button type="submit" class="btn btn-primary">
                        Update Project
                    </button>

                    <a href="{{ route('admin.projects.index') }}" class="btn btn-secondary">
                        Cancel
                    </a>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection