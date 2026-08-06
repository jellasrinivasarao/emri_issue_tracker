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

    <div class="custom-box">
        TODO
    </div>

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

                            {{-- Service --}}

                            <div>

                                <label class="block text-sm font-semibold mb-2">

                                    Service

                                    <span class="text-red-500">*</span>

                                </label>

                                <select id="service_id" name="service_id" class="w-full rounded-lg border-gray-300">

                                    <option value="">

                                        Select Service

                                    </option>

                                </select>

                            </div>

                            {{-- Project --}}

                            <div>

                                <label class="block text-sm font-semibold mb-2">

                                    Project

                                    <span class="text-red-500">*</span>

                                </label>

                                <select id="project_id" name="project_id" class="w-full rounded-lg border-gray-300">

                                    <option value="">

                                        Select Project

                                    </option>

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

                                    <option value="">

                                        Select Application

                                    </option>

                                </select>

                            </div>

                            {{-- Module --}}

                            <div>

                                <label class="block text-sm font-semibold mb-2">

                                    Module

                                </label>

                                <select id="module_id" name="module_id" class="w-full rounded-lg border-gray-300">

                                    <option value="">

                                        Select Module

                                    </option>

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

                                    <option value="{{ $category->id }}"
                                        {{ old('issue_category_id')==$category->id ? 'selected':'' }}>

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

                                    <option value="{{ $priority->id }}"
                                        {{ old('priority_id')==$priority->id ? 'selected':'' }}>

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

                <script src="{{ asset('js/issue.js') }}"></script>

                <script>
                document.getElementById('attachment').addEventListener('change', function() {

                    let file = this.files[0];

                    if (file) {

                        document.getElementById('selectedFile').innerHTML =
                            "Selected : " + file.name;

                    }

                });

                document.addEventListener('DOMContentLoaded', function() {

                    let subject = document.getElementById('subject');
                    let description = document.getElementById('description');

                    let subjectCount = document.getElementById('subjectCount');
                    let descriptionCount = document.getElementById('descriptionCount');

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

                });
                </script>



                @endpush

            </form>

        </div>

    </div>


</x-app-layout>