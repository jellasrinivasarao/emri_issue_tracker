@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3>Issue Tracker</h3>
            <p class="text-muted mb-0">Real-time issue routing and resolution tracking.</p>
        </div>
        <a href="{{ route('issues.create') }}" class="btn btn-primary">+ Raise Issue</a>
    </div>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                    <tr>
                        <th>Issue</th>
                        <th>Project</th>
                        <th>Priority</th>
                        <th>HO Intervention</th>
                        <th>Route</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th class="text-end">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($issues as $issue)
                        <tr>
                            <td><strong>{{ $issue->issue_number }}</strong><br><small>{{ $issue->title }}</small></td>
                            <td>{{ $issue->project_id }}</td>
                            <td>{{ $issue->priority }}</td>
                            <td>{{ $issue->ho_intervention_required ? 'Yes' : 'No' }}</td>
                            <td>
                                @if($issue->assigned_route === 'HO_IT_LEVEL_1')
                                    <span class="badge bg-primary">HO IT L1</span>
                                @else
                                    <span class="badge bg-warning text-dark">Vendor L2</span>
                                @endif
                            </td>
                            <td><span class="badge bg-secondary">{{ $issue->status }}</span></td>
                            <td>{{ optional($issue->created_at)->format('d-m-Y H:i') }}</td>
                            <td class="text-end">
                                <a href="{{ route('issues.show', $issue) }}" class="btn btn-sm btn-outline-info">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center py-4 text-muted">No issues found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            {{ $issues->links() }}
        </div>
    </div>
</div>
@endsection
