@extends('layouts.app')

@section('title', 'Requirement Dashboard')

@section('page-title', 'Requirement Dashboard')

@section('content')

<div class="mx-auto max-w-7xl space-y-6">

    {{-- Header --}}
    <div class="flex items-center justify-between">

        <div>
            <h1 class="text-2xl font-bold text-slate-900">
                Requirement Overview
            </h1>

            <p class="mt-1 text-sm text-slate-500">
                Monitor outsourced requirements and delivery progress.
            </p>
        </div>

        <a
            href="{{ route('requirements.create') }}"
            class="rounded-xl bg-blue-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-blue-800">

            + New Requirement

        </a>

    </div>


    {{-- Filters --}}
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

        <form
            method="GET"
            class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-5">

            <select
                name="state"
                class="rounded-xl border-slate-300 text-sm">

                <option value="">
                    All States
                </option>

                @foreach($states as $state)
                    <option
                        value="{{ $state->state_id }}"
                        @selected(request('state') == $state->state_id)>

                        {{ $state->state_name }}

                    </option>
                @endforeach

            </select>


            <select
                name="status"
                class="rounded-xl border-slate-300 text-sm">

                <option value="">
                    All Status
                </option>

                <option value="BRD Raised">
                    BRD Raised
                </option>

                <option value="In Progress">
                    In Progress
                </option>

                <option value="UAT In Progress">
                    UAT In Progress
                </option>

                <option value="Moved to Production">
                    Moved to Production
                </option>

                <option value="Closed">
                    Closed
                </option>

            </select>


            <input
                type="date"
                name="from_date"
                value="{{ request('from_date') }}"
                class="rounded-xl border-slate-300 text-sm">


            <input
                type="date"
                name="to_date"
                value="{{ request('to_date') }}"
                class="rounded-xl border-slate-300 text-sm">


            <div class="flex gap-2">

                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search..."
                    class="min-w-0 flex-1 rounded-xl border-slate-300 text-sm">

                <button
                    class="rounded-xl bg-slate-900 px-4 text-sm font-semibold text-white">

                    Apply

                </button>

            </div>

        </form>

    </div>


    {{-- KPIs --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">

        @foreach([
            ['Total Requirements', $stats['total'], 'blue'],
            ['In Progress', $stats['in_progress'], 'indigo'],
            ['UAT Stage', $stats['uat'], 'violet'],
            ['Production', $stats['production'], 'green'],
            ['Open Clarifications', $stats['clarifications'], 'amber'],
        ] as [$label, $value, $color])

            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">

                <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                    {{ $label }}
                </p>

                <p class="mt-2 text-3xl font-bold text-slate-900">
                    {{ $value }}
                </p>

            </div>

        @endforeach

    </div>


    {{-- Table --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

        <div class="border-b border-slate-200 px-6 py-4">

            <h2 class="font-semibold text-slate-900">
                Requirements
            </h2>

        </div>


        <div class="overflow-x-auto">

            <table class="w-full text-left">

                <thead class="bg-slate-50">

                    <tr>

                        <th class="px-6 py-3 text-xs uppercase text-slate-500">
                            Requirement
                        </th>

                        <th class="px-4 py-3 text-xs uppercase text-slate-500">
                            State
                        </th>

                        <th class="px-4 py-3 text-xs uppercase text-slate-500">
                            Project
                        </th>

                        <th class="px-4 py-3 text-xs uppercase text-slate-500">
                            Status
                        </th>

                        <th class="px-4 py-3 text-xs uppercase text-slate-500">
                            Man Days
                        </th>

                        <th class="px-6 py-3 text-right text-xs uppercase text-slate-500">
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-100">

                    @forelse($requirements as $requirement)

                        <tr class="hover:bg-slate-50">

                            <td class="px-6 py-4">

                                <a
                                    href="{{ route('requirements.show', $requirement) }}"
                                    class="font-semibold text-blue-900">

                                    {{ $requirement->requirement_no }}

                                </a>

                                <p class="text-sm text-slate-700">
                                    {{ $requirement->title }}
                                </p>

                            </td>


                            <td class="px-4 py-4 text-sm">
                                {{ $requirement->state->name ?? '-' }}
                            </td>


                            <td class="px-4 py-4 text-sm">
                                {{ $requirement->project->name ?? '-' }}
                            </td>


                            <td class="px-4 py-4">

                                @include(
                                    'components.status-badge',
                                    ['status' => $requirement->status]
                                )

                            </td>


                            <td class="px-4 py-4 text-sm">
                                {{ $requirement->man_days ?? '-' }}
                            </td>


                            <td class="px-6 py-4 text-right">

                                <a
                                    href="{{ route('requirements.show', $requirement) }}"
                                    class="text-sm font-semibold text-blue-800">

                                    View

                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td
                                colspan="6"
                                class="px-6 py-12 text-center text-sm text-slate-500">

                                No requirements found.

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        <div class="border-t border-slate-200 px-6 py-4">

            {{ $requirements->links() }}

        </div>

    </div>

</div>

@endsection