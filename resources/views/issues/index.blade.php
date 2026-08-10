<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Issues') }}
        </h2>
    </x-slot>

    <div class="bg-white rounded-xl shadow border border-gray-200">

        {{-- Header --}}
        <div class="p-5 border-b">

            <div class="flex items-center justify-between">

                <h2 class="text-xl font-semibold text-gray-800">
                    Issues
                </h2>

                <a href="{{ route('issues.create') }}"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">

                    <i class="fa fa-plus mr-2"></i> Raise Issue

                </a>

            </div>

            {{-- Search --}}
            <div class="flex items-center gap-3 mt-5">

                <input type="text" name="search" placeholder="Issue ID / Subject"
                    class="w-80 rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">

                <button class="px-5 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">

                    Search

                </button>

                <button class="text-blue-600">

                    Clear

                </button>

                <div class="ml-auto">

                    <button class="px-4 py-2 border rounded-lg">

                        Export

                    </button>

                </div>

            </div>

            {{-- Filters --}}
            <div class="flex flex-wrap gap-3 mt-5">

                <select class="rounded-lg border-gray-300">
                    <option>Status</option>
                </select>

                <select class="rounded-lg border-gray-300">
                    <option>Priority</option>
                </select>

                <select class="rounded-lg border-gray-300">
                    <option>Service</option>
                </select>

                <select class="rounded-lg border-gray-300">
                    <option>Project</option>
                </select>

                <select class="rounded-lg border-gray-300">
                    <option>SLA</option>
                </select>

                <button class="px-4 rounded-lg border">
                    More
                </button>

            </div>

        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">

            <table class="min-w-full divide-y divide-gray-200">

                <thead class="bg-gray-50">

                    <tr>

                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase">
                            ID
                        </th>

                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase">
                            State
                        </th>

                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase">
                            Service
                        </th>

                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase">
                            Project
                        </th>

                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase">
                            Subject
                        </th>

                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase">
                            Priority
                        </th>

                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase">
                            Status
                        </th>

                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase">
                            SLA
                        </th>

                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase">
                            Updated
                        </th>

                        <th class="px-5 py-3 text-center text-xs font-semibold uppercase">
                            Actions
                        </th>

                    </tr>

                </thead>

                <tbody class="divide-y divide-gray-100 bg-white">

                    @forelse($issues as $issue)

                    <tr class="hover:bg-blue-50">

                        <td class="px-5 py-4">

                            <a href="{{ route('issues.show',$issue->issue_id) }}" class="text-blue-600 font-semibold">

                                {{ $issue->issue_number }}

                            </a>

                        </td>

                        <td class="px-5">

                            {{ optional($issue->state)->state_name }}

                        </td>

                        <td class="px-5">

                            {{ optional($issue->service)->service_name }}

                        </td>

                        <td class="px-5">

                            {{ optional($issue->project)->project_name }}

                        </td>

                        <td class="px-5">

                            {{ Str::limit($issue->subject,35) }}

                        </td>

                        <td class="text-center">

                            @php
                            $priorityName = optional($issue->priority)->priority_name;
                            @endphp

                            @switch($priorityName)

                            @case('Critical')
                            <span class="px-2 py-1 rounded bg-red-100 text-red-600">
                                Critical
                            </span>
                            @break

                            @case('High')
                            <span class="px-2 py-1 rounded bg-orange-100 text-orange-600">
                                High
                            </span>
                            @break

                            @case('Medium')
                            <span class="px-2 py-1 rounded bg-yellow-100 text-yellow-600">
                                Medium
                            </span>
                            @break

                            @case('Low')
                            <span class="px-2 py-1 rounded bg-green-100 text-green-600">
                                Low
                            </span>
                            @break

                            @default
                            <span class="px-2 py-1 rounded bg-gray-100">
                                N/A
                            </span>

                            @endswitch

                            <span
                                class="px-3 py-1 rounded-full text-xs font-semibold {{ $priorityClass[$issue->priority->priority_name] ?? 'bg-gray-100' }}">

                                {{ optional($issue->priority)->priority_name }}

                            </span>

                        </td>

                        <td class="text-center">

                            @php
                            $status = $issue->status;
                            @endphp


                            @switch($status)

                            @case('Open')
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                Open
                            </span>
                            @break

                            @case('Assigned')
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">
                                Assigned
                            </span>
                            @break

                            @case('In Progress')
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-700">
                                In Progress
                            </span>
                            @break

                            @case('Pending')
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-700">
                                Pending
                            </span>
                            @break

                            @case('Resolved')
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-700">
                                Resolved
                            </span>
                            @break

                            @case('Closed')
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-200 text-gray-700">
                                Closed
                            </span>
                            @break

                            @default
                            <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                N/A
                            </span>

                            @endswitch

                            <span
                                class="px-3 py-1 rounded-full text-xs font-semibold {{ $statusClass[$issue->status] ?? 'bg-gray-100' }}">

                                {{ $issue->status }}

                            </span>

                        </td>

                        <td class="text-center">

                            <span class="text-red-600 font-semibold">

                                {{ $issue->sla ?? '--' }}

                            </span>

                        </td>

                        <td class="text-center text-sm text-gray-500">

                            {{ $issue->updated_at?->diffForHumans() }}

                        </td>

                        <td class="text-center">

                            <div class="dropdown inline-block">

                                <button class="text-gray-600 hover:text-blue-600">

                                    <i class="fa-solid fa-ellipsis"></i>

                                </button>

                            </div>

                        </td>

                    </tr>

                    @empty

                    <tr>

                        <td colspan="10" class="py-12 text-center text-gray-500">

                            No issues found.

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-between p-5 border-t bg-gray-50">

            <div class="text-sm text-gray-500">

                Showing

                {{ $issues->firstItem() ?? 0 }}

                to

                {{ $issues->lastItem() ?? 0 }}

                of

                {{ $issues->total() }}

                issues

            </div>

            {{ $issues->links() }}

        </div>

    </div>
</x-app-layout>