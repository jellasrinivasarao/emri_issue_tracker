@extends('layouts.admin')

@section('title', 'Edit Organisation')

@section('content')

<div class="container-fluid">

    <div class="card">

        <div class="card-header">

            <h5 class="mb-0">
                Edit Organisation
            </h5>

        </div>

        <div class="card-body">

            <form method="POST" action="{{ route('admin.organisations.update',$organisation) }}">

                @csrf

                @method('PUT')

                @include(
                'admin.organisations._form'
                )

                <div class="mt-4">

                    <button type="submit" class="btn btn-primary">
                        Update Organisation
                    </button>

                    <a href="{{ route(
                            'admin.organisations.index'
                        ) }}" class="btn btn-secondary">
                        Cancel
                    </a>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection