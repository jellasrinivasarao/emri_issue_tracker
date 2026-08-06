{{-- ========================================================= --}}
{{-- Project / Application / Service / Priority / Calendar --}}
{{-- ========================================================= --}}

<div class="grid grid-cols-1 gap-6 md:grid-cols-2">

    {{-- ===================================================== --}}
    {{-- Project --}}
    {{-- ===================================================== --}}

    <div>

        <label class="mb-2 block text-sm font-medium text-slate-700">
            Project
            <span class="text-red-500">*</span>
        </label>

        <select id="project_id" name="project_id" class="w-full rounded-xl border border-slate-300 px-4 py-2.5
                   focus:border-blue-500
                   focus:ring-2
                   focus:ring-blue-200">

            <option value="">
                Select Project
            </option>

            @foreach($projects as $project)

            <option value="{{ $project->project_id }}" @selected(old('project_id', $policy->project_id ?? '') ==
                $project->project_id)>

                {{ $project->project_name }}

            </option>

            @endforeach

        </select>

        @error('project_id')

        <p class="mt-1 text-xs text-red-600">

            {{ $message }}

        </p>

        @enderror

    </div>

    {{-- ===================================================== --}}
    {{-- Application --}}
    {{-- ===================================================== --}}

    <div>

        <label class="mb-2 block text-sm font-medium text-slate-700">
            Application
            <span class="text-red-500">*</span>
        </label>

        <select id="application_id" name="application_id" class="w-full rounded-xl border border-slate-300 px-4 py-2.5">

            <option value="">
                Select Application
            </option>

            @foreach($applications as $application)

            <option value="{{ $application->application_id }}" @selected(old('application_id', $policy->application_id
                ?? '') == $application->application_id)>

                {{ $application->application_name }}

            </option>

            @endforeach

        </select>

        @error('application_id')

        <p class="mt-1 text-xs text-red-600">

            {{ $message }}

        </p>

        @enderror

    </div>

    {{-- ===================================================== --}}
    {{-- Service --}}
    {{-- ===================================================== --}}

    <div>

        <label class="mb-2 block text-sm font-medium text-slate-700">
            Service
            <span class="text-red-500">*</span>
        </label>

        <select id="service_id" name="service_id" class="w-full rounded-xl border border-slate-300 px-4 py-2.5">

            <option value="">
                Select Service
            </option>

            @foreach($services as $service)

            <option value="{{ $service->service_id }}" @selected(old('service_id', $policy->service_id ?? '') ==
                $service->service_id)>

                {{ $service->service_name }}

            </option>

            @endforeach

        </select>

        @error('service_id')

        <p class="mt-1 text-xs text-red-600">

            {{ $message }}

        </p>

        @enderror

    </div>

    {{-- ===================================================== --}}
    {{-- Priority --}}
    {{-- ===================================================== --}}

    <div>

        <label class="mb-2 block text-sm font-medium text-slate-700">
            Priority
            <span class="text-red-500">*</span>
        </label>

        <select id="priority_id" name="priority_id" class="w-full rounded-xl border border-slate-300 px-4 py-2.5">

            <option value="">
                Select Priority
            </option>

            @foreach($priorities as $priority)

            <option value="{{ $priority->priority_id }}" @selected(old('priority_id', $policy->priority_id ?? '') ==
                $priority->priority_id)>

                {{ $priority->priority_name }}

            </option>

            @endforeach

        </select>

        @error('priority_id')

        <p class="mt-1 text-xs text-red-600">

            {{ $message }}

        </p>

        @enderror

    </div>

    {{-- ===================================================== --}}
    {{-- Working Calendar --}}
    {{-- ===================================================== --}}

    <div class="md:col-span-2">

        <label class="mb-2 block text-sm font-medium text-slate-700">
            Working Calendar
            <span class="text-red-500">*</span>
        </label>

        <select id="calendar_id" name="calendar_id" class="w-full rounded-xl border border-slate-300 px-4 py-2.5">

            <option value="">
                Select Working Calendar
            </option>

            @foreach($calendars as $calendar)

            <option value="{{ $calendar->calendar_id }}" @selected(old('calendar_id', $policy->calendar_id ?? '') ==
                $calendar->calendar_id)>

                {{ $calendar->calendar_name }}

            </option>

            @endforeach

        </select>

        @error('calendar_id')

        <p class="mt-1 text-xs text-red-600">

            {{ $message }}

        </p>

        @enderror

    </div>

</div>


{{-- ========================================================= --}}
{{-- SLA Configuration --}}
{{-- ========================================================= --}}

<div class="mt-8 rounded-2xl border border-slate-200">

    <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">

        <h3 class="text-lg font-semibold text-slate-800">
            SLA Configuration
        </h3>

        <p class="mt-1 text-sm text-slate-500">
            Configure response and resolution timelines.
        </p>

    </div>

    <div class="grid grid-cols-1 gap-6 p-6 md:grid-cols-3">

        {{-- Response Time --}}

        <div>

            <label class="mb-2 block text-sm font-medium text-slate-700">
                Response Time (Minutes)
                <span class="text-red-500">*</span>
            </label>

            <input type="number" min="1" id="response_time_minutes" name="response_time_minutes"
                value="{{ old('response_time_minutes',$policy->response_time_minutes ?? '') }}" class="w-full rounded-xl border border-slate-300 px-4 py-2.5
                       focus:border-blue-500
                       focus:ring-2
                       focus:ring-blue-200">

            @error('response_time_minutes')
            <p class="mt-1 text-xs text-red-600">
                {{ $message }}
            </p>
            @enderror

        </div>

        {{-- Resolution Time --}}

        <div>

            <label class="mb-2 block text-sm font-medium text-slate-700">
                Resolution Time (Minutes)
                <span class="text-red-500">*</span>
            </label>

            <input type="number" min="1" id="resolution_time_minutes" name="resolution_time_minutes"
                value="{{ old('resolution_time_minutes',$policy->resolution_time_minutes ?? '') }}" class="w-full rounded-xl border border-slate-300 px-4 py-2.5
                       focus:border-blue-500
                       focus:ring-2
                       focus:ring-blue-200">

            @error('resolution_time_minutes')
            <p class="mt-1 text-xs text-red-600">
                {{ $message }}
            </p>
            @enderror

        </div>

        {{-- Warning Before --}}

        <div>

            <label class="mb-2 block text-sm font-medium text-slate-700">
                Warning Before (Minutes)
            </label>

            <input type="number" min="0" id="warning_before_minutes" name="warning_before_minutes"
                value="{{ old('warning_before_minutes',$policy->warning_before_minutes ?? 30) }}" class="w-full rounded-xl border border-slate-300 px-4 py-2.5
                       focus:border-blue-500
                       focus:ring-2
                       focus:ring-blue-200">

            <p class="mt-1 text-xs text-slate-500">
                Notification will be triggered before SLA breach.
            </p>

            @error('warning_before_minutes')
            <p class="mt-1 text-xs text-red-600">
                {{ $message }}
            </p>
            @enderror

        </div>

    </div>

</div>

{{-- ========================================================= --}}
{{-- Description --}}
{{-- ========================================================= --}}

<div class="mt-8 rounded-2xl border border-slate-200">

    <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">

        <h3 class="text-lg font-semibold text-slate-800">
            Additional Information
        </h3>

    </div>

    <div class="p-6">

        <label class="mb-2 block text-sm font-medium text-slate-700">
            Description
        </label>

        <textarea rows="5" id="description" name="description" class="w-full rounded-xl border border-slate-300 px-4 py-3
                   focus:border-blue-500
                   focus:ring-2
                   focus:ring-blue-200">{{ old('description',$policy->description ?? '') }}</textarea>

        @error('description')
        <p class="mt-1 text-xs text-red-600">
            {{ $message }}
        </p>
        @enderror

    </div>

</div>

{{-- ========================================================= --}}
{{-- SLA Preview --}}
{{-- ========================================================= --}}

<div class="mt-8 rounded-2xl border border-blue-200 bg-blue-50 p-5">

    <h4 class="font-semibold text-blue-900">

        SLA Summary

    </h4>

    <div class="mt-4 grid grid-cols-2 gap-4 md:grid-cols-4">

        <div>

            <div class="text-xs uppercase text-slate-500">

                Response

            </div>

            <div id="response_preview" class="mt-1 text-lg font-semibold text-slate-800">

                --

            </div>

        </div>

        <div>

            <div class="text-xs uppercase text-slate-500">

                Resolution

            </div>

            <div id="resolution_preview" class="mt-1 text-lg font-semibold text-slate-800">

                --

            </div>

        </div>

        <div>

            <div class="text-xs uppercase text-slate-500">

                Warning

            </div>

            <div id="warning_preview" class="mt-1 text-lg font-semibold text-slate-800">

                --

            </div>

        </div>

        <div>

            <div class="text-xs uppercase text-slate-500">

                Calendar

            </div>

            <div id="calendar_preview" class="mt-1 text-lg font-semibold text-slate-800">

                --

            </div>

        </div>

    </div>

</div>

{{-- ========================================================= --}}
{{-- Policy Options --}}
{{-- ========================================================= --}}

<div class="mt-8 rounded-2xl border border-slate-200">

    <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">

        <h3 class="text-lg font-semibold text-slate-800">
            Policy Options
        </h3>

        <p class="mt-1 text-sm text-slate-500">
            Configure escalation and activation settings.
        </p>

    </div>

    <div class="grid grid-cols-1 gap-6 p-6 md:grid-cols-2">

        {{-- Auto Escalation --}}

        <div>

            <label class="flex items-center gap-3">

                <input type="checkbox" id="auto_escalation" name="auto_escalation" value="1"
                    {{ old('auto_escalation',$policy->auto_escalation ?? 1) ? 'checked' : '' }}
                    class="h-5 w-5 rounded border-slate-300 text-blue-600">

                <div>

                    <div class="font-medium text-slate-700">

                        Enable Auto Escalation

                    </div>

                    <div class="text-sm text-slate-500">

                        Automatically escalate after SLA breach.

                    </div>

                </div>

            </label>

        </div>

        {{-- Active Status --}}

        <div>

            <label class="flex items-center gap-3">

                <input type="checkbox" id="is_active" name="is_active" value="1"
                    {{ old('is_active',$policy->is_active ?? 1) ? 'checked' : '' }}
                    class="h-5 w-5 rounded border-slate-300 text-green-600">

                <div>

                    <div class="font-medium text-slate-700">

                        Active Policy

                    </div>

                    <div class="text-sm text-slate-500">

                        Only active policies are used during Issue creation.

                    </div>

                </div>

            </label>

        </div>

    </div>

</div>

{{-- ========================================================= --}}
{{-- Enterprise Footer --}}
{{-- ========================================================= --}}

<div class="mt-8 border-t border-slate-200 pt-6">

    <div class="flex items-center justify-between">

        <div class="text-sm text-slate-500">

            <span class="font-medium text-red-500">*</span>

            Mandatory fields.

        </div>


        <div id="duplicate-warning" class="hidden rounded-xl border border-red-200 bg-red-50 p-4">

            <div class="font-medium text-red-700">

                Duplicate SLA Policy Found

            </div>

            <div class="mt-1 text-sm text-red-600">

                A policy already exists for this Project,
                Application, Service and Priority.

            </div>

        </div>


        <div class="flex gap-3">

            <a href="{{ route('sla-policies.index') }}" class="rounded-xl border border-slate-300 px-6 py-2.5
                      text-slate-700
                      hover:bg-slate-100">

                Cancel

            </a>

            <button type="submit" id="btnSavePolicy" class="rounded-xl bg-emerald-600 px-6 py-2.5
                       font-medium text-white
                       hover:bg-emerald-700">

                {{ isset($policy) ? 'Update SLA Policy' : 'Save SLA Policy' }}

            </button>

        </div>




    </div>

</div>

{{-- ========================================================= --}}
{{-- Hidden values --}}
{{-- ========================================================= --}}

@if(isset($policy))

<input type="hidden" name="sla_policy_id" value="{{ $policy->sla_policy_id }}">

@endif

{{-- ========================================================= --}}
{{-- Live Preview --}}
{{-- ========================================================= --}}

@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function() {

    const response = document.getElementById('response_time_minutes');
    const resolution = document.getElementById('resolution_time_minutes');
    const warning = document.getElementById('warning_before_minutes');
    const calendar = document.getElementById('calendar_id');

    function refreshPreview() {

        document.getElementById('response_preview').innerHTML =
            response.value ?
            response.value + ' Minutes' :
            '--';

        document.getElementById('resolution_preview').innerHTML =
            resolution.value ?
            resolution.value + ' Minutes' :
            '--';

        document.getElementById('warning_preview').innerHTML =
            warning.value ?
            warning.value + ' Minutes' :
            '--';

        document.getElementById('calendar_preview').innerHTML =
            calendar.options[calendar.selectedIndex] ?
            calendar.options[calendar.selectedIndex].text :
            '--';

    }

    response.addEventListener('keyup', refreshPreview);
    resolution.addEventListener('keyup', refreshPreview);
    warning.addEventListener('keyup', refreshPreview);
    calendar.addEventListener('change', refreshPreview);

    refreshPreview();

});
</script>



<script>
const project = document.getElementById('project_id');

project.addEventListener('change', function() {

    loadApplications(this.value);

    loadServices(this.value);

});

async function loadApplications(projectId) {

    const response = await fetch(
        '/admin/ajax/project/' + projectId + '/applications'
    );

    const data = await response.json();

    let html = '<option value="">Select Application</option>';

    data.forEach(item => {

        html += `
        <option value="${item.application_id}">
            ${item.application_name}
        </option>`;

    });

    document.getElementById('application_id').innerHTML = html;

}

async function loadServices(projectId) {

    const response = await fetch(
        '/admin/ajax/project/' + projectId + '/services'
    );

    const data = await response.json();

    let html = '<option value="">Select Service</option>';

    data.forEach(item => {

        html += `
        <option value="${item.service_id}">
            ${item.service_name}
        </option>`;

    });

    document.getElementById('service_id').innerHTML = html;

}

document.getElementById('application_id').addEventListener('change', function() {

    loadModules(this.value);

});

async function loadModules(applicationId) {

    const response = await fetch(

        '/admin/ajax/application/' + applicationId + '/modules'

    );

    const data = await response.json();

    let html = '<option>Select Module</option>';

    data.forEach(item => {

        html += `
        <option value="${item.module_id}">
            ${item.module_name}
        </option>`;

    });

    document.getElementById('module_id').innerHTML = html;

}
</script>

<script>
const fields = [

    'project_id',

    'application_id',

    'service_id',

    'priority_id'

];

fields.forEach(function(id) {

    document
        .getElementById(id)
        .addEventListener('change', checkDuplicate);

});

async function checkDuplicate() {

    const body = {

        project_id: document.getElementById('project_id').value,

        application_id: document.getElementById('application_id').value,

        service_id: document.getElementById('service_id').value,

        priority_id: document.getElementById('priority_id').value,

        sla_policy_id: document.querySelector(
            '[name=sla_policy_id]'
        )?.value ?? ''

    };

    if (
        !body.project_id ||
        !body.application_id ||
        !body.service_id ||
        !body.priority_id
    ) {
        return;
    }

    const response = await fetch(

        "{{ route('ajax.sla-policy.check') }}",

        {

            method: 'POST',

            headers: {

                'Content-Type': 'application/json',

                'X-CSRF-TOKEN': "{{ csrf_token() }}"

            },

            body: JSON.stringify(body)

        }

    );

    const result = await response.json();

    const warning =
        document.getElementById(
            'duplicate-warning'
        );

    const save =
        document.getElementById(
            'btnSavePolicy'
        );

    if (result.exists) {

        warning.classList.remove('hidden');

        save.disabled = true;

        save.classList.add(
            'opacity-50',
            'cursor-not-allowed'
        );

    } else {

        warning.classList.add('hidden');

        save.disabled = false;

        save.classList.remove(
            'opacity-50',
            'cursor-not-allowed'
        );

    }

}
</script>

@endpush