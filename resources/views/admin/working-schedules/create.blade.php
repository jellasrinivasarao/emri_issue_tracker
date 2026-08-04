@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h3 class="mb-3">Create Working Schedule</h3>

    @include('admin.working-schedules._form', [
        'action' => route('admin.working-schedules.store'),
        'method' => 'POST',
        'buttonText' => 'Create Schedule',
        'schedule' => null
    ])
</div>
@endsection
