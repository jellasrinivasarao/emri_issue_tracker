<x-app-layout>

    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ $title ?? __('Raise New Issue') }}
        </h2>
    </x-slot>

    <div class="py-6">

        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">

            {{-- SUCCESS / ERROR MESSAGE --}}
            @if(session('success') || session('error'))
            <div id="issue-message-container" class="mb-5">

                <div id="issue-message" class="relative rounded-2xl px-4 py-3 text-sm font-semibold shadow-sm
                         {{ session('success')
                            ? 'bg-emerald-50 text-emerald-700 border border-emerald-200'
                            : 'bg-rose-50 text-rose-700 border border-rose-200' }}">

                    <span>
                        {{ session('success') ?? session('error') }}
                    </span>

                    <button type="button" onclick="closeIssueMessage()"
                        class="absolute right-3 top-2 rounded-full bg-white/80 px-2 py-1 text-xs text-slate-600 hover:bg-white">
                        ×
                    </button>

                </div>

            </div>
            @endif


            {{-- VALIDATION ERRORS --}}
            @if($errors->any())

            <div class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 p-4">

                <div class="mb-2 font-semibold text-rose-700">
                    Please correct the following errors:
                </div>

                <ul class="list-disc space-y-1 pl-5 text-sm text-rose-600">

                    @foreach($errors->all() as $error)

                    <li>{{ $error }}</li>

                    @endforeach

                </ul>

            </div>

            @endif


            {{-- MAIN CARD --}}
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

                {{-- HEADER --}}
                <div class="border-b border-slate-200 bg-slate-50 px-6 py-5">

                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">

                        <div>

                            <h3 class="text-lg font-semibold text-slate-900">
                                Raise New Issue
                            </h3>

                            <p class="text-sm text-slate-600">
                                Create a support issue. The issue routing engine will automatically determine the
                                appropriate support team.
                            </p>

                        </div>

                        <div class="rounded-xl bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-700">

                            Automatic Routing Enabled

                        </div>

                    </div>

                </div>


                {{-- FORM --}}
                <form method="POST" action="{{ route('issues.store') }}" enctype="multipart/form-data" id="issue-form">

                    @csrf


                    {{-- =====================================================
                         SECTION 1 : SERVICE INFORMATION
                    ====================================================== --}}

                    <div class="border-b border-slate-200 px-6 py-6">

                        <div class="mb-5 flex items-center gap-3">

                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-sm font-bold text-white">
                                1
                            </div>

                            <div>

                                <h4 class="text-sm font-bold uppercase tracking-wide text-blue-700">
                                    Service Information
                                </h4>

                                <p class="text-xs text-slate-500">
                                    Select the service and application affected by the issue.
                                </p>

                            </div>

                        </div>


                        <div class="grid gap-5 md:grid-cols-2">


                            {{-- STATE --}}
                            <div>

                                <label for="state_id" class="mb-1.5 block text-sm font-medium text-slate-700">

                                    State
                                    <span class="text-rose-500">*</span>

                                </label>

                                <select id="state_id" name="state_id" required
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

                                    <option value="">
                                        Select State
                                    </option>

                                    @foreach(($states ?? []) as $state)

                                    <option value="{{ $state->state_id }}" @selected(old('state_id')==$state->state_id)>

                                        {{ $state->state_name }}

                                    </option>

                                    @endforeach

                                </select>

                                @error('state_id')

                                <p class="mt-1 text-xs text-rose-600">
                                    {{ $message }}
                                </p>

                                @enderror

                            </div>


                            {{-- SERVICE --}}
                            <div>

                                <label for="service_id" class="mb-1.5 block text-sm font-medium text-slate-700">

                                    Service
                                    <span class="text-rose-500">*</span>

                                </label>

                                <select id="service_id" name="service_id" required
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

                                    <option value="">
                                        Select Service
                                    </option>

                                    @foreach(($services ?? []) as $service)

                                    <option value="{{ $service->service_id }}" @selected(old('service_id')==$service->
                                        service_id)>

                                        {{ $service->service_name ?? $service->name }}

                                    </option>

                                    @endforeach

                                </select>

                                @error('service_id')

                                <p class="mt-1 text-xs text-rose-600">
                                    {{ $message }}
                                </p>

                                @enderror

                            </div>


                            {{-- PROJECT --}}
                            <div>

                                <label for="project_id" class="mb-1.5 block text-sm font-medium text-slate-700">

                                    Project
                                    <span class="text-rose-500">*</span>

                                </label>

                                <select id="project_id" name="project_id" required
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

                                    <option value="">
                                        Select Project
                                    </option>

                                    @foreach(($projects ?? []) as $project)

                                    <option value="{{ $project->project_id }}" @selected(old('project_id')==$project->
                                        project_id)>

                                        {{ $project->project_name ?? $project->name }}

                                    </option>

                                    @endforeach

                                </select>

                                @error('project_id')

                                <p class="mt-1 text-xs text-rose-600">
                                    {{ $message }}
                                </p>

                                @enderror

                            </div>


                            {{-- APPLICATION --}}
                            <div>

                                <label for="application_id" class="mb-1.5 block text-sm font-medium text-slate-700">

                                    Application
                                    <span class="text-rose-500">*</span>

                                </label>

                                <select id="application_id" name="application_id" required
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

                                    <option value="">
                                        Select Application
                                    </option>

                                    @foreach(($applications ?? []) as $application)

                                    <option value="{{ $application->application_id }}"
                                        @selected(old('application_id')==$application->application_id)>

                                        {{ $application->application_name ?? $application->name }}

                                    </option>

                                    @endforeach

                                </select>

                                @error('application_id')

                                <p class="mt-1 text-xs text-rose-600">
                                    {{ $message }}
                                </p>

                                @enderror

                            </div>


                            {{-- MODULE --}}
                            <div class="md:col-span-2">

                                <label for="module_id" class="mb-1.5 block text-sm font-medium text-slate-700">

                                    Module

                                </label>

                                <select id="module_id" name="module_id"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

                                    <option value="">
                                        Select Module
                                    </option>

                                    @foreach(($modules ?? []) as $module)

                                    <option value="{{ $module->module_id }}" @selected(old('module_id')==$module->
                                        module_id)>

                                        {{ $module->module_name ?? $module->name }}

                                    </option>

                                    @endforeach

                                </select>

                                @error('module_id')

                                <p class="mt-1 text-xs text-rose-600">
                                    {{ $message }}
                                </p>

                                @enderror

                            </div>

                        </div>

                    </div>


                    {{-- =====================================================
                         SECTION 2 : ISSUE INFORMATION
                    ====================================================== --}}

                    <div class="border-b border-slate-200 px-6 py-6">

                        <div class="mb-5 flex items-center gap-3">

                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-sm font-bold text-white">
                                2
                            </div>

                            <div>

                                <h4 class="text-sm font-bold uppercase tracking-wide text-blue-700">
                                    Issue Information
                                </h4>

                                <p class="text-xs text-slate-500">
                                    Provide the issue details and business impact.
                                </p>

                            </div>

                        </div>


                        <div class="grid gap-5 md:grid-cols-2">


                            {{-- ISSUE CATEGORY --}}
                            <div>

                                <label for="issue_category" class="mb-1.5 block text-sm font-medium text-slate-700">

                                    Issue Category
                                    <span class="text-rose-500">*</span>

                                </label>

                                <select id="issue_category" name="issue_category" required
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

                                    <option value="">
                                        Select Issue Category
                                    </option>

                                    <option value="APPLICATION" @selected(old('issue_category')==='APPLICATION' )>
                                        Application Issue
                                    </option>

                                    <option value="NETWORK" @selected(old('issue_category')==='NETWORK' )>
                                        Network Issue
                                    </option>

                                    <option value="HARDWARE" @selected(old('issue_category')==='HARDWARE' )>
                                        Hardware Issue
                                    </option>

                                    <option value="DATABASE" @selected(old('issue_category')==='DATABASE' )>
                                        Database Issue
                                    </option>

                                    <option value="SECURITY" @selected(old('issue_category')==='SECURITY' )>
                                        Security Issue
                                    </option>

                                    <option value="OTHER" @selected(old('issue_category')==='OTHER' )>
                                        Other
                                    </option>

                                </select>

                                @error('issue_category')

                                <p class="mt-1 text-xs text-rose-600">
                                    {{ $message }}
                                </p>

                                @enderror

                            </div>


                            {{-- PRIORITY --}}
                            <div>

                                <label for="priority" class="mb-1.5 block text-sm font-medium text-slate-700">

                                    Priority
                                    <span class="text-rose-500">*</span>

                                </label>

                                <select id="priority" name="priority" required
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

                                    <option value="LOW" @selected(old('priority', 'MEDIUM' )==='LOW' )>
                                        Low
                                    </option>

                                    <option value="MEDIUM" @selected(old('priority', 'MEDIUM' )==='MEDIUM' )>
                                        Medium
                                    </option>

                                    <option value="HIGH" @selected(old('priority')==='HIGH' )>
                                        High
                                    </option>

                                    <option value="CRITICAL" @selected(old('priority')==='CRITICAL' )>
                                        Critical
                                    </option>

                                </select>

                                @error('priority')

                                <p class="mt-1 text-xs text-rose-600">
                                    {{ $message }}
                                </p>

                                @enderror

                            </div>


                            {{-- ISSUE TYPE --}}
                            <div>

                                <label for="issue_type" class="mb-1.5 block text-sm font-medium text-slate-700">

                                    Issue Type
                                    <span class="text-rose-500">*</span>

                                </label>

                                <select id="issue_type" name="issue_type" required
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

                                    <option value="">
                                        Select Issue Type
                                    </option>

                                    <option value="INCIDENT" @selected(old('issue_type')==='INCIDENT' )>
                                        Incident
                                    </option>

                                    <option value="SERVICE_REQUEST" @selected(old('issue_type')==='SERVICE_REQUEST' )>
                                        Service Request
                                    </option>

                                    <option value="PROBLEM" @selected(old('issue_type')==='PROBLEM' )>
                                        Problem
                                    </option>

                                    <option value="ACCESS" @selected(old('issue_type')==='ACCESS' )>
                                        Access Request
                                    </option>

                                </select>

                                @error('issue_type')

                                <p class="mt-1 text-xs text-rose-600">
                                    {{ $message }}
                                </p>

                                @enderror

                            </div>


                            {{-- SUBJECT --}}
                            <div>

                                <label for="subject" class="mb-1.5 block text-sm font-medium text-slate-700">

                                    Subject
                                    <span class="text-rose-500">*</span>

                                </label>

                                <input id="subject" type="text" name="subject" value="{{ old('subject') }}"
                                    maxlength="255" required placeholder="Enter issue subject"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

                                @error('subject')

                                <p class="mt-1 text-xs text-rose-600">
                                    {{ $message }}
                                </p>

                                @enderror

                            </div>


                            {{-- DESCRIPTION --}}
                            <div class="md:col-span-2">

                                <label for="description" class="mb-1.5 block text-sm font-medium text-slate-700">

                                    Description
                                    <span class="text-rose-500">*</span>

                                </label>

                                <textarea id="description" name="description" rows="5" required maxlength="5000"
                                    placeholder="Describe the issue, operational impact and observations"
                                    class="w-full resize-y rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">{{ old('description') }}</textarea>

                                <div class="mt-1 flex justify-between">

                                    @error('description')

                                    <p class="text-xs text-rose-600">
                                        {{ $message }}
                                    </p>

                                    @else

                                    <span></span>

                                    @enderror

                                    <span id="description-counter" class="text-xs text-slate-400">
                                        0 / 5000
                                    </span>

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- =====================================================
                         SECTION 3 : SUPPORTING INFORMATION
                    ====================================================== --}}

                    <div class="px-6 py-6">

                        <div class="mb-5 flex items-center gap-3">

                            <div
                                class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-600 text-sm font-bold text-white">
                                3
                            </div>

                            <div>

                                <h4 class="text-sm font-bold uppercase tracking-wide text-blue-700">
                                    Supporting Information
                                </h4>

                                <p class="text-xs text-slate-500">
                                    Add occurrence details, affected users and supporting documents.
                                </p>

                            </div>

                        </div>


                        <div class="grid gap-5 md:grid-cols-2">


                            {{-- OCCURRED ON --}}
                            <div>

                                <label for="occurred_on" class="mb-1.5 block text-sm font-medium text-slate-700">

                                    Occurred On
                                    <span class="text-rose-500">*</span>

                                </label>

                                <input id="occurred_on" type="datetime-local" name="occurred_on"
                                    value="{{ old('occurred_on', now()->format('Y-m-d\TH:i')) }}" required
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

                                @error('occurred_on')

                                <p class="mt-1 text-xs text-rose-600">
                                    {{ $message }}
                                </p>

                                @enderror

                            </div>


                            {{-- AFFECTED USERS --}}
                            <div>

                                <label for="affected_users" class="mb-1.5 block text-sm font-medium text-slate-700">

                                    Affected Users

                                </label>

                                <input id="affected_users" type="text" name="affected_users"
                                    value="{{ old('affected_users') }}" placeholder="Enter usernames or user groups"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

                                @error('affected_users')

                                <p class="mt-1 text-xs text-rose-600">
                                    {{ $message }}
                                </p>

                                @enderror

                            </div>


                            {{-- ATTACHMENT --}}
                            <div class="md:col-span-2">

                                <label for="attachment" class="mb-1.5 block text-sm font-medium text-slate-700">

                                    Attachment

                                </label>

                                <input id="attachment" type="file" name="attachment"
                                    accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xlsx,.xls"
                                    class="block w-full cursor-pointer rounded-xl border border-slate-200 bg-white text-sm text-slate-600 file:mr-4 file:border-0 file:bg-slate-100 file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-slate-700 hover:file:bg-slate-200">

                                <p class="mt-1.5 text-xs text-slate-500">
                                    Allowed file types: jpg, jpeg, png, pdf, doc, docx, xls, xlsx. Maximum size: 10 MB.
                                </p>

                                @error('attachment')

                                <p class="mt-1 text-xs text-rose-600">
                                    {{ $message }}
                                </p>

                                @enderror

                            </div>

                        </div>

                    </div>


                    {{-- =====================================================
                         ROUTING PREVIEW
                    ====================================================== --}}

                    <div class="mx-6 mb-6 rounded-2xl border border-blue-200 bg-blue-50 p-4">

                        <div class="flex gap-3">

                            <div class="mt-0.5">

                                <svg class="h-5 w-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">

                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z" />

                                </svg>

                            </div>

                            <div>

                                <p class="text-sm font-semibold text-blue-800">
                                    Automatic Issue Routing
                                </p>

                                <p class="mt-1 text-xs leading-5 text-blue-700">

                                    After submission, the Issue Routing Engine will evaluate the
                                    Project Support Configuration and determine whether the issue
                                    should be routed to <strong>HO IT Level 1</strong> or
                                    <strong>Vendor Level 2</strong>.

                                </p>

                            </div>

                        </div>

                    </div>


                    {{-- =====================================================
                         ACTIONS
                    ====================================================== --}}

                    <div
                        class="flex flex-col-reverse gap-3 border-t border-slate-200 bg-slate-50 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">

                        <a href="{{ route('issues.index') }}"
                            class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 shadow-sm transition hover:bg-slate-100">

                            Cancel

                        </a>


                        <button type="submit" id="submit-issue-button"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-200 disabled:cursor-not-allowed disabled:opacity-60">

                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">

                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 19V5m0 0l-5 5m5-5l5 5" />

                            </svg>

                            Submit Issue

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>


    {{-- =============================================================
         JAVASCRIPT
    ============================================================= --}}

    <script>
    document.addEventListener('DOMContentLoaded', function() {

        /*
         * Description counter
         */
        const description = document.getElementById('description');
        const counter = document.getElementById('description-counter');

        function updateDescriptionCounter() {

            if (!description || !counter) {
                return;
            }

            counter.textContent =
                description.value.length + ' / 5000';

        }

        if (description) {

            description.addEventListener(
                'input',
                updateDescriptionCounter
            );

            updateDescriptionCounter();

        }


        /*
         * Submit protection
         */
        const form = document.getElementById('issue-form');
        const submitButton =
            document.getElementById('submit-issue-button');

        if (form && submitButton) {

            form.addEventListener('submit', function() {

                submitButton.disabled = true;

                submitButton.innerHTML = `
                        <svg class="h-4 w-4 animate-spin"
                             fill="none"
                             stroke="currentColor"
                             stroke-width="2"
                             viewBox="0 0 24 24">

                            <circle cx="12"
                                    cy="12"
                                    r="9"
                                    stroke-opacity=".25"></circle>

                            <path d="M21 12a9 9 0 0 1-9 9"></path>

                        </svg>

                        Submitting...
                    `;

            });

        }


        /*
         * State -> Service / Project dependent loading
         *
         * Enable this only when your controller/API routes
         * are available.
         */
        const stateSelect =
            document.getElementById('state_id');

        if (stateSelect) {

            stateSelect.addEventListener('change', function() {

                /*
                 * Future AJAX dependency loading can be placed here.
                 *
                 * Example:
                 *
                 * loadServices(this.value);
                 */

            });

        }


        /*
         * Auto close session message
         */
        const message =
            document.getElementById('issue-message');

        if (message) {

            setTimeout(function() {

                closeIssueMessage();

            }, 10000);

        }

    });


    function closeIssueMessage() {

        const container =
            document.getElementById('issue-message-container');

        if (!container) {
            return;
        }

        container.style.transition =
            'opacity 0.4s ease';

        container.style.opacity = '0';

        setTimeout(function() {

            container.remove();

        }, 400);

    }
    </script>

</x-app-layout>