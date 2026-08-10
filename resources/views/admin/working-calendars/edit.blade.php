@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-3">
        <h3>Edit Working Calendar</h3>
    </div>

    @include('admin.working-calendars._form', [
        'action' => route('admin.working-calendars.update', $calendar),
        'method' => 'PUT',
        'buttonText' => 'Update Calendar',
        'calendar' => $calendar
    ])
</div>
@endsection
