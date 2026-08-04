@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h3 class="mb-3">Edit Working Schedule</h3>

    @include('admin.working-schedules._form', [
        'action' => route('admin.working-schedules.update', $schedule),
        'method' => 'PUT',
        'buttonText' => 'Update Schedule',
        'schedule' => $schedule
    ])
</div>
@endsection
