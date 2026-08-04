@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3>{{ $issue->issue_number }}</h3>
            <p class="text-muted mb-0">{{ $issue->title }}</p>
        </div>
        <a href="{{ route('issues.index') }}" class="btn btn-secondary">Back</a>
    </div>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header"><strong>Issue Details</strong></div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Project</dt><dd class="col-sm-8">{{ $issue->project_id }}</dd>
                        <dt class="col-sm-4">Priority</dt><dd class="col-sm-8">{{ $issue->priority }}</dd>
                        <dt class="col-sm-4">HO Intervention</dt><dd class="col-sm-8">{{ $issue->ho_intervention_required ? 'Required' : 'Not Required' }}</dd>
                        <dt class="col-sm-4">Assigned Route</dt><dd class="col-sm-8"><strong>{{ $issue->assigned_route }}</strong></dd>
                        <dt class="col-sm-4">Routing Reason</dt><dd class="col-sm-8">{{ $issue->routing_reason }}</dd>
                        <dt class="col-sm-4">Status</dt><dd class="col-sm-8">{{ $issue->status }}</dd>
                        <dt class="col-sm-4">Description</dt><dd class="col-sm-8">{{ $issue->description ?: '-' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header"><strong>Real-Time Routing</strong></div>
                <div class="card-body">
                    <p class="mb-2">Calendar ID: <strong>{{ $issue->calendar_id }}</strong></p>
                    <p class="mb-2">Route: <strong>{{ $issue->assigned_route }}</strong></p>
                    <p class="mb-0">Reason: <strong>{{ $issue->routing_reason }}</strong></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
