@extends('layouts.app')

@section('content')

<div class="container">

    <div class="card">

        <div class="card-header">
            <h4 class="mb-0">Edit Working Schedule</h4>
        </div>

        <div class="card-body">

            <form action="{{ route(
                    'working-schedules.update',
                    $workingSchedule->schedule_id
                ) }}" method="POST">

                @method('PUT')

                @include('working-schedules._form')

            </form>

        </div>

    </div>

</div>

@endsection