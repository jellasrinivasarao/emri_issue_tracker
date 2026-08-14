<x-app-layout>

    <style>
    [x-cloak] {
        display: none !important;
    }

    .issue-tracker {
        font-family: 'Manrope', 'Segoe UI', sans-serif;
    }

    .issue-tracker * {
        font-family: 'Manrope', 'Segoe UI', sans-serif;
    }

    .issue-scroll::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }

    .issue-scroll::-webkit-scrollbar-track {
        background: #f8fafc;
    }

    .issue-scroll::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }

    .drawer-scroll::-webkit-scrollbar {
        width: 5px;
    }

    .drawer-scroll::-webkit-scrollbar-track {
        background: #ffffff;
    }

    .drawer-scroll::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }

    .issue-row {
        transition:
            background-color 0.15s ease,
            box-shadow 0.15s ease;
    }

    .issue-row:hover {
        background: #f5f9ff;
        box-shadow: inset 3px 0 0 #0754b8;
    }

    .filter-select {
        appearance: auto;
    }

    .drawer-shadow {
        box-shadow:
            -12px 0 30px -20px rgba(15, 23, 42, 0.45),
            0 0 0 1px rgba(15, 23, 42, 0.02);
    }

    .pagination-wrapper nav {
        display: flex;
        align-items: center;
        gap: 3px;
    }

    .pagination-wrapper nav>div:first-child {
        display: none;
    }

    .pagination-wrapper nav span,
    .pagination-wrapper nav a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 27px;
        height: 27px;
        border: 1px solid #dbe2ea;
        border-radius: 4px;
        padding: 0 7px;
        font-size: 9px;
        font-weight: 600;
    }

    .pagination-wrapper nav span {
        color: #64748b;
        background: #fff;
    }

    .pagination-wrapper nav a {
        color: #475569;
        background: #fff;
    }

    .pagination-wrapper nav a:hover {
        color: #0754b8;
        background: #f5f9ff;
        border-color: #93c5fd;
    }

    .pagination-wrapper nav span[aria-current="page"] {
        color: #fff;
        background: #0754b8;
        border-color: #0754b8;
    }
    </style>


    <div id="issue-tracker" x-data="issueTracker()" x-cloak
        class="issue-tracker min-h-full bg-[#f6f8fb] text-slate-800">

        {{-- ============================================================
            PAGE HEADER
        ============================================================= --}}

        <section class="border-b border-slate-200 bg-white">

            <div class="flex min-h-[62px] items-center justify-between px-5">

                <div>

                    <div class="text-[9px] font-semibold uppercase
                                tracking-[0.18em] text-slate-400">
                        Operations
                    </div>

                    <h1 class="mt-0.5 text-[18px] font-bold leading-tight
                               tracking-tight text-slate-800">
                        Issue Tracker
                    </h1>

                </div>


                <div class="flex items-center gap-2">

                    {{-- Refresh --}}
                    <a href="{{ route('issues.index') }}" class="inline-flex h-8 items-center rounded border
                               border-slate-300 bg-white px-3
                               text-[10px] font-semibold text-slate-600
                               transition hover:bg-slate-50">
                        Refresh
                    </a>


                    {{-- Export --}}
                    <button type="button" @click="exportIssues()" class="inline-flex h-8 items-center gap-1.5 rounded
                               bg-[#0754B8] px-3 text-[10px]
                               font-semibold text-white shadow-sm
                               transition hover:bg-[#06479c]">

                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 3v12m0 0 4-4m-4 4-4-4M5 21h14" />
                        </svg>

                        Export

                    </button>

                </div>

            </div>

        </section>


        {{-- ============================================================
            SEARCH + FILTERS
        ============================================================= --}}

        <section class="border-b border-slate-200 bg-white px-5 py-4">

            <form method="GET" action="{{ route('issues.index') }}">

                {{-- Search --}}
                <div class="flex items-center gap-2">

                    <div class="relative w-full max-w-[320px]">

                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Issue ID / Subject" class="h-9 w-full rounded border
                                   border-slate-300 bg-white
                                   px-3 pr-[75px] text-[10px]
                                   text-slate-700 outline-none
                                   placeholder:text-slate-400
                                   focus:border-[#0754B8]
                                   focus:ring-1 focus:ring-[#0754B8]" />

                        <button type="submit" class="absolute right-1 top-1 flex h-7
                                   items-center gap-1 rounded
                                   bg-[#0754B8] px-2.5
                                   text-[9px] font-semibold text-white
                                   transition hover:bg-[#06479c]">

                            <svg class="h-3 w-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="11" cy="11" r="7" />
                                <path stroke-linecap="round" d="m20 20-4-4" />
                            </svg>

                            Search

                        </button>

                    </div>


                    <a href="{{ route('issues.index') }}" class="text-[10px] font-semibold text-[#0754B8]
                               hover:underline">
                        Clear
                    </a>

                </div>


                {{-- Filters --}}
                <div class="mt-3 flex flex-wrap items-center gap-2">

                    <span class="mr-1 text-[9px] font-bold uppercase
                                 tracking-[0.15em] text-slate-400">
                        Filters:
                    </span>


                    {{-- Status --}}
                    <select name="status_id" onchange="this.form.submit()" class="filter-select h-8 min-w-[100px] rounded
                               border border-slate-300 bg-white
                               px-2.5 text-[9px] text-slate-600
                               outline-none focus:border-[#0754B8]
                               focus:ring-1 focus:ring-[#0754B8]">

                        <option value="">Status</option>

                        @foreach($statuses as $status)

                        <option value="{{ $status->status_id }}" @selected(request('status_id')==$status->status_id)
                            >
                            {{ $status->status_name }}
                        </option>

                        @endforeach

                    </select>


                    {{-- Priority --}}
                    <select name="priority_id" onchange="this.form.submit()" class="filter-select h-8 min-w-[100px] rounded
                               border border-slate-300 bg-white
                               px-2.5 text-[9px] text-slate-600
                               outline-none focus:border-[#0754B8]
                               focus:ring-1 focus:ring-[#0754B8]">

                        <option value="">Priority</option>

                        @foreach($priorities as $priority)

                        <option value="{{ $priority->priority_id }}" @selected(request('priority_id')==$priority->
                            priority_id)
                            >
                            {{ $priority->priority_name }}
                        </option>

                        @endforeach

                    </select>


                    {{-- Service --}}
                    <select name="service_id" onchange="this.form.submit()" class="filter-select h-8 min-w-[100px] rounded
                               border border-slate-300 bg-white
                               px-2.5 text-[9px] text-slate-600
                               outline-none focus:border-[#0754B8]
                               focus:ring-1 focus:ring-[#0754B8]">

                        <option value="">Service</option>

                        @foreach($services as $service)

                        <option value="{{ $service->service_id }}" @selected(request('service_id')==$service->
                            service_id)
                            >
                            {{ $service->service_name }}
                        </option>

                        @endforeach

                    </select>


                    {{-- Project --}}
                    <select id="filter-project" name="project_id" onchange="this.form.submit()" class="filter-select h-8 min-w-[100px] rounded
                               border border-slate-300 bg-white
                               px-2.5 text-[9px] text-slate-600
                               outline-none focus:border-[#0754B8]
                               focus:ring-1 focus:ring-[#0754B8]">

                        <option value="">Project</option>

                        @foreach($projects as $project)

                        <option value="{{ $project->project_id }}" @selected(request('project_id')==$project->
                            project_id)
                            >
                            {{ $project->project_name }}
                        </option>

                        @endforeach

                    </select>

                    {{-- Application --}}
                    <select id="filter-application" name="application_id" onchange="this.form.submit()" class="filter-select h-8 min-w-[110px] rounded
                               border border-slate-300 bg-white
                               px-2.5 text-[9px] text-slate-600
                               outline-none focus:border-[#0754B8]
                               focus:ring-1 focus:ring-[#0754B8]">

                        <option value="">Application</option>

                        @foreach($applications as $application)

                        <option value="{{ $application->application_id }}" @selected(request('application_id')==$application->application_id)>
                            {{ $application->application_name }}
                        </option>

                        @endforeach

                    </select>


                    {{-- SLA --}}
                    <select name="sla" onchange="this.form.submit()" class="filter-select h-8 min-w-[100px] rounded
                               border border-slate-300 bg-white
                               px-2.5 text-[9px] text-slate-600
                               outline-none focus:border-[#0754B8]
                               focus:ring-1 focus:ring-[#0754B8]">

                        <option value="">SLA</option>

                        <option value="breached" @selected(request('sla')==='breached' )>
                            Breached
                        </option>

                        <option value="risk" @selected(request('sla')==='risk' )>
                            At Risk
                        </option>

                        <option value="within" @selected(request('sla')==='within' )>
                            Within SLA
                        </option>

                    </select>


                    {{-- More --}}
                    <button type="button" @click="moreFilters = !moreFilters" class="inline-flex h-8 items-center gap-1
                               rounded border border-slate-300
                               bg-white px-3 text-[9px] font-semibold
                               text-slate-600 hover:bg-slate-50">

                        More

                        <svg class="h-3 w-3 transition-transform" :class="moreFilters ? 'rotate-180' : ''" fill="none"
                            stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
                        </svg>

                    </button>

                </div>


                {{-- More Filters --}}
                <div x-show="moreFilters" x-transition class="mt-3 flex flex-wrap gap-2
                           border-t border-slate-100 pt-3">

                    <select id="filter-state" name="state_id" onchange="this.form.submit()" class="h-8 min-w-[110px] rounded border
                               border-slate-300 bg-white px-2.5
                               text-[9px] text-slate-600">
                        <option value="">State</option>
                        @foreach($states as $st)
                        <option value="{{ $st->state_id }}" @selected((string)request('state_id')===(string)$st->state_id)>
                            {{ $st->state_name }}
                        </option>
                        @endforeach
                    </select>


                    <button type="submit" class="h-8 rounded bg-[#0754B8]
                               px-3 text-[9px] font-semibold text-white
                               hover:bg-[#06479c]">
                        Apply Filters
                    </button>

                </div>

            </form>

        </section>


        {{-- ============================================================
            TABLE CONTAINER
        ============================================================= --}}

        <section class="issue-scroll m-4 overflow-auto rounded border
                        border-slate-200 bg-white shadow-sm">

            {{-- Table Toolbar --}}
            <div class="flex h-12 items-center justify-between
                        border-b border-slate-200 px-4">

                <span class="text-[9px] text-slate-500">

                    Showing

                    <strong class="text-slate-700">
                        {{ $issues->total() }}
                    </strong>

                    issues

                </span>


                <button type="button" @click="exportIssues()" class="inline-flex h-7 items-center gap-1.5 rounded
                           border border-slate-300 bg-slate-50
                           px-2.5 text-[9px] font-semibold
                           text-slate-600 hover:bg-slate-100">

                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0 4-4m-4 4-4-4M5 21h14" />
                    </svg>

                    Export

                </button>

            </div>


            {{-- Table --}}
            <div class="overflow-hidden">

                <table class="w-full min-w-[1000px]">

                    <thead>

                        <tr class="h-9 border-b border-slate-200
                                   bg-[#f8fafc]">

                            <th class="px-3 text-left text-[8px] font-bold
                                       uppercase tracking-wide text-slate-500">
                                ID ↑
                            </th>

                            <th class="px-3 text-left text-[8px] font-bold
                                       uppercase tracking-wide text-slate-500">
                                State
                            </th>

                            <th class="px-3 text-left text-[8px] font-bold
                                       uppercase tracking-wide text-slate-500">
                                Service
                            </th>

                            <th class="px-3 text-left text-[8px] font-bold
                                       uppercase tracking-wide text-slate-500">
                                Project
                            </th>

                            <th class="px-3 text-left text-[8px] font-bold
                                       uppercase tracking-wide text-slate-500">
                                Subject
                            </th>

                            <th class="px-3 text-left text-[8px] font-bold
                                       uppercase tracking-wide text-slate-500">
                                Priority
                            </th>

                            <th class="px-3 text-left text-[8px] font-bold
                                       uppercase tracking-wide text-slate-500">
                                Status
                            </th>

                            <th class="px-3 text-left text-[8px] font-bold
                                       uppercase tracking-wide text-slate-500">
                                SLA ↓
                            </th>

                            <th class="px-3 text-left text-[8px] font-bold
                                       uppercase tracking-wide text-slate-500">
                                Updated ↓
                            </th>

                            <th class="w-[50px] px-3"></th>

                        </tr>

                    </thead>


                    <tbody class="divide-y divide-slate-100">

                        @forelse($issues as $issue)

                        @php

                        $priorityName =
                        $issue->priority?->priority_name
                        ?? 'Medium';

                        $statusName =
                        $issue->status?->status_name
                        ?? 'Open';

                        $priorityLower =
                        strtolower($priorityName);

                        $statusLower =
                        strtolower($statusName);


                        $priorityClass = match(true) {

                        str_contains(
                        $priorityLower,
                        'critical'
                        )
                        => 'bg-red-50 text-red-600 border-red-200',

                        str_contains(
                        $priorityLower,
                        'high'
                        )
                        => 'bg-orange-50 text-orange-600 border-orange-200',

                        str_contains(
                        $priorityLower,
                        'medium'
                        )
                        => 'bg-yellow-50 text-yellow-700 border-yellow-200',

                        str_contains(
                        $priorityLower,
                        'low'
                        )
                        => 'bg-green-50 text-green-600 border-green-200',

                        default
                        => 'bg-slate-50 text-slate-600 border-slate-200',

                        };


                        $statusClass = match(true) {

                        str_contains(
                        $statusLower,
                        'new'
                        )
                        => 'bg-blue-50 text-blue-600',

                        str_contains(
                        $statusLower,
                        'assigned'
                        )
                        => 'bg-purple-50 text-purple-600',

                        str_contains(
                        $statusLower,
                        'progress'
                        )
                        => 'bg-indigo-50 text-indigo-600',

                        str_contains(
                        $statusLower,
                        'pending'
                        )
                        => 'bg-yellow-50 text-yellow-700',

                        str_contains(
                        $statusLower,
                        'resolved'
                        )
                        => 'bg-green-50 text-green-600',

                        str_contains(
                        $statusLower,
                        'closed'
                        )
                        => 'bg-slate-100 text-slate-600',

                        default
                        => 'bg-slate-50 text-slate-600',

                        };

                        @endphp


                        <tr onclick="window.issueTrackerOpen({{ $issue->issue_id }})"
                            class="issue-row h-[43px] cursor-pointer">

                            {{-- ID --}}
                            <td class="whitespace-nowrap px-3">

                                <span class="text-[9px] font-semibold
                                               text-[#0754B8]">
                                    {{ $issue->issue_number }}
                                </span>

                            </td>


                            {{-- State --}}
                            <td class="px-3">

                                <span class="text-[9px] text-slate-600">
                                    {{ $issue->state ?? '-' }}
                                </span>

                            </td>


                            {{-- Service --}}
                            <td class="px-3">

                                <span class="text-[9px] text-slate-700">

                                    {{
                                            $issue->service?->service_name
                                            ?? $issue->service_id
                                            ?? '-'
                                        }}

                                </span>

                            </td>


                            {{-- Project --}}
                            <td class="px-3">

                                <span class="text-[9px] text-slate-700">

                                    {{
                                            $issue->project?->project_name
                                            ?? $issue->project_id
                                            ?? '-'
                                        }}

                                </span>

                            </td>


                            {{-- Subject --}}
                            <td class="max-w-[210px] px-3">

                                <div class="truncate text-[9px]
                                               font-semibold text-slate-700" title="{{ $issue->issue_title }}">
                                    {{ $issue->issue_title }}
                                </div>

                            </td>


                            {{-- Priority --}}
                            <td class="px-3">

                                <span class="inline-flex rounded border
                                               px-2 py-1 text-[8px]
                                               font-bold {{ $priorityClass }}">
                                    {{ $priorityName }}
                                </span>

                            </td>


                            {{-- Status --}}
                            <td class="px-3">

                                <span class="inline-flex rounded px-2 py-1
                                               text-[8px] font-bold
                                               {{ $statusClass }}">
                                    {{ $statusName }}
                                </span>

                            </td>


                            {{-- SLA --}}
                            <td class="px-3">

                                @php
                                $slaLabel =
                                $issue->sla_remaining_label ?? '-';

                                $slaLower =
                                strtolower($slaLabel);
                                @endphp

                                <span class="text-[9px] font-bold
                                        {{
                                            str_contains($slaLower, 'breach')
                                            ? 'text-red-600'
                                            : (
                                                str_contains($slaLower, 'min')
                                                ? 'text-orange-600'
                                                : 'text-green-600'
                                            )
                                        }}">
                                    {{ $slaLabel }}
                                </span>

                            </td>


                            {{-- Updated --}}
                            <td class="whitespace-nowrap px-3">

                                <span class="text-[9px] text-slate-500">
                                    {{ $issue->updated_at?->diffForHumans() ?? '-' }}
                                </span>

                            </td>


                            {{-- Actions --}}
                            <td class="px-3">

                                <button type="button" onclick="
                                            event.stopPropagation();
                                            window.issueTrackerOpen(
                                                {{ $issue->issue_id }}
                                            );
                                        " class="rounded p-1 text-slate-400
                                               transition hover:bg-slate-100
                                               hover:text-[#0754B8]" title="View issue">

                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                        <circle cx="5" cy="12" r="2" />
                                        <circle cx="12" cy="12" r="2" />
                                        <circle cx="19" cy="12" r="2" />
                                    </svg>

                                </button>

                            </td>

                        </tr>


                        @empty

                        <tr>

                            <td colspan="10" class="px-6 py-16 text-center">

                                <div class="text-sm font-semibold
                                               text-slate-500">
                                    No issues found
                                </div>

                                <div class="mt-1 text-[10px]
                                               text-slate-400">
                                    Try changing your search
                                    or filters.
                                </div>

                            </td>

                        </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            {{-- Pagination --}}
            <div class="flex min-h-[52px] items-center justify-between
                        border-t border-slate-200 px-4">

                <span class="text-[9px] text-slate-500">

                    Showing

                    <strong class="text-slate-700">
                        {{ $issues->firstItem() ?? 0 }}
                    </strong>

                    to

                    <strong class="text-slate-700">
                        {{ $issues->lastItem() ?? 0 }}
                    </strong>

                    of

                    <strong class="text-slate-700">
                        {{ $issues->total() }}
                    </strong>

                    issues

                </span>


                <div class="pagination-wrapper">

                    {{ $issues->onEachSide(1)->links() }}

                </div>

            </div>

        </section>


        {{-- ============================================================
            DRAWER OVERLAY
        ============================================================= --}}

        <div x-show="drawerOpen" x-transition:enter="transition-opacity duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" @click="closeDrawer()" class="fixed inset-0 z-40 bg-slate-900/30"
            style="display:none"></div>


        {{-- ============================================================
            RIGHT DRAWER
        ============================================================= --}}

        <aside x-show="drawerOpen" x-transition:enter="transition ease-out duration-250"
            x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full" class="drawer-shadow fixed right-0 top-0 z-50
                   flex h-screen w-[400px] max-w-[95vw]
                   flex-col border-l border-slate-200 bg-white" style="display:none">

            {{-- ========================================================
                DRAWER HEADER
            ========================================================= --}}

            <div class="shrink-0 border-b border-slate-200 px-5 py-4">

                <div class="flex items-start justify-between">

                    <div class="min-w-0">

                        <div x-text="issue.issue_number || 'Loading...'" class="text-[12px] font-bold text-slate-800">
                        </div>

                        <div x-text="issue.issue_title || ''" class="mt-1 truncate text-[10px]
                                   font-medium text-slate-600"></div>

                    </div>


                    <button type="button" @click="closeDrawer()" class="ml-3 flex h-6 w-6 shrink-0
                               items-center justify-center rounded
                               text-lg leading-none text-slate-400
                               hover:bg-slate-100 hover:text-slate-700">
                        ×
                    </button>

                </div>


                <div class="mt-3 flex flex-wrap gap-2">

                    {{-- Priority --}}
                    <span x-text="issue.priority_name || '-'" :class="priorityClass()" class="rounded border px-2.5 py-1
                               text-[8px] font-bold"></span>


                    {{-- SLA --}}
                    <span class="rounded border border-orange-200
                               bg-orange-50 px-2.5 py-1
                               text-[8px] font-bold text-orange-600" x-text="slaText"></span>

                </div>

            </div>


            {{-- ========================================================
                STATUS / OWNER
            ========================================================= --}}

            <div class="shrink-0 border-b border-slate-200 px-5 py-3">

                <div class="grid grid-cols-[110px_1fr] gap-y-2.5">

                    <div class="text-[9px] text-slate-400">
                        Status
                    </div>

                    <div>

                        <span x-text="issue.status_name || '-'" :class="statusClass()" class="inline-flex rounded px-2 py-1
                                   text-[8px] font-bold"></span>

                    </div>


                    <div class="text-[9px] text-slate-400">
                        Current Owner
                    </div>

                    <div x-text="ownerName()" class="text-[9px] font-semibold text-slate-700"></div>

                </div>

            </div>


            {{-- ========================================================
                TABS
            ========================================================= --}}

            <div class="shrink-0 border-b border-slate-200">

                <div class="flex px-2">

                    <button type="button" @click="activeTab = 'details'" :class="
                            activeTab === 'details'
                                ? 'border-[#0754B8] text-[#0754B8]'
                                : 'border-transparent text-slate-500 hover:text-slate-700'
                        " class="border-b-2 px-3 py-3
                               text-[9px] font-bold">
                        Details
                    </button>


                    <button type="button" @click="activeTab = 'updates'" :class="
                            activeTab === 'updates'
                                ? 'border-[#0754B8] text-[#0754B8]'
                                : 'border-transparent text-slate-500 hover:text-slate-700'
                        " class="border-b-2 px-3 py-3
                               text-[9px] font-bold">
                        Updates

                        <span class="ml-1 rounded-full bg-[#0754B8]
                                   px-1.5 py-0.5 text-[7px] text-white">
                            3
                        </span>

                    </button>


                    <button type="button" @click="activeTab = 'history'" :class="
                            activeTab === 'history'
                                ? 'border-[#0754B8] text-[#0754B8]'
                                : 'border-transparent text-slate-500 hover:text-slate-700'
                        " class="border-b-2 px-3 py-3
                               text-[9px] font-bold">
                        History
                    </button>


                    <button type="button" @click="activeTab = 'attachments'" :class="
                            activeTab === 'attachments'
                                ? 'border-[#0754B8] text-[#0754B8]'
                                : 'border-transparent text-slate-500 hover:text-slate-700'
                        " class="border-b-2 px-3 py-3
                               text-[9px] font-bold">
                        Attachments

                        <span class="ml-1 rounded-full bg-[#0754B8]
                                   px-1.5 py-0.5 text-[7px] text-white">
                            2
                        </span>

                    </button>

                </div>

            </div>


            {{-- ========================================================
                DRAWER CONTENT
            ========================================================= --}}

            <div class="drawer-scroll min-h-0 flex-1 overflow-y-auto">

                {{-- Loading --}}
                <template x-if="loading">

                    <div class="flex min-h-[300px]
                                items-center justify-center">

                        <div class="text-center">

                            <svg class="mx-auto h-7 w-7 animate-spin
                                       text-[#0754B8]" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4" />

                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />

                            </svg>

                            <div class="mt-2 text-[9px]
                                       text-slate-400">
                                Loading issue...
                            </div>

                        </div>

                    </div>

                </template>


                {{-- ====================================================
                    DETAILS
                ===================================================== --}}

                <template x-if="!loading && activeTab === 'details'">

                    <div class="px-5 py-5">

                        <h3 class="mb-5 text-[9px] font-bold uppercase
                                   tracking-[0.15em] text-slate-700">
                            Issue Details
                        </h3>


                        <div class="space-y-4">

                            {{-- State --}}
                            <div>

                                <div class="text-[8px] text-slate-400">
                                    State
                                </div>

                                <div x-text="issue.state || '-'" class="mt-1 text-[9px]
                                           font-semibold text-slate-700"></div>

                            </div>


                            {{-- Service --}}
                            <div>

                                <div class="text-[8px] text-slate-400">
                                    Service
                                </div>

                                <div x-text="issue.service_name || issue.service_id || '-'" class="mt-1 text-[9px]
                                           font-semibold text-slate-700"></div>

                            </div>


                            {{-- Project --}}
                            <div>

                                <div class="text-[8px] text-slate-400">
                                    Project
                                </div>

                                <div x-text="issue.project_name || issue.project_id || '-'" class="mt-1 text-[9px]
                                           font-semibold text-slate-700"></div>

                            </div>


                            {{-- Application --}}
                            <div>

                                <div class="text-[8px] text-slate-400">
                                    Application
                                </div>

                                <div x-text="issue.application_name || '-'" class="mt-1 text-[9px]
                                           font-semibold text-slate-700"></div>

                            </div>


                            {{-- Module --}}
                            <div>

                                <div class="text-[8px] text-slate-400">
                                    Module
                                </div>

                                <div x-text="issue.module_name || '-'" class="mt-1 text-[9px]
                                           font-semibold text-slate-700"></div>

                            </div>


                            {{-- Raised --}}
                            <div>

                                <div class="text-[8px] text-slate-400">
                                    Raised At
                                </div>

                                <div x-text="issue.raised_at || '-'" class="mt-1 text-[9px]
                                           font-semibold text-slate-700"></div>

                            </div>


                            {{-- Description --}}
                            <div>

                                <div class="text-[8px] text-slate-400">
                                    Description
                                </div>

                                <div x-text="issue.issue_description || '-'" class="mt-2 whitespace-pre-line
                                           text-[9px] leading-5
                                           text-slate-600"></div>

                            </div>


                            {{-- Resolution --}}
                            <template x-if="issue.resolution_summary">

                                <div>

                                    <div class="text-[8px] text-slate-400">
                                        Resolution
                                    </div>

                                    <div x-text="issue.resolution_summary" class="mt-2 whitespace-pre-line
                                               rounded border
                                               border-green-200
                                               bg-green-50 p-3
                                               text-[9px] leading-5
                                               text-green-700"></div>

                                </div>

                            </template>

                        </div>

                    </div>

                </template>


                {{-- ====================================================
                    UPDATES
                ===================================================== --}}

                <template x-if="!loading && activeTab === 'updates'">

                    <div class="px-5 py-5">

                        <h3 class="mb-5 text-[9px] font-bold uppercase
                                   tracking-[0.15em] text-slate-700">
                            Updates
                        </h3>


                        <div class="space-y-5">

                            {{-- Update 1 --}}
                            <div class="flex gap-3">

                                <div class="mt-1.5 h-2 w-2 shrink-0
                                           rounded-full bg-[#0754B8]"></div>

                                <div>

                                    <div class="text-[9px] font-semibold
                                               text-slate-700">
                                        Issue assigned
                                    </div>

                                    <div class="mt-1 text-[8px]
                                               text-slate-400">
                                        HO IT User
                                    </div>

                                </div>

                            </div>


                            {{-- Update 2 --}}
                            <div class="flex gap-3">

                                <div class="mt-1.5 h-2 w-2 shrink-0
                                           rounded-full bg-indigo-600"></div>

                                <div>

                                    <div class="text-[9px] font-semibold
                                               text-slate-700">
                                        Work started
                                    </div>

                                    <div class="mt-1 text-[8px]
                                               text-slate-400">
                                        System update
                                    </div>

                                </div>

                            </div>


                            {{-- Update 3 --}}
                            <div class="flex gap-3">

                                <div class="mt-1.5 h-2 w-2 shrink-0
                                           rounded-full bg-yellow-500"></div>

                                <div>

                                    <div class="text-[9px] font-semibold
                                               text-slate-700">
                                        SLA monitoring started
                                    </div>

                                    <div class="mt-1 text-[8px]
                                               text-slate-400">
                                        SLA Engine
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </template>


                {{-- ====================================================
                    HISTORY
                ===================================================== --}}

                <template x-if="!loading && activeTab === 'history'">

                    <div class="px-5 py-5">

                        <h3 class="mb-5 text-[9px] font-bold uppercase
                                   tracking-[0.15em] text-slate-700">
                            History
                        </h3>


                        <div class="overflow-hidden rounded border
                                   border-slate-200">

                            <table class="w-full">

                                <thead>

                                    <tr class="bg-slate-50">

                                        <th class="px-3 py-2 text-left
                                                   text-[8px] font-bold
                                                   uppercase text-slate-500">
                                            Date
                                        </th>

                                        <th class="px-3 py-2 text-left
                                                   text-[8px] font-bold
                                                   uppercase text-slate-500">
                                            Action
                                        </th>

                                    </tr>

                                </thead>


                                <tbody>

                                    <tr class="border-t border-slate-100">

                                        <td class="px-3 py-3 text-[8px]
                                                   text-slate-500">
                                            <span x-text="
                                                    issue.raised_at || '-'
                                                "></span>
                                        </td>

                                        <td class="px-3 py-3 text-[8px]
                                                   text-slate-600">
                                            Issue Raised
                                        </td>

                                    </tr>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </template>


                {{-- ====================================================
                    ATTACHMENTS
                ===================================================== --}}

                <template x-if="!loading && activeTab === 'attachments'">

                    <div class="px-5 py-5">

                        <h3 class="mb-5 text-[9px] font-bold uppercase
                                   tracking-[0.15em] text-slate-700">
                            Attachments
                        </h3>


                        <div class="rounded border border-dashed
                                   border-slate-300 p-8 text-center">

                            <svg class="mx-auto h-8 w-8 text-slate-300" fill="none" stroke="currentColor"
                                stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 16V4m0 0L8 8m4-4 4 4M5 20h14" />
                            </svg>

                            <div class="mt-2 text-[9px]
                                       text-slate-400">
                                No attachments loaded.
                            </div>

                        </div>

                    </div>

                </template>

            </div>


            {{-- ========================================================
                ACTION BAR
            ========================================================= --}}

            <div class="shrink-0 border-t border-slate-200
                       bg-white p-4">

                <div class="grid grid-cols-2 gap-2">

                    {{-- Start Work --}}
                    <button type="button" @click="startWork()" :disabled="actionLoading" class="flex h-9 items-center justify-center
                               gap-1.5 rounded border
                               border-blue-300 bg-white
                               px-3 text-[8px] font-bold
                               text-[#0754B8] transition
                               hover:bg-blue-50
                               disabled:cursor-not-allowed
                               disabled:opacity-50">

                        <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8 5v14l11-7z" />
                        </svg>

                        Start Work

                    </button>


                    {{-- Request Information --}}
                    <button type="button" @click="requestInformation()" :disabled="actionLoading" class="flex h-9 items-center justify-center
                               gap-1.5 rounded border
                               border-blue-300 bg-white
                               px-3 text-[8px] font-bold
                               text-[#0754B8] transition
                               hover:bg-blue-50
                               disabled:opacity-50">

                        <span class="flex h-3.5 w-3.5 items-center
                                   justify-center rounded-full
                                   border border-[#0754B8] text-[8px]">
                            ?
                        </span>

                        Request Information

                    </button>


                    {{-- Escalate Vendor --}}
                    <button type="button" @click="escalateVendor()" :disabled="actionLoading" class="flex h-9 items-center justify-center
                               gap-1.5 rounded border
                               border-blue-300 bg-white
                               px-3 text-[8px] font-bold
                               text-[#0754B8] transition
                               hover:bg-blue-50
                               disabled:opacity-50">

                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 19V5m0 0-5 5m5-5 5 5" />
                        </svg>

                        Escalate to Vendor

                    </button>


                    {{-- Resolution --}}
                    <button type="button" @click="submitResolution()" :disabled="actionLoading" class="flex h-9 items-center justify-center
                               gap-1.5 rounded
                               bg-[#0754B8] px-3
                               text-[8px] font-bold text-white
                               transition hover:bg-[#06479c]
                               disabled:opacity-50">

                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" />
                        </svg>

                        Submit Resolution

                    </button>

                </div>

            </div>

        </aside>


        {{-- ============================================================
            TOAST
        ============================================================= --}}

        <div x-show="toast.show" x-transition class="fixed right-5 top-5 z-[100] max-w-sm" style="display:none">

            <div :class="
                    toast.type === 'success'
                        ? 'border-green-200 bg-green-50 text-green-700'
                        : 'border-red-200 bg-red-50 text-red-700'
                " class="rounded border px-4 py-3 shadow-lg">

                <div x-text="toast.message" class="text-[10px] font-semibold"></div>

            </div>

        </div>


        {{-- ============================================================
            REQUEST INFORMATION MODAL
        ============================================================= --}}

        <div x-show="requestModal" x-transition class="fixed inset-0 z-[90] flex items-center
                   justify-center bg-slate-900/40 p-4" style="display:none">

            <div @click.outside="requestModal = false" class="w-full max-w-[430px] overflow-hidden
                       rounded-lg bg-white shadow-2xl">

                <div class="border-b border-slate-200 px-5 py-4">

                    <div class="text-[13px] font-bold text-slate-800">
                        Request Information
                    </div>

                    <div class="mt-1 text-[9px] text-slate-400">
                        Request additional information from the requester.
                    </div>

                </div>


                <div class="p-5">

                    <label class="mb-2 block text-[9px]
                               font-bold text-slate-600">
                        Message
                    </label>

                    <textarea x-model="requestMessage" rows="5"
                        placeholder="Enter information required from requester..." class="w-full resize-none rounded border
                               border-slate-300 p-3 text-[10px]
                               text-slate-700 outline-none
                               placeholder:text-slate-400
                               focus:border-[#0754B8]
                               focus:ring-1 focus:ring-[#0754B8]"></textarea>

                </div>


                <div class="flex justify-end gap-2 border-t
                           border-slate-200 p-4">

                    <button type="button" @click="requestModal = false" class="h-8 rounded border border-slate-300
                               px-4 text-[9px] font-semibold
                               text-slate-600 hover:bg-slate-50">
                        Cancel
                    </button>


                    <button type="button" @click="sendInformationRequest()" :disabled="actionLoading" class="h-8 rounded bg-[#0754B8]
                               px-4 text-[9px] font-semibold
                               text-white hover:bg-[#06479c]
                               disabled:opacity-50">
                        Submit
                    </button>

                </div>

            </div>

        </div>


        {{-- ============================================================
            RESOLUTION MODAL
        ============================================================= --}}

        <div x-show="resolutionModal" x-transition class="fixed inset-0 z-[90] flex items-center
                   justify-center bg-slate-900/40 p-4" style="display:none">

            <div @click.outside="resolutionModal = false" class="w-full max-w-[500px] overflow-hidden
                       rounded-lg bg-white shadow-2xl">

                <div class="border-b border-slate-200 px-5 py-4">

                    <div class="text-[13px] font-bold text-slate-800">
                        Submit Resolution
                    </div>

                    <div class="mt-1 text-[9px] text-slate-400">
                        Provide a summary of how this issue was resolved.
                    </div>

                </div>


                <div class="p-5">

                    <label class="mb-2 block text-[9px]
                               font-bold text-slate-600">
                        Resolution Summary
                    </label>

                    <textarea x-model="resolutionSummary" rows="6" placeholder="Describe how the issue was resolved..."
                        class="w-full resize-none rounded border
                               border-slate-300 p-3 text-[10px]
                               text-slate-700 outline-none
                               placeholder:text-slate-400
                               focus:border-[#0754B8]
                               focus:ring-1 focus:ring-[#0754B8]"></textarea>

                </div>


                <div class="flex justify-end gap-2 border-t
                           border-slate-200 p-4">

                    <button type="button" @click="resolutionModal = false" class="h-8 rounded border border-slate-300
                               px-4 text-[9px] font-semibold
                               text-slate-600 hover:bg-slate-50">
                        Cancel
                    </button>


                    <button type="button" @click="sendResolution()" :disabled="actionLoading" class="h-8 rounded bg-[#0754B8]
                               px-4 text-[9px] font-semibold
                               text-white hover:bg-[#06479c]
                               disabled:opacity-50">
                        Submit Resolution
                    </button>

                </div>

            </div>

        </div>

    </div>


    {{-- ================================================================
        ALPINE
    ================================================================= --}}

    <script>
    function issueTracker() {

        return {

            /* -------------------------------------------------------
             | State
             ------------------------------------------------------- */

            drawerOpen: false,

            loading: false,

            actionLoading: false,

            activeTab: 'details',

            moreFilters: false,

            issue: {},

            issueId: null,

            slaText: 'SLA calculating...',

            requestModal: false,

            requestMessage: '',

            resolutionModal: false,

            resolutionSummary: '',

            toast: {
                show: false,
                type: 'success',
                message: '',
            },


            /* -------------------------------------------------------
             | Open Issue
             ------------------------------------------------------- */

            openIssue(id) {

                if (!id) {
                    return;
                }

                this.issueId = id;

                this.drawerOpen = true;

                this.loading = true;

                this.activeTab = 'details';

                this.issue = {};

                this.slaText = 'SLA calculating...';


                fetch(
                        `{{ url('/issues') }}/${id}`, {
                            method: 'GET',

                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            }
                        }
                    )

                    .then(response => {

                        if (!response.ok) {

                            throw new Error(
                                'Unable to load issue.'
                            );

                        }

                        return response.json();

                    })

                    .then(data => {

                        if (!data.success) {

                            throw new Error(
                                data.message ||
                                'Unable to load issue.'
                            );

                        }

                        this.issue = data.issue || {};

                        this.calculateSla();

                    })

                    .catch(error => {

                        this.showToast(
                            error.message ||
                            'Unable to load issue.',
                            'error'
                        );

                        this.closeDrawer();

                    })

                    .finally(() => {

                        this.loading = false;

                    });

            },


            /* -------------------------------------------------------
             | Close Drawer
             ------------------------------------------------------- */

            closeDrawer() {

                this.drawerOpen = false;

                this.issue = {};

                this.issueId = null;

                this.loading = false;

            },


            /* -------------------------------------------------------
             | Priority CSS
             ------------------------------------------------------- */

            priorityClass() {

                const priority = (
                    this.issue.priority_name || ''
                ).toLowerCase();


                if (priority.includes('critical')) {

                    return 'bg-red-50 text-red-600 border-red-200';

                }


                if (priority.includes('high')) {

                    return 'bg-orange-50 text-orange-600 border-orange-200';

                }


                if (priority.includes('medium')) {

                    return 'bg-yellow-50 text-yellow-700 border-yellow-200';

                }


                if (priority.includes('low')) {

                    return 'bg-green-50 text-green-600 border-green-200';

                }


                return 'bg-slate-50 text-slate-600 border-slate-200';

            },


            /* -------------------------------------------------------
             | Status CSS
             ------------------------------------------------------- */

            statusClass() {

                const status = (
                    this.issue.status_name || ''
                ).toLowerCase();


                if (status.includes('new')) {

                    return 'bg-blue-50 text-blue-600';

                }


                if (status.includes('assigned')) {

                    return 'bg-purple-50 text-purple-600';

                }


                if (status.includes('progress')) {

                    return 'bg-indigo-50 text-indigo-600';

                }


                if (status.includes('pending')) {

                    return 'bg-yellow-50 text-yellow-700';

                }


                if (status.includes('resolved')) {

                    return 'bg-green-50 text-green-600';

                }


                if (status.includes('closed')) {

                    return 'bg-slate-100 text-slate-600';

                }


                return 'bg-blue-50 text-blue-600';

            },


            /* -------------------------------------------------------
             | Owner
             ------------------------------------------------------- */

            ownerName() {

                return (
                    this.issue.owner_name ||
                    this.issue.current_owner_name ||
                    this.issue.current_owner_organisation_name ||
                    'Unassigned'
                );

            },


            /* -------------------------------------------------------
             | SLA
             ------------------------------------------------------- */

            calculateSla() {

                if (
                    this.issue.sla_remaining_label
                ) {

                    this.slaText =
                        `SLA: ${this.issue.sla_remaining_label}`;

                    return;

                }


                if (
                    this.issue.sla_text
                ) {

                    this.slaText =
                        this.issue.sla_text;

                    return;

                }


                this.slaText =
                    'SLA monitoring active';

            },


            /* -------------------------------------------------------
             | Start Work
             ------------------------------------------------------- */

            startWork() {

                if (
                    !this.issueId ||
                    this.actionLoading
                ) {
                    return;
                }


                this.actionLoading = true;


                this.postAction(
                        `/issues/${this.issueId}/start-work`, {}
                    )

                    .then(() => {

                        this.issue.status_name =
                            'In Progress';

                    })

                    .catch(() => {

                        // Error already handled by postAction.

                    })

                    .finally(() => {

                        this.actionLoading = false;

                    });

            },


            /* -------------------------------------------------------
             | Request Information
             ------------------------------------------------------- */

            requestInformation() {

                if (this.actionLoading) {
                    return;
                }

                this.requestMessage = '';

                this.requestModal = true;

            },


            sendInformationRequest() {

                if (
                    !this.issueId ||
                    this.actionLoading
                ) {
                    return;
                }


                if (
                    !this.requestMessage.trim()
                ) {

                    this.showToast(
                        'Please enter a message.',
                        'error'
                    );

                    return;

                }


                this.actionLoading = true;


                this.postAction(
                        `/issues/${this.issueId}/request-information`, {
                            message: this.requestMessage
                        }
                    )

                    .then(() => {

                        this.requestModal = false;

                        this.issue.status_name =
                            'Pending';

                        this.requestMessage = '';

                    })

                    .catch(() => {

                        // Error handled by postAction.

                    })

                    .finally(() => {

                        this.actionLoading = false;

                    });

            },


            /* -------------------------------------------------------
             | Escalate Vendor
             ------------------------------------------------------- */

            escalateVendor() {

                if (
                    !this.issueId ||
                    this.actionLoading
                ) {
                    return;
                }


                if (
                    !confirm(
                        'Are you sure you want to escalate this issue to the vendor?'
                    )
                ) {
                    return;
                }


                this.actionLoading = true;


                this.postAction(
                        `/issues/${this.issueId}/escalate-vendor`, {}
                    )

                    .then(() => {

                        this.showToast(
                            'Issue escalated to vendor.',
                            'success'
                        );

                    })

                    .catch(() => {

                        // Error handled by postAction.

                    })

                    .finally(() => {

                        this.actionLoading = false;

                    });

            },


            /* -------------------------------------------------------
             | Resolution
             ------------------------------------------------------- */

            submitResolution() {

                if (
                    !this.issueId ||
                    this.actionLoading
                ) {
                    return;
                }


                this.resolutionSummary = '';

                this.resolutionModal = true;

            },


            sendResolution() {

                if (
                    !this.issueId ||
                    this.actionLoading
                ) {
                    return;
                }


                if (
                    !this.resolutionSummary.trim()
                ) {

                    this.showToast(
                        'Please enter resolution summary.',
                        'error'
                    );

                    return;

                }


                this.actionLoading = true;


                this.postAction(
                        `/issues/${this.issueId}/submit-resolution`, {
                            resolution_summary: this.resolutionSummary
                        }
                    )

                    .then(() => {

                        this.resolutionModal = false;

                        this.issue.status_name =
                            'Resolved';

                        this.issue.resolution_summary =
                            this.resolutionSummary;

                        this.resolutionSummary = '';

                    })

                    .catch(() => {

                        // Error handled by postAction.

                    })

                    .finally(() => {

                        this.actionLoading = false;

                    });

            },


            /* -------------------------------------------------------
             | POST Action
             ------------------------------------------------------- */

            postAction(url, payload) {

                const csrfToken =
                    document
                    .querySelector(
                        'meta[name="csrf-token"]'
                    )
                    ?.getAttribute('content');


                return fetch(
                        url, {
                            method: 'POST',

                            headers: {

                                'Content-Type': 'application/json',

                                'Accept': 'application/json',

                                'X-Requested-With': 'XMLHttpRequest',

                                'X-CSRF-TOKEN': csrfToken || '',

                            },

                            body: JSON.stringify(payload)

                        }
                    )

                    .then(async response => {

                        let data = {};

                        try {

                            data =
                                await response.json();

                        } catch (error) {

                            data = {};

                        }


                        if (
                            !response.ok ||
                            !data.success
                        ) {

                            throw new Error(
                                data.message ||
                                'Operation failed.'
                            );

                        }


                        return data;

                    })

                    .then(data => {

                        this.showToast(
                            data.message ||
                            'Operation completed successfully.',
                            'success'
                        );


                        return data;

                    })

                    .catch(error => {

                        this.showToast(
                            error.message ||
                            'Operation failed.',
                            'error'
                        );


                        throw error;

                    });

            },


            /* -------------------------------------------------------
             | Toast
             ------------------------------------------------------- */

            showToast(
                message,
                type = 'success'
            ) {

                this.toast = {

                    show: true,

                    type: type,

                    message: message ||
                        'Operation completed.'

                };


                setTimeout(() => {

                    this.toast.show = false;

                }, 3500);

            },


            /* -------------------------------------------------------
             | Export
             ------------------------------------------------------- */

            exportIssues() {

                const params =
                    new URLSearchParams(
                        window.location.search
                    );


                const exportUrl =
                    `{{ route('issues.index') }}?${params.toString()}&export=1`;


                window.location.href =
                    exportUrl;

            },

        };

    }


    /* ================================================================
       GLOBAL DRAWER OPENER
    ================================================================= */

    window.issueTrackerOpen = function(id) {

        const root =
            document.getElementById(
                'issue-tracker'
            );


        if (
            !root ||
            !root._x_dataStack
        ) {

            console.error(
                'Issue tracker Alpine component not found.'
            );

            return;

        }


        const component =
            root._x_dataStack[0];


        if (
            component &&
            typeof component.openIssue === 'function'
        ) {

            component.openIssue(id);

        }

    };


    /* ================================================================
       ESC KEY
    ================================================================= */

    document.addEventListener(
        'keydown',
        function(event) {

            if (event.key !== 'Escape') {
                return;
            }


            const root =
                document.getElementById(
                    'issue-tracker'
                );


            if (
                root &&
                root._x_dataStack
            ) {

                const component =
                    root._x_dataStack[0];


                if (component) {

                    if (component.requestModal) {

                        component.requestModal = false;

                        return;

                    }


                    if (component.resolutionModal) {

                        component.resolutionModal = false;

                        return;

                    }


                    if (component.drawerOpen) {

                        component.closeDrawer();

                    }

                }

            }

        }
    );
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const stateSel = document.getElementById('filter-state');
        const projSel = document.getElementById('filter-project');
        const appSel = document.getElementById('filter-application');

        async function loadProjectsForState(stateId) {
            if (!projSel) return;
            projSel.innerHTML = '<option value="">Loading...</option>';
            if (appSel) appSel.innerHTML = '<option value="">Application</option>';
            if (!stateId) {
                projSel.innerHTML = '<option value="">Project</option>';
                return;
            }
            try {
                const url = new URL('{{ route('issues.ajax.projects') }}', window.location.origin);
                url.searchParams.set('state_id', stateId);
                const res = await fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                if (!res.ok) throw new Error('Failed to load projects');
                const data = await res.json();
                projSel.innerHTML = '<option value="">Project</option>';
                data.forEach(function (p) {
                    const opt = document.createElement('option');
                    opt.value = p.project_id;
                    opt.textContent = p.project_name;
                    projSel.appendChild(opt);
                });
            } catch (err) {
                console.error(err);
                projSel.innerHTML = '<option value="">Project</option>';
            }
        }

        async function loadApplicationsForProject(projectId) {
            if (!appSel) return;
            appSel.innerHTML = '<option value="">Loading...</option>';
            if (!projectId) {
                appSel.innerHTML = '<option value="">Application</option>';
                return;
            }
            try {
                const url = new URL('{{ route('issues.ajax.applications') }}', window.location.origin);
                url.searchParams.set('project_id', projectId);
                // include state if present to let vendor mapping narrow results
                if (stateSel && stateSel.value) url.searchParams.set('state_id', stateSel.value);
                const res = await fetch(url.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                if (!res.ok) throw new Error('Failed to load applications');
                const data = await res.json();
                appSel.innerHTML = '<option value="">Application</option>';
                data.forEach(function (a) {
                    const opt = document.createElement('option');
                    opt.value = a.application_id;
                    opt.textContent = a.application_name;
                    appSel.appendChild(opt);
                });
            } catch (err) {
                console.error(err);
                appSel.innerHTML = '<option value="">Application</option>';
            }
        }

        if (stateSel) {
            stateSel.addEventListener('change', function (e) {
                const sid = e.target.value;
                loadProjectsForState(sid);
            });
            // load projects on initial page load if a state is already selected
            if (stateSel.value) loadProjectsForState(stateSel.value);
        }

        if (projSel) {
            projSel.addEventListener('change', function (e) {
                const pid = e.target.value;
                loadApplicationsForProject(pid);
            });
            // load applications on initial page load if a project is already selected
            if (projSel.value) loadApplicationsForProject(projSel.value);
        }
    });
    </script>


</x-app-layout>