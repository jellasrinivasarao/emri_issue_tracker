@push('styles')
<style>
.select2-container--default .select2-selection--single {

    height: 44px;
    border-radius: 8px;
    border: 1px solid #d1d5db;

}

.select2-selection__rendered {

    line-height: 42px !important;

}

.select2-selection__arrow {

    height: 42px !important;

}
</style>
<!-- <link rel="stylesheet" href="{{ asset('css/issue.css') }}"> -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
@endpush

<div class="min-h-screen bg-slate-100 px-3 py-6">
    <div class="mx-auto max-w-5xl">
        <div class="mb-6 text-center">
            <h2 class="text-2xl font-bold text-slate-900">Raise New Issue</h2>
            <p class="mt-1 text-sm text-slate-500">Submit a new support issue</p>
        </div>

        <div id="issueSuccess" class="hidden mt-6">
        <div class="bg-green-50 border border-green-200 rounded-xl p-6">
            <div class="flex items-center gap-3">
                <div class="text-green-600 text-2xl">
                    ✓
                </div>

                <div>
                    <h3 class="font-semibold text-green-800">
                        Issue Raised Successfully
                    </h3>

                    <p class="text-sm text-green-700 mt-1">
                        Issue Number:
                        <strong id="successIssueNumber"></strong>
                    </p>
                </div>
            </div>
        </div>
    </div>

            <form id="issueForm" action="{{ route('issues.store') }}" method="POST" enctype="multipart/form-data">

                @csrf

                {{-- ========================================================= --}}
                {{-- SERVICE INFORMATION --}}
                {{-- ========================================================= --}}

                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">

                    <div class="bg-blue-50 border-b border-gray-200 px-6 py-4">

                        <div class="flex items-center gap-3">

                            <div
                                class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">

                                1

                            </div>

                            <h3 class="font-semibold text-blue-700 uppercase tracking-wide">

                                Service Information

                            </h3>

                        </div>

                    </div>

                    <div class="p-8">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- State --}}

                            <div>

                                <label class="block text-sm font-semibold mb-2">
                                    State
                                    <span class="text-red-500">*</span>
                                </label>



                                <select id="state_id" name="state_id"
                                    onchange="window.loadIssuePopupProjects && window.loadIssuePopupProjects(this.value)"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-600 focus:ring-blue-600">

                                    <option value="">Select State</option>


                                    @foreach($states as $state)

                                    <option value="{{ $state->state_id }}"
                                        {{ old('state_id')==$state->state_id ? 'selected':'' }}>

                                        {{ $state->state_name }}

                                    </option>

                                    @endforeach

                                </select>

                                @error('state_id')
                                <span class="text-red-500 text-xs">
                                    {{ $message }}
                                </span>
                                @enderror

                            </div>

                            {{-- Service removed per requirement: not required on create --}}

                            {{-- Project --}}

                            <div>

                                <label class="block text-sm font-semibold mb-2">

                                    Project

                                    <span class="text-red-500">*</span>

                                </label>

                                <select id="project_id" name="project_id"
                                    onchange="window.loadIssuePopupApplications && window.loadIssuePopupApplications(this.value)"
                                    class="w-full rounded-lg border-gray-300">

                                    @foreach($projects as $project)
                                    <option value="{{ $project->project_id }}"
                                        {{ old('project_id') == $project->project_id ? 'selected' : '' }}>
                                        {{ $project->project_name }}
                                    </option>
                                    @endforeach

                                </select>

                            </div>

                            {{-- Application --}}

                            <div>

                                <label class="block text-sm font-semibold mb-2">

                                    Application

                                    <span class="text-red-500">*</span>

                                </label>

                                <select id="application_id" name="application_id"
                                    onchange="window.loadIssuePopupModules && window.loadIssuePopupModules(this.value, document.getElementById('project_id')?.value || '')"
                                    class="w-full rounded-lg border-gray-300">

                                    @foreach($applications as $application)
                                    <option value="{{ $application->application_id }}"
                                        {{ old('application_id') == $application->application_id ? 'selected' : '' }}>
                                        {{ $application->application_name }}
                                    </option>
                                    @endforeach

                                </select>

                            </div>

                            {{-- Module --}}

                            <div>

                                <label class="block text-sm font-semibold mb-2">

                                    Module

                                </label>

                                <select id="module_id" name="module_id" class="w-full rounded-lg border-gray-300">

                                    @foreach($modules as $module)
                                    <option value="{{ $module->module_id }}"
                                        {{ old('module_id') == $module->module_id ? 'selected' : '' }}>
                                        {{ $module->module_name }}
                                    </option>
                                    @endforeach

                                </select>

                            </div>

                        </div>

                    </div>

                </div>

                {{-- ========================================================= --}}
                {{-- ISSUE INFORMATION --}}
                {{-- ========================================================= --}}

                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mt-8">

                    <div class="bg-blue-50 border-b border-gray-200 px-6 py-4">

                        <div class="flex items-center gap-3">

                            <div
                                class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">
                                2
                            </div>

                            <h3 class="font-semibold text-blue-700 uppercase tracking-wide">
                                Issue Information
                            </h3>

                        </div>

                    </div>

                    <div class="p-8">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- Issue Category --}}

                            <div>

                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Issue Category
                                    <span class="text-red-500">*</span>
                                </label>

                                <select name="issue_category_id" id="issue_category_id"
                                    class="w-full rounded-lg border-gray-300 focus:ring-blue-600 focus:border-blue-600">

                                    <option value="">
                                        Select Issue Category
                                    </option>

                                    @foreach($issueCategories as $category)

                                    <option value="{{ $category->issue_category_id }}"
                                        {{ old('issue_category_id')==$category->issue_category_id ? 'selected':'' }}>

                                        {{ $category->category_name }}

                                    </option>

                                    @endforeach

                                </select>

                                @error('issue_category_id')
                                <p class="text-red-500 text-xs mt-1">
                                    {{ $message }}
                                </p>
                                @enderror

                            </div>

                            {{-- Priority --}}

                            <div>

                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Priority
                                    <span class="text-red-500">*</span>
                                </label>

                                <select name="priority_id" id="priority_id"
                                    class="w-full rounded-lg border-gray-300 focus:ring-blue-600 focus:border-blue-600">

                                    <option value="">
                                        Select Priority
                                    </option>

                                    @foreach($priorities as $priority)

                                    <option value="{{ $priority->priority_id }}"
                                        {{ old('priority_id')==$priority->priority_id ? 'selected':'' }}>

                                        {{ $priority->priority_name }}

                                    </option>

                                    @endforeach

                                </select>

                                @error('priority_id')
                                <p class="text-red-500 text-xs mt-1">
                                    {{ $message }}
                                </p>
                                @enderror

                            </div>

                        </div>

                        {{-- Subject --}}

                        <div class="mt-6">

                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Subject
                                <span class="text-red-500">*</span>
                            </label>

                            <input type="text" id="subject" name="subject" maxlength="255" value="{{ old('subject') }}"
                                placeholder="Enter issue subject"
                                class="w-full rounded-lg border-gray-300 focus:ring-blue-600 focus:border-blue-600">

                            @error('subject')
                            <p class="text-red-500 text-xs mt-1">
                                {{ $message }}
                            </p>
                            @enderror

                            <div class="flex justify-between mt-1">

                                <small class="text-gray-400">
                                    Maximum 255 characters
                                </small>

                                <small class="text-gray-500">
                                    <span id="subjectCount">0</span>/255
                                </small>

                            </div>

                        </div>

                        {{-- Description --}}

                        <div class="mt-6">

                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Description
                                <span class="text-red-500">*</span>
                            </label>

                            <textarea id="description" name="description" rows="6" maxlength="5000"
                                placeholder="Describe the issue, operational impact and observations..."
                                class="w-full rounded-lg border-gray-300 focus:ring-blue-600 focus:border-blue-600 resize-none">{{ old('description') }}</textarea>

                            @error('description')
                            <p class="text-red-500 text-xs mt-1">
                                {{ $message }}
                            </p>
                            @enderror

                            <div class="flex justify-between mt-1">

                                <small class="text-gray-400">
                                    Provide detailed information to help support resolve the issue faster.
                                </small>

                                <small class="text-gray-500">
                                    <span id="descriptionCount">0</span>/5000
                                </small>

                            </div>

                        </div>

                    </div>

                </div>




                {{-- ========================================================= --}}
                {{-- SUPPORTING INFORMATION --}}
                {{-- ========================================================= --}}

                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mt-8">

                    <div class="bg-blue-50 border-b border-gray-200 px-6 py-4">

                        <div class="flex items-center gap-3">

                            <div
                                class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold">
                                3
                            </div>

                            <h3 class="font-semibold text-blue-700 uppercase tracking-wide">
                                Supporting Information
                            </h3>

                        </div>

                    </div>

                    <div class="p-8">

                        {{-- Occurred On --}}

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <div>

                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Occurred On
                                    <span class="text-red-500">*</span>
                                </label>

                                <input type="date" name="occurred_date" id="occurred_date"
                                    value="{{ old('occurred_date',date('Y-m-d')) }}"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-600 focus:ring-blue-600">

                                @error('occurred_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror

                            </div>

                            <div>

                                <label class="block text-sm font-semibold text-gray-700 mb-2">
                                    Time
                                    <span class="text-red-500">*</span>
                                </label>

                                <input type="time" name="occurred_time" id="occurred_time"
                                    value="{{ old('occurred_time',date('H:i')) }}"
                                    class="w-full rounded-lg border-gray-300 focus:border-blue-600 focus:ring-blue-600">

                                @error('occurred_time')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror

                            </div>

                        </div>

                        {{-- Affected Users --}}

                        <div class="mt-6">

                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Affected Users
                            </label>

                            <input type="text" name="affected_users" id="affected_users"
                                value="{{ old('affected_users') }}" placeholder="Enter usernames or user groups"
                                class="w-full rounded-lg border-gray-300 focus:border-blue-600 focus:ring-blue-600">

                            @error('affected_users')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror

                        </div>

                        {{-- Attachment --}}

                        <div class="mt-6">

                            <label class="block text-sm font-semibold text-gray-700 mb-2">
                                Attachment
                            </label>

                            <div
                                class="border-2 border-dashed border-gray-300 rounded-xl p-6 hover:border-blue-500 transition">

                                <div class="flex flex-col items-center justify-center">

                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400 mb-3"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">

                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 16V8a5 5 0 0110 0v8a3 3 0 11-6 0V9" />

                                    </svg>

                                    <p class="text-gray-600 font-medium">
                                        Drag & Drop your file here
                                    </p>

                                    <p class="text-sm text-gray-400 mt-1">
                                        or click below
                                    </p>

                                    <input type="file" name="attachment" id="attachment" class="hidden">

                                    <button type="button" onclick="document.getElementById('attachment').click()"
                                        class="mt-4 px-5 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">

                                        Choose File

                                    </button>

                                    <div id="selectedFile" class="text-sm text-green-600 mt-3">
                                    </div>

                                </div>

                            </div>

                            <small class="text-gray-500">
                                Allowed: JPG, JPEG, PNG, PDF, DOC, DOCX, XLS, XLSX (Max 10 MB)
                            </small>

                            @error('attachment')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror

                        </div>

                    </div>

                    {{-- Footer Buttons --}}

                    <div class="bg-gray-50 border-t px-8 py-5">

                        <div class="flex justify-between">

                            <a href="{{ route('issues.index') }}"
                                class="px-6 py-3 rounded-lg border border-gray-300 bg-white hover:bg-gray-100 font-medium">

                                Cancel

                            </a>



                            <button id="submitBtn" type="submit"
                                class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700">

                                Submit Issue

                            </button>

                        </div>

                    </div>

                </div>

                {{-- Character Counter --}}

                @push('scripts')


                <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

                <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/js/select2.min.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

                <!-- <script src="{{ asset('js/issue.js') }}"></script> -->

                <script>
                window.initIssueCreatePopup = function initIssueCreatePopup() {
                    const attachmentInput = document.getElementById('attachment');
                    if (attachmentInput && !attachmentInput.dataset.bound) {
                        attachmentInput.dataset.bound = '1';
                        attachmentInput.addEventListener('change', function() {
                            const file = this.files[0];
                            const selectedFile = document.getElementById('selectedFile');
                            if (file && selectedFile) {
                                selectedFile.innerHTML = 'Selected : ' + file.name;
                            }
                        });
                    }

                    let subject = document.getElementById('subject');
                    let description = document.getElementById('description');
                    if (subject && !subject.dataset.bound) {
                        subject.dataset.bound = '1';
                        const subjectCount = document.getElementById('subjectCount');
                        const updateCounter = (input, counter) => {
                            if (counter) counter.innerHTML = input.value.length;
                        };
                        updateCounter(subject, subjectCount);
                        subject.addEventListener('keyup', () => updateCounter(subject, subjectCount));
                    }

                    if (description && !description.dataset.bound) {
                        description.dataset.bound = '1';
                        const descriptionCount = document.getElementById('descriptionCount');
                        const updateCounter = (input, counter) => {
                            if (counter) counter.innerHTML = input.value.length;
                        };
                        updateCounter(description, descriptionCount);
                        description.addEventListener('keyup', () => updateCounter(description, descriptionCount));
                    }

                    const issueForm = document.getElementById('issueForm');
                    if (issueForm && !issueForm.dataset.handlerAttached) {
                        issueForm.dataset.handlerAttached = '1';

                        issueForm.addEventListener('submit', function(e) {
                            e.preventDefault();
                            if (issueForm.dataset.submitting === '1') return;

                            issueForm.dataset.submitting = '1';
                            const submitBtn = document.getElementById('submitBtn');
                            const originalText = submitBtn ? submitBtn.innerHTML : '';

                            if (submitBtn) {
                                submitBtn.disabled = true;
                                submitBtn.innerHTML = 'Submitting...';
                            }

                            const formData = new FormData(issueForm);
                            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

                            fetch(issueForm.action, {
                                    method: 'POST',
                                    body: formData,
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'Accept': 'application/json',
                                        ...(csrfToken ? { 'X-CSRF-TOKEN': csrfToken } : {})
                                    }
                                })
                                .then(response => response.json().then(data => ({ status: response.status, data })))
                                .then(({ status, data }) => {
                                    document.querySelectorAll('.validation-error').forEach(e => e.remove());

                                    if (status === 422) {
                                        Object.keys(data.errors || {}).forEach(function(field) {
                                            const input = document.querySelector(`[name="${field}"]`);
                                            if (input) {
                                                const error = document.createElement('div');
                                                error.className = 'validation-error text-red-500 text-sm mt-1';
                                                error.innerHTML = (data.errors[field] || [])[0];
                                                input.parentNode.appendChild(error);
                                            }
                                        });
                                        return;
                                    }

                                    if (data && data.success) {
                                        const message = data.message || 'Issue created successfully.';
                                        const issue = data.issue || {};

                                        if (typeof Swal !== 'undefined') {
                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Issue Raised Successfully',
                                                html: `<b>${message}</b><br><br>${data.error || ''} <b>Issue Number: ${issue.issue_number || '-'}</b><br><br>${message}${issue.issue_title ? `<br><br><b>Subject:</b> ${issue.issue_title}` : ''}`,
                                                allowOutsideClick: false,
                                                allowEscapeKey: false,
                                                confirmButtonText: 'OK'
                                            }).then(function() {
                                                window.location.href = window.location.href;
                                            });
                                        } else {
                                            alert(message + '\nIssue Number: ' + (issue.issue_number || '-'));
                                            window.location.href = window.location.href;
                                        }

                                        issueForm.reset();
                                        const successIssueNumber = document.getElementById('successIssueNumber');
                                        if (successIssueNumber) successIssueNumber.textContent = issue.issue_number || '-';
                                        const issueSuccess = document.getElementById('issueSuccess');
                                        if (issueSuccess) issueSuccess.classList.remove('hidden');

                                        $('#state_id').val('').trigger('change');
                                        $('#project_id').val('').trigger('change');
                                        $('#application_id').val('').trigger('change');
                                        $('#module_id').val('').trigger('change');
                                        $('#issue_category_id').val('').trigger('change');
                                        $('#priority_id').val('').trigger('change');

                                        const selectedFile = document.getElementById('selectedFile');
                                        if (selectedFile) selectedFile.innerHTML = '';
                                        return;
                                    }

                                    const message = (data && data.message) ? data.message : 'Unable to raise issue.';
                                    const selectedFile = document.getElementById('selectedFile');
                                    if (selectedFile) selectedFile.innerHTML = '<span class="text-red-600">' + message + '</span>';
                                })
                                .catch((err) => {
                                    console.error('Issue create failed', err);
                                    const selectedFile = document.getElementById('selectedFile');
                                    if (selectedFile) selectedFile.innerHTML = '<span class="text-red-600">Unable to raise issue.</span>';
                                })
                                .finally(() => {
                                    if (submitBtn) {
                                        submitBtn.disabled = false;
                                        submitBtn.innerHTML = originalText;
                                    }
                                    issueForm.dataset.submitting = '0';
                                });
                        });
                    }

                    const stateSelect = document.getElementById('state_id');
                    const projectSelect = document.getElementById('project_id');
                    const applicationSelect = document.getElementById('application_id');
                    const moduleSelect = document.getElementById('module_id');

                    function clearSelect(select, placeholder) {
                        if (!select) return;
                        select.innerHTML = '';
                        const option = document.createElement('option');
                        option.value = '';
                        option.textContent = placeholder || 'Select';
                        select.appendChild(option);
                    }

                    if (stateSelect && !stateSelect.dataset.handlerAttached) {
                        stateSelect.dataset.handlerAttached = '1';
                        stateSelect.addEventListener('change', function() {
                            const stateId = this.value;
                            clearSelect(projectSelect, 'Select Project');
                            clearSelect(applicationSelect, 'Select Application');
                            clearSelect(moduleSelect, 'Select Module');
                            if (!stateId) return;

                            fetch('{{ route("issues.ajax.projects") }}?state_id=' + encodeURIComponent(stateId), {
                                headers: { 'X-Requested-With': 'XMLHttpRequest' }
                            }).then(r => r.json()).then(data => {
                                const seen = new Set();
                                data.forEach(function(p) {
                                    if (!p || !p.project_id || seen.has(String(p.project_id))) return;
                                    seen.add(String(p.project_id));
                                    const option = document.createElement('option');
                                    option.value = p.project_id;
                                    option.textContent = p.project_name;
                                    projectSelect.appendChild(option);
                                });

                                if (projectSelect.options.length > 1) {
                                    projectSelect.value = String(projectSelect.options[1].value);
                                    projectSelect.dispatchEvent(new Event('change'));
                                }
                            });
                        });
                    }

                    if (projectSelect && !projectSelect.dataset.handlerAttached) {
                        projectSelect.dataset.handlerAttached = '1';
                        projectSelect.addEventListener('change', function() {
                            const projectId = this.value;
                            clearSelect(applicationSelect, 'Select Application');
                            clearSelect(moduleSelect, 'Select Module');
                            if (!projectId) return;

                            fetch('{{ route("issues.ajax.applications") }}?project_id=' + encodeURIComponent(projectId), {
                                headers: { 'X-Requested-With': 'XMLHttpRequest' }
                            }).then(r => r.json()).then(data => {
                                const seen = new Set();
                                data.forEach(function(a) {
                                    if (!a || !a.application_id || seen.has(String(a.application_id))) return;
                                    seen.add(String(a.application_id));
                                    const option = document.createElement('option');
                                    option.value = a.application_id;
                                    option.textContent = a.application_name;
                                    applicationSelect.appendChild(option);
                                });

                                if (applicationSelect.options.length > 1) {
                                    applicationSelect.value = String(applicationSelect.options[1].value);
                                    applicationSelect.dispatchEvent(new Event('change'));
                                }
                            });
                        });
                    }

                    if (applicationSelect && !applicationSelect.dataset.handlerAttached) {
                        applicationSelect.dataset.handlerAttached = '1';
                        applicationSelect.addEventListener('change', function() {
                            const appId = this.value;
                            const projectId = projectSelect ? projectSelect.value : '';
                            clearSelect(moduleSelect, 'Select Module');
                            if (!appId) return;

                            const params = new URLSearchParams({ application_id: appId });
                            if (projectId) params.set('project_id', projectId);

                            fetch('{{ route("issues.ajax.modules") }}?' + params.toString(), {
                                headers: { 'X-Requested-With': 'XMLHttpRequest' }
                            }).then(r => r.json()).then(data => {
                                const seen = new Set();
                                data.forEach(function(m) {
                                    if (!m || !m.module_id || seen.has(String(m.module_id))) return;
                                    seen.add(String(m.module_id));
                                    const option = document.createElement('option');
                                    option.value = m.module_id;
                                    option.textContent = m.module_name;
                                    moduleSelect.appendChild(option);
                                });
                            });
                        });
                    }

                    if (stateSelect && stateSelect.value) {
                        stateSelect.dispatchEvent(new Event('change'));
                    }
                };

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', window.initIssueCreatePopup);
                } else {
                    window.initIssueCreatePopup();
                }
                </script>
                @endpush

            </form>
        </div>
    </div>
</div>