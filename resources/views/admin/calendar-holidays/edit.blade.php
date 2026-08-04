@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h3 class="mb-3">Edit Calendar Holiday</h3>

    @include('admin.calendar-holidays._form', [
        'action' => route('admin.calendar-holidays.update', $holiday),
        'method' => 'PUT',
        'buttonText' => 'Update Holiday',
        'holiday' => $holiday
    ])
</div>
@endsection
