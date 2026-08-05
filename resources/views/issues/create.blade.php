<x-app-layout>

    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ $title ?? __('Issue Tracker') }}
        </h2>
    </x-slot>


    <div class="py-8">

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            {{-- =========================================================
                 MAIN CARD
            ========================================================== --}}

            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

                {{-- HEADER --}}
                <div class="border-b border-slate-200 bg-slate-50 px-5 py-5">

                    <div class="grid gap-4 md:grid-cols-[1fr_auto_auto] md:items-center">

                        <div>

                            <h3 class="text-lg font-semibold text-slate-900">
                                Issue Tracker
                            </h3>

                            <p class="text-sm text-slate-600">
                                {{ $description ?? __('Track, manage and resolve support issues.') }}
                            </p>

                        </div>


                        {{-- SEARCH --}}
                        <div
                            class="flex items-center justify-end rounded-2xl border border-slate-200 bg-white px-3 py-2 shadow-sm">

                            <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">

                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" />

                            </svg>

                            <input id="issue-search" type="text" placeholder="Search"
                                class="ml-2 w-36 bg-transparent text-sm text-slate-900 placeholder:text-slate-400 outline-none" />

                        </div>


                        {{-- ADD ISSUE --}}
                        <button type="button" onclick="openIssueModal()"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">

                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">

                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />

                            </svg>

                            Raise New Issue

                        </button>

                    </div>

                </div>


                {{-- EXPORT --}}
                <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">

                    <div class="flex flex-wrap items-center gap-3">

                        <button type="button" onclick="exportIssueTable('csv')"
                            class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-100">

                            Export CSV

                        </button>


                        <button type="button" onclick="exportIssueTable('xlsx')"
                            class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-semibold text-emerald-700 hover:bg-emerald-100">

                            Export XLSX

                        </button>


                        <button type="button" onclick="exportIssueTable('pdf')"
                            class="rounded-lg border border-purple-200 bg-purple-50 px-3 py-2 text-sm font-semibold text-purple-700 hover:bg-purple-100">

                            Export PDF

                        </button>

                    </div>

                </div>


                {{-- SESSION MESSAGE --}}
                @if(session('success') || session('error'))

                <div class="px-5 py-4" id="issue-message-container">

                    <div id="issue-message" class="relative rounded-2xl px-4 py-3 text-sm font-semibold shadow-sm
                            {{ session('success')
                                ? 'bg-emerald-50 text-emerald-700'
                                : 'bg-rose-50 text-rose-700' }}">

                        <span>
                            {{ session('success') ?? session('error') }}
                        </span>

                        <button type="button" onclick="closeIssueMessage()"
                            class="absolute right-3 top-3 rounded-full bg-white/80 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-white">

                            Close

                        </button>

                    </div>

                </div>

                @endif


                {{-- VALIDATION ERRORS --}}
                @if($errors->any())

                <div class="px-5 py-4">

                    <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4">

                        <p class="mb-2 text-sm font-semibold text-rose-700">
                            Please correct the following errors:
                        </p>

                        <ul class="list-disc space-y-1 pl-5 text-sm text-rose-600">

                            @foreach($errors->all() as $error)

                            <li>{{ $error }}</li>

                            @endforeach

                        </ul>

                    </div>

                </div>

                @endif


                {{-- =====================================================
                     ISSUE TABLE
                ====================================================== --}}

                <div class="overflow-x-auto">

                    <div class="max-h-[500px] overflow-auto">

                        <table class="min-w-full divide-y divide-slate-200">

                            <thead class="sticky top-0 z-10 bg-purple-100">

                                <tr>

                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.15em] text-purple-900">
                                        Issue No
                                    </th>

                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.15em] text-purple-900">
                                        Subject
                                    </th>

                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.15em] text-purple-900">
                                        Category
                                    </th>

                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.15em] text-purple-900">
                                        Priority
                                    </th>

                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.15em] text-purple-900">
                                        Routing
                                    </th>

                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.15em] text-purple-900">
                                        Status
                                    </th>

                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.15em] text-purple-900">
                                        Created
                                    </th>

                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.15em] text-purple-900">
                                        Actions
                                    </th>

                                </tr>

                            </thead>


                            <tbody id="issue-table-body" class="divide-y divide-slate-200 bg-white">

                                @forelse(($issues ?? []) as $issue)

                                <tr>

                                    {{-- ISSUE NO --}}
                                    <td class="px-5 py-3 text-sm font-semibold text-slate-900">

                                        {{ $issue->issue_no ?? ('ISSUE-' . $issue->issue_id) }}

                                    </td>


                                    {{-- SUBJECT --}}
                                    <td class="px-5 py-3 text-sm text-slate-700">

                                        <div class="max-w-xs">

                                            <p class="font-semibold">
                                                {{ $issue->subject }}
                                            </p>

                                            <p class="truncate text-xs text-slate-400">
                                                {{ $issue->description }}
                                            </p>

                                        </div>

                                    </td>


                                    {{-- CATEGORY --}}
                                    <td class="px-5 py-3 text-sm text-slate-600">

                                        {{ $issue->issue_category ?? '-' }}

                                    </td>


                                    {{-- PRIORITY --}}
                                    <td class="px-5 py-3 text-sm">

                                        @php

                                        $priority = strtoupper($issue->priority ?? 'MEDIUM');

                                        $priorityClass = match($priority) {

                                        'CRITICAL' =>
                                        'bg-rose-100 text-rose-700',

                                        'HIGH' =>
                                        'bg-orange-100 text-orange-700',

                                        'MEDIUM' =>
                                        'bg-amber-100 text-amber-700',

                                        default =>
                                        'bg-emerald-100 text-emerald-700',

                                        };

                                        @endphp


                                        <span
                                            class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $priorityClass }}">

                                            {{ ucfirst(strtolower($priority)) }}

                                        </span>

                                    </td>


                                    {{-- ROUTING --}}
                                    <td class="px-5 py-3 text-sm">

                                        @php

                                        $routing = strtoupper(
                                        $issue->routing_level
                                        ?? $issue->assigned_level
                                        ?? ''
                                        );

                                        @endphp


                                        @if($routing === 'HO_IT_LEVEL_1')

                                        <span
                                            class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700">
                                            HO IT Level 1
                                        </span>

                                        @elseif($routing === 'VENDOR_LEVEL_2')

                                        <span
                                            class="rounded-full bg-purple-50 px-2.5 py-1 text-xs font-semibold text-purple-700">
                                            Vendor Level 2
                                        </span>

                                        @else

                                        <span
                                            class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">
                                            Pending Routing
                                        </span>

                                        @endif

                                    </td>


                                    {{-- STATUS --}}
                                    <td class="px-5 py-3 text-sm">

                                        @php

                                        $status = strtoupper(
                                        $issue->status ?? 'OPEN'
                                        );

                                        @endphp


                                        @if($status === 'CLOSED')

                                        <span
                                            class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                            Closed
                                        </span>

                                        @elseif($status === 'REOPENED')

                                        <span
                                            class="rounded-full bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-700">
                                            Reopened
                                        </span>

                                        @elseif($status === 'IN_PROGRESS')

                                        <span
                                            class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700">
                                            In Progress
                                        </span>

                                        @else

                                        <span
                                            class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                            {{ ucfirst(strtolower(str_replace('_', ' ', $status))) }}
                                        </span>

                                        @endif

                                    </td>


                                    {{-- CREATED --}}
                                    <td class="px-5 py-3 text-sm text-slate-600">

                                        {{ optional($issue->created_at)->format('d M Y H:i') }}

                                    </td>


                                    {{-- ACTIONS --}}
                                    <td class="px-5 py-3 text-sm">

                                        <div class="flex flex-wrap gap-2">

                                            @if(Route::has('issues.show'))

                                            <a href="{{ route('issues.show', ['issue_id' => $issue->issue_id]) }}"
                                                class="rounded-lg bg-slate-900 px-2.5 py-1.5 text-xs font-semibold text-white hover:bg-slate-800">

                                                View

                                            </a>

                                            @endif

                                        </div>

                                    </td>

                                </tr>

                                @empty

                                <tr class="empty-row">

                                    <td colspan="8" class="px-5 py-10 text-center text-sm text-slate-500">

                                        No issues found.

                                    </td>

                                </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- ================================================================
         RAISE ISSUE MODAL
    ================================================================= --}}

    <div id="issue-modal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 px-4 py-6 sm:px-6">

        <div class="mx-auto flex min-h-full items-center justify-center">

            <div class="flex w-full max-w-5xl flex-col overflow-hidden rounded-3xl bg-white shadow-2xl">


                {{-- MODAL HEADER --}}
                <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-6 py-4">

                    <div>

                        <h3 id="issue-modal-title" class="text-lg font-semibold text-slate-900">

                            Raise New Issue

                        </h3>

                        <p class="text-sm text-slate-600">

                            Provide the issue information below.

                        </p>

                    </div>


                    <button type="button" onclick="closeIssueModal()"
                        class="rounded-full bg-slate-100 p-2 text-slate-600 hover:bg-slate-200 hover:text-slate-900">

                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">

                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />

                        </svg>

                    </button>

                </div>


                {{-- FORM --}}
                <form id="issue-form" method="POST" action="{{ route('issues.store') }}" enctype="multipart/form-data">

                    @csrf


                    {{-- =================================================
                         SERVICE INFORMATION
                    ================================================== --}}

                    <div class="border-b border-slate-200 px-6 py-5">

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
                                    Select the affected service and application.
                                </p>

                            </div>

                        </div>


                        <div class="grid gap-4 md:grid-cols-2">


                            {{-- STATE --}}
                            <div>

                                <label for="state_id" class="mb-1 block text-sm font-medium text-slate-700">

                                    State
                                    <span class="text-rose-500">*</span>

                                </label>

                                <select id="state_id" name="state_id" required
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

                                    <option value="">
                                        Select State
                                    </option>

                                    @foreach(($states ?? []) as $state)

                                    <option value="{{ $state->state_id }}" @selected(old('state_id')==$state->state_id)>

                                        {{ $state->state_name }}

                                    </option>

                                    @endforeach

                                </select>

                            </div>


                            {{-- SERVICE --}}
                            <div>

                                <label for="service_id" class="mb-1 block text-sm font-medium text-slate-700">

                                    Service
                                    <span class="text-rose-500">*</span>

                                </label>

                                <select id="service_id" name="service_id" required
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

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

                            </div>


                            {{-- PROJECT --}}
                            <div>

                                <label for="project_id" class="mb-1 block text-sm font-medium text-slate-700">

                                    Project
                                    <span class="text-rose-500">*</span>

                                </label>

                                <select id="project_id" name="project_id" required
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

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

                            </div>


                            {{-- APPLICATION --}}
                            <div>

                                <label for="application_id" class="mb-1 block text-sm font-medium text-slate-700">

                                    Application
                                    <span class="text-rose-500">*</span>

                                </label>

                                <select id="application_id" name="application_id" required
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

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

                            </div>


                            {{-- MODULE --}}
                            <div class="md:col-span-2">

                                <label for="module_id" class="mb-1 block text-sm font-medium text-slate-700">

                                    Module

                                </label>

                                <select id="module_id" name="module_id"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

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

                            </div>

                        </div>

                    </div>


                    {{-- =================================================
                         ISSUE INFORMATION
                    ================================================== --}}

                    <div class="border-b border-slate-200 px-6 py-5">

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
                                    Provide issue details and priority.
                                </p>

                            </div>

                        </div>


                        <div class="grid gap-4 md:grid-cols-2">


                            {{-- CATEGORY --}}
                            <div>

                                <label for="issue_category" class="mb-1 block text-sm font-medium text-slate-700">

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

                            </div>


                            {{-- PRIORITY --}}
                            <div>

                                <label for="priority" class="mb-1 block text-sm font-medium text-slate-700">

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

                            </div>


                            {{-- ISSUE TYPE --}}
                            <div>

                                <label for="issue_type" class="mb-1 block text-sm font-medium text-slate-700">

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

                            </div>


                            {{-- SUBJECT --}}
                            <div>

                                <label for="subject" class="mb-1 block text-sm font-medium text-slate-700">

                                    Subject
                                    <span class="text-rose-500">*</span>

                                </label>

                                <input id="subject" name="subject" type="text" required maxlength="255"
                                    value="{{ old('subject') }}" placeholder="Enter issue subject"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

                            </div>


                            {{-- DESCRIPTION --}}
                            <div class="md:col-span-2">

                                <label for="description" class="mb-1 block text-sm font-medium text-slate-700">

                                    Description
                                    <span class="text-rose-500">*</span>

                                </label>

                                <textarea id="description" name="description" rows="4" maxlength="5000" required
                                    placeholder="Describe the issue, operational impact and observations"
                                    class="w-full resize-y rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">{{ old('description') }}</textarea>

                                <div class="mt-1 text-right text-xs text-slate-400">
                                    <span id="description-counter">0</span> / 5000
                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- =================================================
                         SUPPORTING INFORMATION
                    ================================================== --}}

                    <div class="px-6 py-5">

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
                                    Add additional information and attachments.
                                </p>

                            </div>

                        </div>


                        <div class="grid gap-4 md:grid-cols-2">


                            {{-- OCCURRED --}}
                            <div>

                                <label for="occurred_on" class="mb-1 block text-sm font-medium text-slate-700">

                                    Occurred On
                                    <span class="text-rose-500">*</span>

                                </label>

                                <input id="occurred_on" name="occurred_on" type="datetime-local" required
                                    value="{{ old('occurred_on', now()->format('Y-m-d\TH:i')) }}"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

                            </div>


                            {{-- AFFECTED USERS --}}
                            <div>

                                <label for="affected_users" class="mb-1 block text-sm font-medium text-slate-700">

                                    Affected Users

                                </label>

                                <input id="affected_users" name="affected_users" type="text"
                                    value="{{ old('affected_users') }}" placeholder="Enter usernames or user groups"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-blue-500 focus:ring-2 focus:ring-blue-100">

                            </div>


                            {{-- ATTACHMENT --}}
                            <div class="md:col-span-2">

                                <label for="attachment" class="mb-1 block text-sm font-medium text-slate-700">

                                    Attachment

                                </label>

                                <input id="attachment" name="attachment" type="file"
                                    accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx"
                                    class="block w-full rounded-xl border border-slate-200 bg-white text-sm text-slate-600 file:mr-4 file:border-0 file:bg-slate-100 file:px-4 file:py-2.5 file:text-sm file:font-semibold file:text-slate-700 hover:file:bg-slate-200">

                                <p class="mt-1 text-xs text-slate-500">
                                    Allowed: jpg, jpeg, png, pdf, doc, docx, xls, xlsx. Maximum 10 MB.
                                </p>

                            </div>

                        </div>

                    </div>


                    {{-- ROUTING INFO --}}
                    <div class="mx-6 mb-5 rounded-2xl border border-blue-200 bg-blue-50 p-4">

                        <div class="flex gap-3">

                            <svg class="mt-0.5 h-5 w-5 flex-shrink-0 text-blue-600" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">

                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z" />

                            </svg>

                            <div>

                                <p class="text-sm font-semibold text-blue-800">
                                    Automatic Issue Routing
                                </p>

                                <p class="mt-1 text-xs leading-5 text-blue-700">

                                    After submission, the system will evaluate the configured
                                    support rules and route the issue to
                                    <strong>HO IT Level 1</strong> or
                                    <strong>Vendor Level 2</strong>.

                                </p>

                            </div>

                        </div>

                    </div>


                    {{-- MODAL FOOTER --}}
                    <div class="flex items-center justify-end gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4">

                        <button type="button" onclick="closeIssueModal()"
                            class="rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100">

                            Cancel

                        </button>


                        <button type="submit" id="issue-submit-button"
                            class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-700">

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


    {{-- ================================================================
         JAVASCRIPT
    ================================================================= --}}

    <script>
    function openIssueModal() {

        const modal = document.getElementById('issue-modal');

        if (!modal) {
            return;
        }

        modal.classList.remove('hidden');

        document.body.classList.add('overflow-hidden');

    }


    function closeIssueModal() {

        const modal = document.getElementById('issue-modal');

        if (!modal) {
            return;
        }

        modal.classList.add('hidden');

        document.body.classList.remove('overflow-hidden');

    }


    /*
     * Close modal when clicking outside
     */
    document
        .getElementById('issue-modal')
        ?.addEventListener('click', function(event) {

            if (event.target === this) {

                closeIssueModal();

            }

        });


    /*
     * ESC key
     */
    document.addEventListener('keydown', function(event) {

        if (event.key === 'Escape') {

            closeIssueModal();

        }

    });


    /*
     * Description counter
     */
    const description =
        document.getElementById('description');

    const counter =
        document.getElementById('description-counter');


    if (description && counter) {

        function updateDescriptionCounter() {

            counter.textContent =
                description.value.length;

        }

        description.addEventListener(
            'input',
            updateDescriptionCounter
        );

        updateDescriptionCounter();

    }


    /*
     * Search
     */
    const searchInput =
        document.getElementById('issue-search');


    if (searchInput) {

        searchInput.addEventListener('input', function() {

            const query =
                this.value.trim().toLowerCase();

            const rows =
                document.querySelectorAll(
                    '#issue-table-body tr'
                );

            let visibleCount = 0;


            rows.forEach(function(row) {

                if (row.classList.contains('empty-row')) {
                    return;
                }

                const text =
                    row.textContent.toLowerCase();

                const match =
                    query === '' ||
                    text.includes(query);

                if (match) {

                    row.classList.remove('hidden');

                    visibleCount++;

                } else {

                    row.classList.add('hidden');

                }

            });

        });

    }


    /*
     * Prevent double submission
     */
    const issueForm =
        document.getElementById('issue-form');

    const submitButton =
        document.getElementById('issue-submit-button');


    if (issueForm && submitButton) {

        issueForm.addEventListener('submit', function() {

            submitButton.disabled = true;

            submitButton.innerHTML = `
                    <svg class="h-4 w-4 animate-spin"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="2"
                         viewBox="0 0 24 24">

                        <circle
                            cx="12"
                            cy="12"
                            r="9"
                            stroke-opacity=".25">
                        </circle>

                        <path d="M21 12a9 9 0 0 1-9 9">
                        </path>

                    </svg>

                    Submitting...
                `;

        });

    }


    /*
     * Export
     */
    function exportIssueTable(format) {

        const params =
            new URLSearchParams({
                format: format
            });

        window.location.href =
            '{{ route('
        issues.index ') }}?' +
            params.toString();

    }


    /*
     * Message close
     */
    function closeIssueMessage() {

        const container =
            document.getElementById(
                'issue-message-container'
            );

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


    /*
     * Automatically open modal when validation
     * failed after POST.
     */
    @if($errors - > any())

    document.addEventListener(
        'DOMContentLoaded',
        function() {

            openIssueModal();

        }
    );

    @endif


    /*
     * Auto-close success message
     */
    document.addEventListener(
        'DOMContentLoaded',
        function() {

            const message =
                document.getElementById(
                    'issue-message'
                );

            if (message) {

                setTimeout(
                    closeIssueMessage,
                    10000
                );

            }

        }
    );
    </script>

</x-app-layout>