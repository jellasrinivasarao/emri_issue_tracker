<x-app-layout>

    <style>
    /* @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@300;400;500;600;700&display=swap');

    [x-cloak] {
        display: none !important;
    }

    .app-light-font,
    .app-light-font * {
        font-family: 'Manrope', 'Segoe UI', sans-serif;
        color: #0f172a;
    }

    .app-light-font h1,
    .app-light-font h2,
    .app-light-font h3,
    .app-light-font h4 {
        font-family: 'Manrope', 'Segoe UI', sans-serif;
        font-weight: 700;
        letter-spacing: -0.02em;
        color: #0f172a;
    }

    .app-light-font .soft-muted {
        color: #64748b;
    }

    .app-light-font .card-strong {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        box-shadow: 0 16px 36px -18px rgba(15, 23, 42, 0.24);
    }

    .app-light-font .control-pill {
        border: 1px solid #dbe3ee;
        background: #f8fafc;
        color: #334155;
    }

    .app-light-font .page-shell {
        background: linear-gradient(180deg, #f8fbff 0%, #f4f7fb 100%);
        border: 1px solid #e2e8f0;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.9);
    } */

    .issue-scroll::-webkit-scrollbar {
        width: 5px;
        height: 5px;
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

    .drawer-scroll::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }
    </style>

    <div x-data="issueTracker()" x-cloak class="app-light-font space-y-4">

        <section class="page-shell card-strong rounded-[24px] border border-slate-200 p-4 sm:p-6">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <p class="soft-muted text-[11px] font-semibold uppercase tracking-[0.24em]">Operations</p>
                    <h1 class="mt-1 text-2xl leading-tight">Issue Tracker</h1>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('issues.index') }}"
                        class="control-pill inline-flex items-center rounded-full px-3 py-1.5 text-[11px] font-semibold transition hover:bg-slate-100">
                        Refresh
                    </a>
                    <button type="button"
                        class="inline-flex items-center gap-2 rounded-full bg-slate-900 px-3 py-1.5 text-[11px] font-semibold text-white transition hover:bg-slate-800 shadow-[0_10px_20px_-12px_rgba(15,23,42,0.9)]">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="1.75"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 3v12m0 0l4-4m-4 4l-4-4M5 21h14" />
                        </svg>
                        Export
                    </button>
                </div>
            </div>

            <form method="GET" action="{{ route('issues.index') }}" class="mt-5">

                <div class="flex flex-col gap-3 lg:flex-row lg:items-center">
                    <div class="relative w-full max-w-[360px]">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Issue ID / Subject" class="h-10 w-full rounded-full border border-slate-300
                            bg-white px-4 pr-24 text-[12px]
                            text-slate-700 outline-none
                            placeholder:text-slate-400
                            focus:border-blue-500
                            focus:ring-1 focus:ring-blue-500">

                        <button type="submit"
                            class="absolute right-1 top-1 flex h-8 items-center gap-1 rounded-full bg-[#0754B8] px-3 text-[10px] font-semibold text-white">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <circle cx="11" cy="11" r="7" />
                                <path stroke-linecap="round" stroke-width="2" d="M20 20l-4-4" />
                            </svg>
                            Search
                        </button>
                    </div>

                    <a href="{{ route('issues.index') }}" class="text-[11px] font-semibold text-blue-600">
                        Clear
                    </a>
                </div>

                <div class="mt-4 flex flex-wrap items-center gap-2">
                    <span class="soft-muted mr-1 text-[10px] font-semibold uppercase tracking-[0.2em]">
                        Filters:
                    </span>

                    <select name="status_id" onchange="this.form.submit()"
                        class="h-8 rounded-full border border-slate-300 bg-white px-3 text-[10px] text-slate-600 focus:border-blue-500">
                        <option value="">Status</option>
                        @foreach($statuses as $status)
                        <option value="{{ $status->status_id }}" @selected( request('status_id')==$status->status_id )>
                            {{ $status->status_name }}
                        </option>
                        @endforeach
                    </select>

                    <select name="priority_id" onchange="this.form.submit()"
                        class="h-8 rounded-full border border-slate-300 bg-white px-3 text-[10px] text-slate-600 focus:border-blue-500">
                        <option value="">Priority</option>
                        @foreach($priorities as $priority)
                        <option value="{{ $priority->priority_id }}" @selected( request('priority_id')==$priority->
                            priority_id )>
                            {{ $priority->priority_name }}
                        </option>
                        @endforeach
                    </select>

                    <select name="service_id" onchange="this.form.submit()"
                        class="h-8 rounded-full border border-slate-300 bg-white px-3 text-[10px] text-slate-600 focus:border-blue-500">
                        <option value="">Service</option>
                        @foreach($services as $service)
                        <option value="{{ $service->service_id }}" @selected( request('service_id')==$service->
                            service_id )>
                            {{ $service->service_name }}
                        </option>
                        @endforeach
                    </select>

                    <select name="project_id" onchange="this.form.submit()"
                        class="h-8 rounded-full border border-slate-300 bg-white px-3 text-[10px] text-slate-600 focus:border-blue-500">
                        <option value="">Project</option>
                        @foreach($projects as $project)
                        <option value="{{ $project->project_id }}" @selected( request('project_id')==$project->
                            project_id )>
                            {{ $project->project_name }}
                        </option>
                        @endforeach
                    </select>

                    <select name="sla"
                        class="h-8 rounded-full border border-slate-300 bg-white px-3 text-[10px] text-slate-600">
                        <option value="">SLA</option>
                        <option value="breached">Breached</option>
                        <option value="risk">At Risk</option>
                        <option value="within">Within SLA</option>
                    </select>

                    <button type="button"
                        class="flex h-8 items-center gap-1 rounded-full border border-slate-300 px-3 text-[10px] font-semibold text-slate-600">
                        More
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 9l6 6 6-6" />
                        </svg>
                    </button>
                </div>
            </form>
        </section>

        {{-- ========================================================
            TABLE
        ========================================================= --}}

        <section
            class="issue-scroll overflow-auto rounded-[24px] border border-slate-200 bg-white px-4 py-4 shadow-[0_14px_34px_-18px_rgba(15,23,42,0.3)] sm:px-6">


            {{-- Toolbar --}}
            <div class="flex items-center justify-between py-4">


                <span class="text-[10px] text-slate-500">

                    Showing

                    <strong class="text-slate-700">
                        {{ $issues->total() }}
                    </strong>

                    issues

                </span>


                <button type="button" class="flex items-center gap-2 rounded-full
                        border border-slate-300 bg-slate-50
                        px-3 py-1.5 text-[10px]
                        font-semibold text-slate-700 shadow-sm">

                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">

                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 3v12m0 0l4-4m-4 4l-4-4M5 21h14" />

                    </svg>

                    Export

                </button>

            </div>


            {{-- Table --}}
            <div class="overflow-hidden rounded-md border border-slate-200">


                <table class="w-full min-w-[950px]">


                    <thead class="bg-slate-50">

                        <tr class="border-b border-slate-200">


                            @foreach([
                            'ID ↑',
                            'State',
                            'Service',
                            'Project',
                            'Subject',
                            'Priority',
                            'Status',
                            'SLA ↓',
                            'Updated ↓',
                            'Actions'
                            ] as $heading)

                            <th class="px-3 py-3 text-left
                                        text-[9px] font-bold uppercase
                                        tracking-wide text-slate-500">

                                {{ $heading }}

                            </th>

                            @endforeach

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


                        <tr onclick="window.issueTrackerOpen({{ $issue->issue_id }})" class="cursor-pointer transition
                                    hover:bg-blue-50">


                            {{-- ID --}}
                            <td class="whitespace-nowrap px-3 py-3
                                        text-[10px] font-semibold
                                        text-blue-600">

                                {{ $issue->issue_number }}

                            </td>


                            {{-- State --}}
                            <td class="px-3 py-3 text-[10px]
                                        text-slate-600">

                                {{ $issue->state ?? '-' }}

                            </td>


                            {{-- Service --}}
                            <td class="px-3 py-3 text-[10px]
                                        text-slate-700">

                                {{ $issue->service?->service_name
                                            ?? $issue->service_id
                                            ?? '-' }}

                            </td>


                            {{-- Project --}}
                            <td class="px-3 py-3 text-[10px]
                                        text-slate-700">

                                {{ $issue->project?->project_name
                                            ?? $issue->project_id
                                            ?? '-' }}

                            </td>


                            {{-- Subject --}}
                            <td class="max-w-[190px] px-3 py-3">


                                <div class="truncate text-[10px]
                                            font-medium text-slate-700">

                                    {{ $issue->issue_title }}

                                </div>

                            </td>


                            {{-- Priority --}}
                            <td class="px-3 py-3">


                                <span class="inline-flex rounded-md
                                            border px-2 py-1
                                            text-[9px] font-semibold
                                            {{ $priorityClass }}">

                                    {{ $priorityName }}

                                </span>

                            </td>


                            {{-- Status --}}
                            <td class="px-3 py-3">


                                <span class="inline-flex rounded-md
                                            px-2 py-1 text-[9px]
                                            font-semibold
                                            {{ $statusClass }}">

                                    {{ $statusName }}

                                </span>

                            </td>


                            {{-- SLA --}}
                            <td class="px-3 py-3">

                                <span class="text-[10px] font-semibold
                                            text-green-600">

                                    {{ $issue->sla_remaining_label ?? '-' }}

                                </span>

                            </td>


                            {{-- Updated --}}
                            <td class="whitespace-nowrap
                                        px-3 py-3 text-[10px]
                                        text-slate-500">

                                {{ $issue->updated_at?->diffForHumans()
                                            ?? '-' }}

                            </td>


                            {{-- Actions --}}
                            <td class="px-3 py-3">

                                <button type="button" onclick="
                                                event.stopPropagation();
                                                window.issueTrackerOpen(
                                                    {{ $issue->issue_id }}
                                                );
                                            " class="rounded-md p-1 text-slate-400
                                            hover:bg-slate-100
                                            hover:text-blue-600">

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
            <div class="flex items-center justify-between py-4">


                <span class="text-[10px] text-slate-500">

                    Showing

                    <strong>
                        {{ $issues->firstItem() ?? 0 }}
                    </strong>

                    to

                    <strong>
                        {{ $issues->lastItem() ?? 0 }}
                    </strong>

                    of

                    <strong>
                        {{ $issues->total() }}
                    </strong>

                    issues

                </span>


                <div>

                    {{ $issues->onEachSide(1)->links() }}

                </div>

            </div>

        </section>


        {{-- ============================================================
            DRAWER OVERLAY
        ============================================================= --}}

        <div x-show="drawerOpen" x-transition.opacity @click="closeDrawer()" class="fixed inset-0 z-40 bg-slate-900/30"
            style="display:none">
        </div>


        {{-- ============================================================
            RIGHT DRAWER
        ============================================================= --}}

        <aside x-show="drawerOpen" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full" class="fixed right-0 top-0 z-50 flex h-screen
            w-[430px] max-w-[95vw] flex-col bg-white
            shadow-2xl" style="display:none">


            {{-- ========================================================
                DRAWER HEADER
            ========================================================= --}}

            <div class="shrink-0 border-b border-slate-200 px-5 py-4">


                <div class="flex items-start justify-between">


                    <div class="min-w-0">


                        <div x-text="issue.issue_number || 'Loading...'" class="text-[13px] font-bold text-slate-800">

                        </div>


                        <div x-text="issue.issue_title || ''" class="mt-1 truncate text-[11px]
                            font-medium text-slate-600">

                        </div>

                    </div>


                    <button type="button" @click="closeDrawer()" class="ml-3 rounded-md p-1 text-xl
                        leading-none text-slate-400
                        hover:bg-slate-100 hover:text-slate-700">

                        ×

                    </button>

                </div>


                {{-- Priority / SLA --}}
                <div class="mt-4 flex flex-wrap gap-2">


                    <span x-text="issue.priority_name || '-'" :class="priorityClass()" class="rounded-md border px-2.5 py-1
                        text-[9px] font-bold">

                    </span>


                    <span class="rounded-md border border-orange-200
                        bg-orange-50 px-2.5 py-1 text-[9px]
                        font-semibold text-orange-600">

                        <span x-text="slaText">
                            SLA calculating...
                        </span>

                    </span>

                </div>

            </div>


            {{-- ========================================================
                OWNER
            ========================================================= --}}

            <div class="shrink-0 border-b border-slate-200 px-5 py-4">


                <div class="grid grid-cols-2 gap-y-3">


                    <div class="text-[9px] text-slate-400">

                        Status

                    </div>


                    <div>

                        <span x-text="issue.status_name || '-'" :class="statusClass()" class="inline-flex rounded-md px-2 py-1
                            text-[9px] font-semibold">

                        </span>

                    </div>


                    <div class="text-[9px] text-slate-400">

                        Current Owner

                    </div>


                    <div class="text-[10px] font-semibold text-slate-700">

                        <span x-text="ownerName()">

                        </span>

                    </div>

                </div>

            </div>


            {{-- ========================================================
                TABS
            ========================================================= --}}

            <div class="shrink-0 border-b border-slate-200">


                <div class="flex">


                    <button type="button" @click="activeTab='details'" :class="
                            activeTab === 'details'
                            ? 'border-blue-600 text-blue-600'
                            : 'border-transparent text-slate-500'
                        " class="border-b-2 px-4 py-3 text-[10px]
                        font-semibold">

                        Details

                    </button>


                    <button type="button" @click="activeTab='updates'" :class="
                            activeTab === 'updates'
                            ? 'border-blue-600 text-blue-600'
                            : 'border-transparent text-slate-500'
                        " class="border-b-2 px-4 py-3 text-[10px]
                        font-semibold">

                        Updates

                        <span class="ml-1 rounded-full bg-blue-600
                            px-1.5 py-0.5 text-[8px] text-white">

                            3

                        </span>

                    </button>


                    <button type="button" @click="activeTab='history'" :class="
                            activeTab === 'history'
                            ? 'border-blue-600 text-blue-600'
                            : 'border-transparent text-slate-500'
                        " class="border-b-2 px-4 py-3 text-[10px]
                        font-semibold">

                        History

                    </button>


                    <button type="button" @click="activeTab='attachments'" :class="
                            activeTab === 'attachments'
                            ? 'border-blue-600 text-blue-600'
                            : 'border-transparent text-slate-500'
                        " class="border-b-2 px-4 py-3 text-[10px]
                        font-semibold">

                        Attachments

                        <span class="ml-1 rounded-full bg-blue-600
                            px-1.5 py-0.5 text-[8px] text-white">

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

                    <div class="flex h-full items-center justify-center">

                        <div class="text-center">

                            <svg class="mx-auto h-7 w-7 animate-spin
                                text-blue-600" fill="none" viewBox="0 0 24 24">

                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4" />

                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />

                            </svg>

                            <div class="mt-2 text-[10px] text-slate-400">

                                Loading issue...

                            </div>

                        </div>

                    </div>

                </template>


                {{-- Details --}}
                <template x-if="!loading && activeTab === 'details'">

                    <div class="px-5 py-5">


                        <h3 class="mb-5 text-[10px] font-bold uppercase
                            tracking-wide text-slate-700">

                            Issue Details

                        </h3>


                        <div class="space-y-5">


                            {{-- State --}}
                            <div>

                                <div class="text-[9px] text-slate-400">

                                    State

                                </div>

                                <div x-text="issue.state || '-'" class="mt-1 text-[10px]
                                    font-semibold text-slate-700">

                                </div>

                            </div>


                            {{-- Service --}}
                            <div>

                                <div class="text-[9px] text-slate-400">

                                    Service

                                </div>

                                <div x-text="issue.service_name || '-'" class="mt-1 text-[10px]
                                    font-semibold text-slate-700">

                                </div>

                            </div>


                            {{-- Project --}}
                            <div>

                                <div class="text-[9px] text-slate-400">

                                    Project

                                </div>

                                <div x-text="issue.project_name || '-'" class="mt-1 text-[10px]
                                    font-semibold text-slate-700">

                                </div>

                            </div>


                            {{-- Application --}}
                            <div>

                                <div class="text-[9px] text-slate-400">

                                    Application

                                </div>

                                <div x-text="issue.application_name || '-'" class="mt-1 text-[10px]
                                    font-semibold text-slate-700">

                                </div>

                            </div>


                            {{-- Module --}}
                            <div>

                                <div class="text-[9px] text-slate-400">

                                    Module

                                </div>

                                <div x-text="issue.module_name || '-'" class="mt-1 text-[10px]
                                    font-semibold text-slate-700">

                                </div>

                            </div>


                            {{-- Raised --}}
                            <div>

                                <div class="text-[9px] text-slate-400">

                                    Raised At

                                </div>

                                <div x-text="issue.raised_at || '-'" class="mt-1 text-[10px]
                                    font-semibold text-slate-700">

                                </div>

                            </div>


                            {{-- Description --}}
                            <div>

                                <div class="text-[9px] text-slate-400">

                                    Description

                                </div>

                                <div x-text="issue.issue_description || '-'" class="mt-2 whitespace-pre-line
                                    text-[10px] leading-5 text-slate-600">

                                </div>

                            </div>


                            {{-- Resolution --}}
                            <template x-if="issue.resolution_summary">

                                <div>

                                    <div class="text-[9px] text-slate-400">

                                        Resolution

                                    </div>

                                    <div x-text="issue.resolution_summary" class="mt-2 whitespace-pre-line
                                        rounded-md bg-green-50 p-3
                                        text-[10px] leading-5
                                        text-green-700">

                                    </div>

                                </div>

                            </template>

                        </div>

                    </div>

                </template>


                {{-- Updates --}}
                <template x-if="!loading && activeTab === 'updates'">

                    <div class="px-5 py-5">

                        <h3 class="mb-5 text-[10px] font-bold uppercase
                            tracking-wide text-slate-700">

                            Updates

                        </h3>


                        <div class="space-y-5">


                            <div class="flex gap-3">


                                <div class="mt-1 h-2 w-2 shrink-0
                                    rounded-full bg-blue-600">

                                </div>


                                <div>

                                    <div class="text-[10px] font-semibold
                                        text-slate-700">

                                        Issue assigned

                                    </div>

                                    <div class="mt-1 text-[9px]
                                        text-slate-400">

                                        HO IT User

                                    </div>

                                </div>

                            </div>


                            <div class="flex gap-3">


                                <div class="mt-1 h-2 w-2 shrink-0
                                    rounded-full bg-indigo-600">

                                </div>


                                <div>

                                    <div class="text-[10px] font-semibold
                                        text-slate-700">

                                        Work started

                                    </div>

                                    <div class="mt-1 text-[9px]
                                        text-slate-400">

                                        System update

                                    </div>

                                </div>

                            </div>


                            <div class="flex gap-3">


                                <div class="mt-1 h-2 w-2 shrink-0
                                    rounded-full bg-yellow-500">

                                </div>


                                <div>

                                    <div class="text-[10px] font-semibold
                                        text-slate-700">

                                        SLA monitoring started

                                    </div>

                                    <div class="mt-1 text-[9px]
                                        text-slate-400">

                                        SLA Engine

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                </template>


                {{-- History --}}
                <template x-if="!loading && activeTab === 'history'">

                    <div class="px-5 py-5">

                        <h3 class="mb-5 text-[10px] font-bold uppercase
                            tracking-wide text-slate-700">

                            History

                        </h3>


                        <div class="overflow-hidden rounded-md
                            border border-slate-200">

                            <table class="w-full">

                                <thead class="bg-slate-50">

                                    <tr>

                                        <th class="px-3 py-2 text-left
                                            text-[8px] uppercase
                                            text-slate-500">

                                            Date

                                        </th>

                                        <th class="px-3 py-2 text-left
                                            text-[8px] uppercase
                                            text-slate-500">

                                            Action

                                        </th>

                                    </tr>

                                </thead>


                                <tbody>

                                    <tr class="border-t border-slate-100">

                                        <td class="px-3 py-3 text-[9px]
                                            text-slate-500">

                                            <span x-text="issue.raised_at || '-'">

                                            </span>

                                        </td>

                                        <td class="px-3 py-3 text-[9px]
                                            text-slate-600">

                                            Issue Raised

                                        </td>

                                    </tr>

                                </tbody>

                            </table>

                        </div>

                    </div>

                </template>


                {{-- Attachments --}}
                <template x-if="!loading && activeTab === 'attachments'">

                    <div class="px-5 py-5">

                        <h3 class="mb-5 text-[10px] font-bold uppercase
                            tracking-wide text-slate-700">

                            Attachments

                        </h3>


                        <div class="rounded-md border border-dashed
                            border-slate-300 p-8 text-center">

                            <svg class="mx-auto h-8 w-8 text-slate-300" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">

                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 16V4m0 0L8 8m4-4l4 4M5 20h14" />

                            </svg>


                            <div class="mt-2 text-[10px]
                                text-slate-400">

                                No attachments loaded.

                            </div>

                        </div>

                    </div>

                </template>

            </div>


            {{-- ========================================================
                ACTION BUTTONS
            ========================================================= --}}

            <div class="shrink-0 border-t border-slate-200 bg-white p-4">


                <div class="grid grid-cols-2 gap-2">


                    {{-- Start Work --}}
                    <button type="button" @click="startWork()" :disabled="actionLoading" class="flex items-center justify-center
                        gap-1.5 rounded-md border border-blue-300
                        px-3 py-2.5 text-[9px] font-semibold
                        text-blue-600 transition
                        hover:bg-blue-50
                        disabled:cursor-not-allowed
                        disabled:opacity-50">

                        <span>▶</span>

                        Start Work

                    </button>


                    {{-- Request Information --}}
                    <button type="button" @click="requestInformation()" :disabled="actionLoading" class="flex items-center justify-center
                        gap-1.5 rounded-md border border-blue-300
                        px-3 py-2.5 text-[9px] font-semibold
                        text-blue-600 transition
                        hover:bg-blue-50
                        disabled:opacity-50">

                        <span>?</span>

                        Request Information

                    </button>


                    {{-- Escalate --}}
                    <button type="button" @click="escalateVendor()" :disabled="actionLoading" class="flex items-center justify-center
                        gap-1.5 rounded-md border border-blue-300
                        px-3 py-2.5 text-[9px] font-semibold
                        text-blue-600 transition
                        hover:bg-blue-50
                        disabled:opacity-50">

                        <span>↑</span>

                        Escalate to Vendor

                    </button>


                    {{-- Resolution --}}
                    <button type="button" @click="submitResolution()" :disabled="actionLoading" class="flex items-center justify-center
                        gap-1.5 rounded-md bg-[#0754B8]
                        px-3 py-2.5 text-[9px] font-semibold
                        text-white transition hover:bg-blue-700
                        disabled:opacity-50">

                        <span>✓</span>

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
                " class="rounded-lg border px-4 py-3
                shadow-lg">


                <div x-text="toast.message" class="text-[11px] font-semibold">

                </div>

            </div>

        </div>


        {{-- ============================================================
            REQUEST INFORMATION MODAL
        ============================================================= --}}

        <div x-show="requestModal" class="fixed inset-0 z-[90] flex items-center
            justify-center bg-slate-900/40" style="display:none">


            <div @click.outside="requestModal=false" class="w-[430px] rounded-xl bg-white shadow-2xl">


                <div class="border-b border-slate-200 px-5 py-4">

                    <div class="text-sm font-bold text-slate-800">

                        Request Information

                    </div>

                </div>


                <div class="p-5">


                    <label class="mb-2 block text-[10px]
                        font-semibold text-slate-600">

                        Message

                    </label>


                    <textarea x-model="requestMessage" rows="5"
                        placeholder="Enter information required from requester..." class="w-full rounded-md border border-slate-300
                        p-3 text-[11px] outline-none
                        focus:border-blue-500
                        focus:ring-1 focus:ring-blue-500">

                    </textarea>

                </div>


                <div class="flex justify-end gap-2
                    border-t border-slate-200 p-4">


                    <button type="button" @click="requestModal=false" class="rounded-md border border-slate-300
                        px-4 py-2 text-[10px] font-semibold
                        text-slate-600">

                        Cancel

                    </button>


                    <button type="button" @click="sendInformationRequest()" class="rounded-md bg-[#0754B8]
                        px-4 py-2 text-[10px] font-semibold
                        text-white">

                        Submit

                    </button>

                </div>

            </div>

        </div>


        {{-- ============================================================
            RESOLUTION MODAL
        ============================================================= --}}

        <div x-show="resolutionModal" class="fixed inset-0 z-[90] flex items-center
            justify-center bg-slate-900/40" style="display:none">


            <div @click.outside="resolutionModal=false" class="w-[500px] rounded-xl bg-white shadow-2xl">


                <div class="border-b border-slate-200 px-5 py-4">

                    <div class="text-sm font-bold text-slate-800">

                        Submit Resolution

                    </div>

                </div>


                <div class="p-5">


                    <label class="mb-2 block text-[10px]
                        font-semibold text-slate-600">

                        Resolution Summary

                    </label>


                    <textarea x-model="resolutionSummary" rows="6" placeholder="Describe how the issue was resolved..."
                        class="w-full rounded-md border border-slate-300
                        p-3 text-[11px] outline-none
                        focus:border-blue-500
                        focus:ring-1 focus:ring-blue-500">

                    </textarea>

                </div>


                <div class="flex justify-end gap-2
                    border-t border-slate-200 p-4">


                    <button type="button" @click="resolutionModal=false" class="rounded-md border border-slate-300
                        px-4 py-2 text-[10px] font-semibold
                        text-slate-600">

                        Cancel

                    </button>


                    <button type="button" @click="sendResolution()" class="rounded-md bg-[#0754B8]
                        px-4 py-2 text-[10px] font-semibold
                        text-white">

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

            drawerOpen: false,

            loading: false,

            actionLoading: false,

            activeTab: 'details',

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


            /*
            |--------------------------------------------------------------------------
            | Open Drawer
            |--------------------------------------------------------------------------
            */

            openIssue(id) {
                this.issueId = id;

                this.drawerOpen = true;

                this.loading = true;

                this.activeTab = 'details';

                this.issue = {};

                this.slaText =
                    'SLA calculating...';


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

                        this.issue =
                            data.issue;

                        this.calculateSla();

                    })

                    .catch(error => {

                        this.showToast(
                            error.message,
                            'error'
                        );

                        this.closeDrawer();

                    })

                    .finally(() => {

                        this.loading = false;

                    });
            },


            /*
            |--------------------------------------------------------------------------
            | Close
            |--------------------------------------------------------------------------
            */

            closeDrawer() {
                this.drawerOpen = false;

                this.issue = {};

                this.issueId = null;
            },


            /*
            |--------------------------------------------------------------------------
            | Priority CSS
            |--------------------------------------------------------------------------
            */

            priorityClass() {
                const priority =
                    (
                        this.issue.priority_name ||
                        ''
                    ).toLowerCase();


                if (
                    priority.includes('critical')
                ) {

                    return 'bg-red-50 text-red-600 border-red-200';

                }


                if (
                    priority.includes('high')
                ) {

                    return 'bg-orange-50 text-orange-600 border-orange-200';

                }


                if (
                    priority.includes('medium')
                ) {

                    return 'bg-yellow-50 text-yellow-700 border-yellow-200';

                }


                if (
                    priority.includes('low')
                ) {

                    return 'bg-green-50 text-green-600 border-green-200';

                }


                return 'bg-slate-50 text-slate-600 border-slate-200';
            },


            /*
            |--------------------------------------------------------------------------
            | Status CSS
            |--------------------------------------------------------------------------
            */

            statusClass() {
                const status =
                    (
                        this.issue.status_name ||
                        ''
                    ).toLowerCase();


                if (
                    status.includes('progress')
                ) {

                    return 'bg-indigo-50 text-indigo-600';

                }


                if (
                    status.includes('pending')
                ) {

                    return 'bg-yellow-50 text-yellow-700';

                }


                if (
                    status.includes('resolved')
                ) {

                    return 'bg-green-50 text-green-600';

                }


                if (
                    status.includes('closed')
                ) {

                    return 'bg-slate-100 text-slate-600';

                }


                if (
                    status.includes('assigned')
                ) {

                    return 'bg-purple-50 text-purple-600';

                }


                return 'bg-blue-50 text-blue-600';
            },


            /*
            |--------------------------------------------------------------------------
            | Owner
            |--------------------------------------------------------------------------
            */

            ownerName() {
                return (
                    this.issue.owner_name ||
                    this.issue.current_owner_organisation_name ||
                    'Unassigned'
                );
            },


            /*
            |--------------------------------------------------------------------------
            | SLA
            |--------------------------------------------------------------------------
            */

            calculateSla() {
                /*
                |--------------------------------------------------------------------------
                | If your API returns remaining SLA
                |--------------------------------------------------------------------------
                */

                if (
                    this.issue.sla_remaining_label
                ) {

                    this.slaText =
                        `SLA: ${this.issue.sla_remaining_label}`;

                    return;
                }


                /*
                |--------------------------------------------------------------------------
                | Fallback
                |--------------------------------------------------------------------------
                */

                this.slaText =
                    'SLA monitoring active';
            },


            /*
            |--------------------------------------------------------------------------
            | Start Work
            |--------------------------------------------------------------------------
            */

            startWork() {
                if (!this.issueId) {
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

                    .finally(() => {

                        this.actionLoading = false;

                    });
            },


            /*
            |--------------------------------------------------------------------------
            | Request Information
            |--------------------------------------------------------------------------
            */

            requestInformation() {
                this.requestMessage = '';

                this.requestModal = true;
            },


            sendInformationRequest() {
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

                        `/issues/${this.issueId}/request-information`,

                        {
                            message: this.requestMessage
                        }

                    )

                    .then(() => {

                        this.requestModal = false;

                        this.issue.status_name =
                            'Pending';

                    })

                    .finally(() => {

                        this.actionLoading = false;

                    });
            },


            /*
            |--------------------------------------------------------------------------
            | Vendor
            |--------------------------------------------------------------------------
            */

            escalateVendor() {
                if (!this.issueId) {
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

                    .finally(() => {

                        this.actionLoading = false;

                    });
            },


            /*
            |--------------------------------------------------------------------------
            | Resolution
            |--------------------------------------------------------------------------
            */

            submitResolution() {
                this.resolutionSummary = '';

                this.resolutionModal = true;
            },


            sendResolution() {
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

                        `/issues/${this.issueId}/submit-resolution`,

                        {
                            resolution_summary: this.resolutionSummary
                        }

                    )

                    .then(() => {

                        this.resolutionModal = false;

                        this.issue.status_name =
                            'Resolved';

                        this.issue.resolution_summary =
                            this.resolutionSummary;

                    })

                    .finally(() => {

                        this.actionLoading = false;

                    });
            },


            /*
            |--------------------------------------------------------------------------
            | POST
            |--------------------------------------------------------------------------
            */

            postAction(
                url,
                payload
            ) {
                return fetch(

                        url,

                        {

                            method: 'POST',

                            headers: {

                                'Content-Type': 'application/json',

                                'Accept': 'application/json',

                                'X-Requested-With': 'XMLHttpRequest',

                                'X-CSRF-TOKEN': document
                                    .querySelector(
                                        'meta[name="csrf-token"]'
                                    )
                                    .getAttribute('content'),

                            },

                            body: JSON.stringify(
                                payload
                            )

                        }

                    )

                    .then(response => {

                        return response
                            .json()
                            .then(data => {

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

                            });

                    })

                    .then(data => {

                        this.showToast(
                            data.message,
                            'success'
                        );

                        return data;

                    })

                    .catch(error => {

                        this.showToast(
                            error.message,
                            'error'
                        );

                        throw error;

                    });
            },


            /*
            |--------------------------------------------------------------------------
            | Toast
            |--------------------------------------------------------------------------
            */

            showToast(
                message,
                type = 'success'
            ) {
                this.toast = {

                    show: true,

                    type: type,

                    message: message,

                };


                setTimeout(() => {

                    this.toast.show = false;

                }, 3500);
            }

        };
    }


    /*
    |--------------------------------------------------------------------------
    | Global Open Function
    |--------------------------------------------------------------------------
    */

    window.issueTrackerOpen = function(id) {
        const root =
            document.querySelector(
                '[x-data="issueTracker()"]'
            );


        if (
            root &&
            root._x_dataStack
        ) {

            root._x_dataStack[0]
                .openIssue(id);

        }
    };
    </script>

    <!-- <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"> -->
    </script>


</x-app-layout>