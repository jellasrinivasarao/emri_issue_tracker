@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h3 class="mb-3">Raise Issue</h3>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('issues.store') }}" id="issueForm">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Project ID <span class="text-danger">*</span></label>
                        <input type="number" name="project_id" value="{{ old('project_id') }}" class="form-control"
                            required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Calendar ID <span class="text-danger">*</span></label>
                        <input type="number" name="calendar_id" value="{{ old('calendar_id') }}" class="form-control"
                            required>
                    </div>

                    <div class="col-md-8">
                        <label class="form-label">Issue Title <span class="text-danger">*</span></label>
                        <input type="text" name="title" value="{{ old('title') }}" class="form-control" maxlength="250"
                            required>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Priority</label>
                        <select name="priority" class="form-select">
                            @foreach(['LOW','MEDIUM','HIGH','CRITICAL'] as $priority)
                            <option value="{{ $priority }}" @selected(old('priority','MEDIUM')===$priority)>
                                {{ $priority }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="5" class="form-control">{{ old('description') }}</textarea>
                    </div>

                    <div class="col-12">
                        <div class="form-check">
                            <input type="hidden" name="ho_intervention_required" value="0">
                            <input class="form-check-input" type="checkbox" name="ho_intervention_required" value="1"
                                id="ho_intervention_required" @checked(old('ho_intervention_required'))>
                            <label class="form-check-label" for="ho_intervention_required">
                                HO IT intervention required
                            </label>
                        </div>
                    </div>

                    <div class="col-12">
                        <div id="routePreview" class="alert alert-secondary d-none"></div>
                    </div>

                    <div class="col-12 d-flex gap-2">
                        <button type="button" id="checkRoute" class="btn btn-outline-primary">
                            Check Real-Time Route
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Raise Issue
                        </button>
                        <a href="{{ route('issues.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('checkRoute').addEventListener('click', async function() {
    const calendarId = document.querySelector('[name="calendar_id"]').value;
    const hoRequired = document.querySelector('[name="ho_intervention_required"]:checked')?.value ?? 0;
    const preview = document.getElementById('routePreview');

    if (!calendarId) {
        preview.className = 'alert alert-danger';
        preview.textContent = 'Please enter Calendar ID.';
        preview.classList.remove('d-none');
        return;
    }

    preview.className = 'alert alert-info';
    preview.textContent = 'Checking current working hours...';
    preview.classList.remove('d-none');

    try {
        const response = await fetch('{{ route('
            issues.determine - route ') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    calendar_id: calendarId,
                    ho_intervention_required: hoRequired
                })
            });

        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.message || 'Unable to determine route.');
        }

        const data = result.data;

        preview.className = data.route === 'HO_IT_LEVEL_1' ?
            'alert alert-success' :
            'alert alert-warning';

        preview.innerHTML =
            '<strong>Route: ' + data.route + '</strong><br>' +
            'Reason: ' + data.reason + '<br>' +
            'Working: ' + (data.is_working ? 'Yes' : 'No') +
            (data.calendar?.timezone ? '<br>Timezone: ' + data.calendar.timezone : '');

        preview.classList.remove('d-none');
    } catch (error) {
        preview.className = 'alert alert-danger';
        preview.textContent = error.message;
        preview.classList.remove('d-none');
    }
});
</script>
@endsection