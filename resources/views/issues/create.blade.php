<x-app-layout>

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
    <link rel="stylesheet" href="{{ asset('css/issue.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0/dist/css/select2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @endpush

    <!-- <div class="custom-box">
        TODO
    </div> -->

    <x-slot name="header">
        <div class="flex items-center justify-between">

            <div>
                <h2 class="text-2xl font-bold text-gray-800">
                    Raise New Issue
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Submit a new support issue
                </p>
            </div>

            <a href="{{ route('issues.index') }}"
                class="rounded-lg border border-gray-300 bg-white px-5 py-2 text-sm font-medium hover:bg-gray-50">
                Back
            </a>

        </div>
    </x-slot>


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

    <div class="py-8 bg-gray-100 min-h-screen">

        <div class="max-w-7xl mx-auto px-6">

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

                                <select id="project_id" name="project_id" class="w-full rounded-lg border-gray-300">

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

                                    <div class="flex flex-col sm:flex-row items-center gap-3 mt-4">
                                        <button type="button" onclick="document.getElementById('attachment').click()"
                                            class="px-5 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">
                                            Choose File
                                        </button>

                                        <button type="button" id="previewTicketBtn" onclick="if (typeof openTicketPreviewModal === 'function') { openTicketPreviewModal(); } else { const modal = document.getElementById('ticketPreviewModal'); if (modal) modal.classList.remove('hidden'); }"
                                            class="px-5 py-2 rounded-lg border border-blue-600 bg-blue-50 text-blue-700 hover:bg-blue-100">
                                            Preview Ticket
                                        </button>
                                    </div>

                                    <div id="selectedFile" class="mt-3 hidden"></div>
                                    <div id="selectedFileActions" class="mt-3 hidden flex items-center gap-3">
                                        <button type="button" id="selectedFilePreviewBtn" onclick="if (typeof openTicketPreviewModal === 'function') { openTicketPreviewModal(); } else { const modal = document.getElementById('ticketPreviewModal'); if (modal) modal.classList.remove('hidden'); }"
                                            class="px-4 py-2 rounded-lg border border-blue-600 bg-blue-50 text-sm font-medium text-blue-700 hover:bg-blue-100">
                                            Preview
                                        </button>
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

                            <a href="{{ route('raise.issue') }}"
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
                function readFormValue(selector, fallback = '-') {
                    const el = document.querySelector(selector);
                    if (!el) return fallback;
                    const value = (el.value || '').trim();
                    return value || fallback;
                }

                function getAttachmentName() {
                    const attachment = document.getElementById('attachment');
                    if (attachment && attachment.files && attachment.files.length) {
                        return attachment.files[0].name;
                    }
                    return 'No attachment selected';
                }

                function getAttachmentImageMarkup() {
                    const attachment = document.getElementById('attachment');
                    if (!attachment || !attachment.files || !attachment.files.length) return '';
                    const file = attachment.files[0];
                    if (!file.type || !file.type.startsWith('image/')) return '';
                    const previewUrl = URL.createObjectURL(file);
                    return `<img src="${previewUrl}" alt="${file.name}" class="max-h-64 rounded-xl border border-gray-200 object-contain bg-gray-50 p-2" />`;
                }

                function buildTicketPreviewHtml() {
                    const state = readFormValue('#state_id');
                    const project = readFormValue('#project_id');
                    const application = readFormValue('#application_id');
                    const module = readFormValue('#module_id');
                    const issueCategory = readFormValue('#issue_category_id');
                    const priority = readFormValue('#priority_id');
                    const subject = readFormValue('#subject');
                    const description = readFormValue('#description');
                    const occurredDate = readFormValue('#occurred_date');
                    const occurredTime = readFormValue('#occurred_time');
                    const affectedUsers = readFormValue('#affected_users');
                    const attachmentName = getAttachmentName();
                    const imageMarkup = getAttachmentImageMarkup();

                    return `
                        <div class="space-y-4 bg-white">
                            <!-- TICKET INFORMATION Header -->
                            <div class="bg-blue-100 border-l-4 border-blue-600 px-4 py-3 mb-4">
                                <p class="text-sm font-semibold text-blue-700 flex items-center gap-2">
                                    <span>📋</span> TICKET INFORMATION
                                </p>
                            </div>

                            <!-- Row 1: Ticket ID, State, Priority -->
                            <div class="grid grid-cols-3 gap-4">
                                <div class="border border-gray-300 rounded-lg p-4 bg-white">
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Ticket ID</p>
                                    <p class="mt-2 font-bold text-gray-900">DRAFT-PREVIEW</p>
                                </div>
                                <div class="border border-gray-300 rounded-lg p-4 bg-white">
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">State</p>
                                    <p class="mt-2 font-bold text-gray-900">${state}</p>
                                </div>
                                <div class="border border-gray-300 rounded-lg p-4 bg-white">
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Priority</p>
                                    <p class="mt-2 font-bold text-gray-900">${priority}</p>
                                </div>
                            </div>

                            <!-- Row 2: Project, Application, Module -->
                            <div class="grid grid-cols-3 gap-4">
                                <div class="border border-gray-300 rounded-lg p-4 bg-white">
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Project</p>
                                    <p class="mt-2 font-bold text-gray-900">${project}</p>
                                </div>
                                <div class="border border-gray-300 rounded-lg p-4 bg-white">
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Application</p>
                                    <p class="mt-2 font-bold text-gray-900">${application}</p>
                                </div>
                                <div class="border border-gray-300 rounded-lg p-4 bg-white">
                                    <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Module</p>
                                    <p class="mt-2 font-bold text-gray-900">${module}</p>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="border border-gray-300 rounded-lg p-4 bg-white">
                                <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Status</p>
                                <p class="mt-2 font-bold text-gray-900">${state}</p>
                            </div>

                            <!-- Description -->
                            <div class="border border-gray-300 rounded-lg p-4 bg-white">
                                <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Description</p>
                                <p class="mt-2 text-gray-700 whitespace-pre-wrap">${description}</p>
                            </div>

                            <!-- Attachments -->
                            <div class="border border-gray-300 rounded-lg p-4 bg-white">
                                <p class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-3">Attachments</p>
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between py-2">
                                        <p class="text-sm text-gray-700">${attachmentName}</p>
                                        <div class="flex gap-3">
                                            <a href="#" class="text-sm font-semibold text-blue-600 hover:underline">View</a>
                                            <a href="#" class="text-sm font-semibold text-blue-600 hover:underline">Download</a>
                                        </div>
                                    </div>
                                    ${imageMarkup ? '<div class="mt-3 border-t pt-3">' + imageMarkup + '</div>' : ''}
                                </div>
                            </div>

                            <!-- Update History Section -->
                            <div class="mt-6">
                                <div class="bg-blue-100 border-l-4 border-blue-600 px-4 py-3 mb-4">
                                    <p class="text-sm font-semibold text-blue-700 flex items-center gap-2">
                                        <span>⏱</span> UPDATE HISTORY
                                    </p>
                                </div>
                                
                                <!-- History Entries -->
                                <div class="space-y-3">
                                    <!-- Entry 1: Issue Created -->
                                    <div class="border border-gray-300 rounded-lg p-4 bg-gray-50">
                                        <div class="mb-3">
                                            <p class="font-bold text-blue-600">Issue Created</p>
                                            <p class="text-xs text-gray-600">by System · Just now</p>
                                        </div>
                                        <p class="text-sm text-gray-700">Action: <span class="font-semibold">Issue Created</span></p>
                                        <p class="text-sm font-semibold text-gray-900 mt-2">Remarks: Issue created successfully.</p>
                                    </div>

                                    <!-- Entry 2: Status Change - Example -->
                                    <div class="border border-gray-300 rounded-lg p-4 bg-gray-50">
                                        <div class="mb-3">
                                            <p class="font-bold text-blue-600">Status Changed</p>
                                            <p class="text-xs text-gray-600">by Support Team · 15 minutes ago</p>
                                        </div>
                                        <div class="text-sm text-gray-700 space-y-1">
                                            <p>Status: <span class="font-semibold">Assigned</span> → <span class="font-semibold">In Progress</span></p>
                                        </div>
                                        <p class="text-sm font-semibold text-gray-900 mt-2">Remarks: Issue under investigation</p>
                                    </div>

                                    <!-- Entry 3: Attachment Addition - Example -->
                                    <div class="border border-gray-300 rounded-lg p-4 bg-gray-50">
                                        <div class="mb-3">
                                            <p class="font-bold text-blue-600">Information Updated</p>
                                            <p class="text-xs text-gray-600">by Support Team · 10 minutes ago</p>
                                        </div>
                                        <p class="text-sm text-gray-700">Action: <span class="font-semibold">Attachment Added / Details Updated</span></p>
                                        <p class="text-sm font-semibold text-gray-900 mt-2">Remarks: Supporting document attached for reference</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }

                function renderAttachmentPreview(file) {
                    const selectedFile = document.getElementById('selectedFile');
                    const selectedFileActions = document.getElementById('selectedFileActions');
                    if (!selectedFile) return;

                    if (!file) {
                        selectedFile.classList.add('hidden');
                        selectedFile.innerHTML = '';
                        if (selectedFileActions) selectedFileActions.classList.add('hidden');
                        return;
                    }

                    selectedFile.classList.remove('hidden');
                    if (selectedFileActions) selectedFileActions.classList.remove('hidden');

                    if (file.type && file.type.startsWith('image/')) {
                        const previewUrl = URL.createObjectURL(file);
                        selectedFile.innerHTML = `
                            <div class="rounded-xl border border-gray-200 bg-gray-50 p-3">
                                <img src="${previewUrl}" alt="${file.name}" class="max-h-48 rounded-lg object-contain mx-auto" />
                                <p class="mt-2 text-sm text-gray-700 text-center">${file.name}</p>
                            </div>
                        `;
                        return;
                    }

                    selectedFile.innerHTML = `
                        <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 p-4 text-center">
                            <p class="text-sm text-gray-500">No preview available for this file type.</p>
                            <p class="mt-2 text-sm font-medium text-gray-700">${file.name}</p>
                        </div>
                    `;
                }

                function openTicketPreviewModal() {
                    const previewContent = document.getElementById('ticketPreviewContent');
                    if (!previewContent) return;
                    previewContent.innerHTML = buildTicketPreviewHtml();
                    const modal = document.getElementById('ticketPreviewModal');
                    if (modal) modal.classList.remove('hidden');
                }

                window.openTicketPreviewModal = openTicketPreviewModal;

                function closeTicketPreviewModal() {
                    const modal = document.getElementById('ticketPreviewModal');
                    if (modal) modal.classList.add('hidden');
                }

                window.closeTicketPreviewModal = closeTicketPreviewModal;

                function printTicketPreview() {
                    const previewHtml = buildTicketPreviewHtml();
                    const printWindow = window.open('', '_blank', 'width=1000,height=800');

                    if (!printWindow) {
                        alert('Your browser blocked the print popup. Please allow popups and try again.');
                        return;
                    }

                    printWindow.document.write(`<!DOCTYPE html>
                        <html>
                        <head>
                            <title>Issue Ticket Preview</title>
                            <style>
                                body { font-family: Arial, sans-serif; margin: 24px; color: #111827; }
                                .section { margin-bottom: 18px; }
                                .grid { display: grid; grid-template-columns: repeat(2, minmax(180px, 1fr)); gap: 12px; }
                                .card { border: 1px solid #e5e7eb; border-radius: 12px; padding: 12px; background: #f9fafb; }
                                .label { font-size: 11px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.08em; }
                                .value { margin-top: 6px; font-weight: 600; }
                                .title { font-size: 28px; font-weight: 700; margin-bottom: 14px; }
                                img { max-height: 220px; border: 1px solid #e5e7eb; border-radius: 10px; padding: 8px; background: #fff; }
                                @media print { body { margin: 0; } }
                            </style>
                        </head>
                        <body>${previewHtml}</body>
                        </html>`);
                    printWindow.document.close();
                    printWindow.focus();
                    setTimeout(() => printWindow.print(), 400);
                }

                const attachmentInput = document.getElementById('attachment');
                if (attachmentInput) {
                    attachmentInput.addEventListener('change', function() {
                        const file = this.files && this.files[0] ? this.files[0] : null;
                        renderAttachmentPreview(file);
                    });
                }

                const previewTicketBtn = document.getElementById('previewTicketBtn');
                if (previewTicketBtn) {
                    previewTicketBtn.addEventListener('click', openTicketPreviewModal);
                }

                const closePreviewBtn = document.getElementById('closePreviewBtn');
                if (closePreviewBtn) {
                    closePreviewBtn.addEventListener('click', closeTicketPreviewModal);
                }

                const printTicketBtn = document.getElementById('printTicketBtn');
                if (printTicketBtn) {
                    printTicketBtn.addEventListener('click', printTicketPreview);
                }

                const exportPdfBtn = document.getElementById('exportPdfBtn');
                if (exportPdfBtn) {
                    exportPdfBtn.addEventListener('click', printTicketPreview);
                }

                const ticketPreviewModal = document.getElementById('ticketPreviewModal');
                if (ticketPreviewModal) {
                    ticketPreviewModal.addEventListener('click', function(event) {
                        if (event.target === ticketPreviewModal) {
                            closeTicketPreviewModal();
                        }
                    });
                }

                const selectedFilePreviewBtn = document.getElementById('selectedFilePreviewBtn');
                if (selectedFilePreviewBtn) {
                    selectedFilePreviewBtn.addEventListener('click', openTicketPreviewModal);
                }

                document.addEventListener('DOMContentLoaded', function() {
                    let subject = document.getElementById('subject');
                    let description = document.getElementById('description');

                    let subjectCount = document.getElementById('subjectCount');
                    let descriptionCount = document.getElementById('descriptionCount');

                        let isSubmitting = false;

                        function updateCounter(input, counter) {
                            counter.innerHTML = input.value.length;
                        }

                        updateCounter(subject, subjectCount);
                        updateCounter(description, descriptionCount);

                        subject.addEventListener('keyup', function() {
                            updateCounter(subject, subjectCount);
                        });

                        description.addEventListener('keyup', function() {
                            updateCounter(description, descriptionCount);
                        });

                        const issueForm = document.getElementById('issueForm');
                        if (issueForm && !issueForm.dataset.handlerAttached) {
                            issueForm.dataset.handlerAttached = '1';

                            issueForm.addEventListener('submit', function(e) {
                                e.preventDefault();

                                // dataset-based guard is robust across multiple handlers
                                if (issueForm.dataset.submitting === '1') {
                                    return;
                                }

                                issueForm.dataset.submitting = '1';
                                const submitBtn = document.getElementById('submitBtn');
                                const originalText = submitBtn.innerHTML;

                                submitBtn.disabled = true;
                                submitBtn.innerHTML = 'Submitting...';

                                const formData = new FormData(issueForm);

                                console.log("===== FormData =====");
                                for (let [key, value] of formData.entries()) {
                                    if (value instanceof File) {
                                        console.log(key, value.name, value.size);
                                    } else {
                                        console.log(key, value);
                                    }
                                }

                                fetch(issueForm.action, {
                                        method: 'POST',
                                        body: formData,
                                        headers: {
                                            'X-Requested-With': 'XMLHttpRequest',
                                            'Accept': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                        }
                                    })
                                    .then(response => response.json().then(data => ({ status: response.status, data })))
                                    .then(({ status, data }) => {

                                        document.querySelectorAll('.validation-error').forEach(e => e.remove());

                                        if (status === 422) {
                                            Object.keys(data.errors).forEach(function(field) {
                                                const input = document.querySelector(`[name="${field}"]`);
                                                if (input) {
                                                    const error = document.createElement('div');
                                                    error.className = 'validation-error text-red-500 text-sm mt-1';
                                                    error.innerHTML = data.errors[field][0];
                                                    input.parentNode.appendChild(error);
                                                }
                                            });
                                            return;
                                        }

                                        // Treat success only when server explicitly signals it
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
                                                    // Redirect to fresh create page after user clicks OK
                                                    window.location.href = window.location.href;
                                                });
                                            } else {
                                                alert(message + "\nIssue Number: " + (issue.issue_number || '-'));
                                                // fallback redirect
                                                window.location.href = window.location.href;
                                            }

                                            issueForm.reset();

                                            document.getElementById('successIssueNumber').textContent = issue.issue_number || '-';
                                            document.getElementById('issueSuccess').classList.remove('hidden');

                                            $('#state_id').val('').trigger('change');
                                            $('#project_id').val('').trigger('change');
                                            $('#application_id').val('').trigger('change');
                                            $('#module_id').val('').trigger('change');
                                            $('#issue_category_id').val('').trigger('change');
                                            $('#priority_id').val('').trigger('change');

                                            renderAttachmentPreview(null);
                                            return;
                                        }

                                        const message = (data && data.message) ? data.message : 'Unable to raise issue.';
                                        const selectedFile = document.getElementById('selectedFile');
                                        if (selectedFile) {
                                            selectedFile.classList.remove('hidden');
                                            selectedFile.innerHTML = '<span class="text-red-600">' + message + '</span>';
                                        }
                                    })
                                    .catch((err) => {
                                        console.error('Issue create failed', err);
                                        const selectedFile = document.getElementById('selectedFile');
                                        if (selectedFile) {
                                            selectedFile.classList.remove('hidden');
                                            selectedFile.innerHTML = '<span class="text-red-600">Unable to raise issue.</span>';
                                        }
                                    })
                                    .finally(() => {
                                        submitBtn.disabled = false;
                                        submitBtn.innerHTML = originalText;
                                        issueForm.dataset.submitting = '0';
                                    });
                            });
                        }
                });
                </script>



                @endpush

                <div id="ticketPreviewModal" class="fixed inset-0 z-50 hidden bg-black/40 flex items-center justify-center">
                    <div class="w-full max-w-4xl rounded-2xl bg-white shadow-2xl flex flex-col" style="max-height: 90vh;">
                        <div class="border-b border-gray-200 bg-white px-6 py-4 flex-shrink-0">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-2xl font-bold text-gray-800">Draft Ticket Preview</h2>
                                <button type="button" id="closePreviewBtn" onclick="closeTicketPreviewModal()" class="text-gray-500 hover:text-gray-700 text-lg font-semibold">
                                    ✕
                                </button>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <button type="button" onclick="if (typeof printTicketPreview === 'function') { printTicketPreview(); }" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 text-white px-4 py-2 text-sm font-semibold hover:bg-blue-700">
                                    <span>🖨</span> Print
                                </button>
                                <button type="button" onclick="if (typeof printTicketPreview === 'function') { printTicketPreview(); }" class="inline-flex items-center gap-2 rounded-lg bg-red-600 text-white px-4 py-2 text-sm font-semibold hover:bg-red-700">
                                    <span>📄</span> Export PDF
                                </button>
                            </div>
                        </div>

                        <div class="overflow-y-auto p-6 flex-1">
                            <div id="ticketPreviewContent"></div>
                        </div>
                    </div>
                </div>
                
                @push('scripts')
                <script>
                // Dependent selects: state -> projects -> applications -> modules
                document.addEventListener('DOMContentLoaded', function() {
                    const stateSelect = document.getElementById('state_id');
                    const projectSelect = document.getElementById('project_id');
                    const applicationSelect = document.getElementById('application_id');
                    const moduleSelect = document.getElementById('module_id');

                    function clearSelect(select, placeholder) {
                        select.innerHTML = '';
                        const opt = document.createElement('option');
                        opt.value = '';
                        opt.textContent = placeholder || 'Select';
                        select.appendChild(opt);
                    }

                    // Debounce + in-flight guard for projects loading to avoid duplicate calls
                    let _projectsTimer = null;
                    let _projectsToken = null;

                    // guard to ignore repeated identical requests within short window
                    let _lastProjectsState = null;
                    let _lastProjectsAt = 0;

                    async function doLoadProjects(stateId, token) {
                        const now = Date.now();
                        if (_lastProjectsState === String(stateId) && now - _lastProjectsAt < 1000) {
                            return;
                        }
                        _lastProjectsState = String(stateId);
                        _lastProjectsAt = now;

                        clearSelect(projectSelect, 'Select Project');
                        clearSelect(applicationSelect, 'Select Application');
                        clearSelect(moduleSelect, 'Select Module');

                        if (!stateId) return;

                        // disable to prevent user re-clicks
                        stateSelect.disabled = true;

                        try {
                            const res = await fetch('{{ route("issues.ajax.projects") }}?state_id=' + encodeURIComponent(stateId), {
                                headers: { 'X-Requested-With': 'XMLHttpRequest' }
                            });
                            if (!res.ok) return;
                            const data = await res.json();

                            // if a newer request started, ignore this response
                            if (_projectsToken !== token) return;

                            // dedupe by project_id in case backend returns duplicates
                            const seen = new Set();
                            data.forEach(function(p) {
                                if (!p || !p.project_id) return;
                                if (seen.has(String(p.project_id))) return;
                                seen.add(String(p.project_id));
                                const o = document.createElement('option');
                                o.value = p.project_id;
                                o.textContent = p.project_name;
                                projectSelect.appendChild(o);
                            });
                        } finally {
                            stateSelect.disabled = false;
                        }
                    }

                    function loadProjects(stateId) {
                        // cancel pending timer
                        if (_projectsTimer) {
                            clearTimeout(_projectsTimer);
                            _projectsTimer = null;
                        }
                        // create a token for this request
                        const token = String(Date.now()) + Math.random();
                        _projectsToken = token;

                        // debounce rapid changes (200ms)
                        _projectsTimer = setTimeout(() => doLoadProjects(stateId, token), 200);
                    }

                    async function loadApplications(projectId) {
                        clearSelect(applicationSelect, 'Select Application');
                        clearSelect(moduleSelect, 'Select Module');
                        if (!projectId) return;

                        const res = await fetch('{{ route("issues.ajax.applications") }}?project_id=' + encodeURIComponent(projectId), {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        if (!res.ok) return;
                        const data = await res.json();

                        const seen = new Set();
                        data.forEach(function(a) {
                            if (!a || !a.application_id) return;
                            if (seen.has(String(a.application_id))) return;
                            seen.add(String(a.application_id));
                            const o = document.createElement('option');
                            o.value = a.application_id;
                            o.textContent = a.application_name;
                            applicationSelect.appendChild(o);
                        });
                    }

                    async function loadModules(applicationId, projectId) {
                        clearSelect(moduleSelect, 'Select Module');
                        if (!applicationId) return;

                        const params = new URLSearchParams({ application_id: applicationId });
                        if (projectId) params.set('project_id', projectId);

                        const res = await fetch('{{ route("issues.ajax.modules") }}?' + params.toString(), {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        if (!res.ok) return;
                        const data = await res.json();

                        const seen = new Set();
                        data.forEach(function(m) {
                            if (!m || !m.module_id) return;
                            if (seen.has(String(m.module_id))) return;
                            seen.add(String(m.module_id));
                            const o = document.createElement('option');
                            o.value = m.module_id;
                            o.textContent = m.module_name;
                            moduleSelect.appendChild(o);
                        });
                    }

                    if (stateSelect && !stateSelect.dataset.handlerAttached) {
                        stateSelect.addEventListener('change', function() {
                            loadProjects(this.value);
                        });
                        stateSelect.dataset.handlerAttached = '1';
                    }

                    if (projectSelect && !projectSelect.dataset.handlerAttached) {
                        projectSelect.addEventListener('change', function() {
                            loadApplications(this.value);
                        });
                        projectSelect.dataset.handlerAttached = '1';
                    }

                    if (applicationSelect && !applicationSelect.dataset.handlerAttached) {
                        applicationSelect.addEventListener('change', function() {
                            loadModules(this.value, projectSelect.value);
                        });
                        applicationSelect.dataset.handlerAttached = '1';
                    }

                    // Auto-load projects if a state is already selected when form loads
                    if (stateSelect && stateSelect.value) {
                        loadProjects(stateSelect.value);
                    }
                });
                </script>
                @endpush

            </form>

        </div>

    </div>


</x-app-layout>