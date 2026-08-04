@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h3 class="mb-3">Create Calendar Holiday</h3>

    @include('admin.calendar-holidays._form', [
        'action' => route('admin.calendar-holidays.store'),
        'method' => 'POST',
        'buttonText' => 'Create Holiday',
        'holiday' => null
    ])
</div>
@endsection
