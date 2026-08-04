@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-3">
        <h3>Create Working Calendar</h3>
    </div>

    @include('admin.working-calendars._form', [
        'action' => route('admin.working-calendars.store'),
        'method' => 'POST',
        'buttonText' => 'Create Calendar',
        'calendar' => null
    ])
</div>
@endsection
