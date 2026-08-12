<x-app-layout>

    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ $title ?? 'Working Schedules' }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

                {{-- Header / Search / Add --}}
                <div class="border-b border-slate-200 bg-slate-50 px-5 py-5">

                    <div class="grid gap-4 md:grid-cols-[1fr_auto_auto] md:items-center">

                        <div>
                            <p class="text-sm text-slate-600">
                                Manage working schedules, shifts, timings and working days.
                            </p>
                        </div>

                        {{-- Search --}}
                        <div class="flex items-center gap-3">

                            <div
                                class="flex items-center justify-end rounded-2xl border border-slate-200 bg-white px-3 py-2 shadow-sm">

                                <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z">
                                    </path>
                                </svg>

                                <input id="schedule-search" type="text" placeholder="Search"
                                    class="ml-2 w-36 bg-transparent text-sm text-slate-900 placeholder:text-slate-400 outline-none" />

                            </div>

                        </div>

                        {{-- Add --}}
                        <div class="flex justify-end">

                            <a href="{{ route('working-schedules.create') }}"
                                class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">

                                Add Working Schedule

                            </a>

                        </div>

                    </div>

                </div>

                @if(session('success') || session('error'))
                <div class="px-5 py-4" id="state-message-container">
                    <div id="state-message"
                        class="relative rounded-2xl px-4 py-3 text-sm font-semibold shadow-sm {{ session('success') ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                        <span>{{ session('success') ?? session('error') }}</span>
                        <button type="button" onclick="closeStateMessage()"
                            class="absolute right-3 top-3 rounded-full bg-white/80 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-white focus:outline-none">
                            Close
                        </button>
                    </div>
                </div>
                @endif


                {{-- Table --}}
                <div class="overflow-x-auto">

                    <div class="max-h-[520px] overflow-auto">

                        <table class="min-w-full divide-y divide-slate-200">

                            <thead class="sticky top-0 z-10 bg-purple-100">

                                <tr>

                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">
                                        #
                                    </th>

                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">
                                        Day
                                    </th>

                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">
                                        Schedule
                                    </th>

                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">
                                        Shift
                                    </th>

                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">
                                        Start
                                    </th>

                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">
                                        Break
                                    </th>

                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">
                                        End
                                    </th>

                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">
                                        Working Day
                                    </th>

                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">
                                        24 Hours
                                    </th>

                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">
                                        Effective From
                                    </th>

                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">
                                        Effective To
                                    </th>

                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">
                                        Status
                                    </th>

                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">
                                        Actions
                                    </th>

                                </tr>

                            </thead>


                            <tbody class="divide-y divide-slate-200 bg-white">

                                @php
                                $start = $schedules->firstItem() ?? 1;
                                @endphp

                                @forelse ($schedules as $i => $schedule)

                                <tr class="schedule-row">

                                    {{-- # --}}
                                    <td class="px-5 py-3 text-sm font-semibold text-slate-900">
                                        {{ $start + $i }}
                                    </td>


                                    {{-- Day --}}
                                    <td class="px-5 py-3 text-sm font-semibold text-slate-900 whitespace-nowrap">
                                        {{ $schedule->day_of_week ?? '-' }}
                                    </td>


                                    {{-- Schedule --}}
                                    <td class="px-5 py-3 text-sm text-slate-900">
                                        {{ $schedule->schedule_name ?? '-' }}
                                    </td>


                                    {{-- Shift --}}
                                    <td class="px-5 py-3 text-sm text-slate-600">

                                        {{ $schedule->shift_name ?? '-' }}

                                        @if ($schedule->shift_no)
                                        <span class="text-xs text-slate-400">
                                            (#{{ $schedule->shift_no }})
                                        </span>
                                        @endif

                                    </td>


                                    {{-- Start --}}
                                    <td class="px-5 py-3 text-sm text-slate-600 whitespace-nowrap">
                                        {{ $schedule->start_time ?? '-' }}
                                    </td>


                                    {{-- Break --}}
                                    <td class="px-5 py-3 text-sm text-slate-600 whitespace-nowrap">

                                        @if ($schedule->break_start && $schedule->break_end)

                                        {{ $schedule->break_start }}
                                        -
                                        {{ $schedule->break_end }}

                                        @else

                                        -

                                        @endif

                                    </td>


                                    {{-- End --}}
                                    <td class="px-5 py-3 text-sm text-slate-600 whitespace-nowrap">
                                        {{ $schedule->end_time ?? '-' }}
                                    </td>


                                    {{-- Working Day --}}
                                    <td class="px-5 py-3 text-sm">

                                        @if ($schedule->is_working_day)

                                        <span
                                            class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                            Yes
                                        </span>

                                        @else

                                        <span
                                            class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">
                                            No
                                        </span>

                                        @endif

                                    </td>


                                    {{-- 24 Hours --}}
                                    <td class="px-5 py-3 text-sm">

                                        @if ($schedule->is_24_hours)

                                        <span
                                            class="rounded-full bg-sky-50 px-2.5 py-1 text-xs font-semibold text-sky-700">
                                            Yes
                                        </span>

                                        @else

                                        <span class="text-slate-400">
                                            No
                                        </span>

                                        @endif

                                    </td>


                                    {{-- Effective From --}}
                                    <td class="px-5 py-3 text-sm text-slate-600 whitespace-nowrap">

                                        {{ optional($schedule->effective_from)->format('d-m-Y') ?? '-' }}

                                    </td>


                                    {{-- Effective To --}}
                                    <td class="px-5 py-3 text-sm text-slate-600 whitespace-nowrap">

                                        {{ optional($schedule->effective_to)->format('d-m-Y') ?? '-' }}

                                    </td>


                                    {{-- Status --}}
                                    <td class="px-5 py-3 text-sm">

                                        @if ($schedule->is_active)

                                        <span
                                            class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                            Active
                                        </span>

                                        @else

                                        <span
                                            class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-semibold text-amber-700">
                                            Inactive
                                        </span>

                                        @endif

                                    </td>


                                    {{-- Actions --}}
                                    <td class="px-5 py-3 text-sm">

                                        <div class="flex items-center gap-2">

                                            {{-- Edit --}}
                                            <a href="{{ route('working-schedules.edit', $schedule->schedule_id) }}"
                                                class="rounded-lg bg-slate-900 px-2.5 py-1.5 text-xs font-semibold text-white hover:bg-slate-800">

                                                Edit

                                            </a>


                                            {{-- Toggle Status --}}
                                            <form method="POST"
                                                action="{{ route('working-schedules.toggle-status', $schedule->schedule_id) }}"
                                                class="inline">

                                                @csrf
                                                @method('PATCH')

                                                <button type="submit"
                                                    class="rounded-lg bg-sky-50 px-2.5 py-1.5 text-xs font-semibold text-sky-700 hover:bg-sky-100">
                                                    {{ $schedule->is_active ? 'Disable' : 'Activate' }}
                                                </button>

                                            </form>


                                            {{-- Delete --}}
                                            <form method="POST"
                                                action="{{ route('working-schedules.destroy', $schedule->schedule_id) }}"
                                                class="inline"
                                                onsubmit="return confirm('Are you sure you want to delete this working schedule?');">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                    class="rounded-lg bg-rose-50 px-2.5 py-1.5 text-xs font-semibold text-rose-700 hover:bg-rose-100">
                                                    Delete
                                                </button>

                                            </form>

                                        </div>

                                    </td>

                                </tr>

                                @empty

                                <tr class="empty-row">

                                    <td colspan="13" class="px-5 py-8 text-center text-sm text-slate-500">

                                        No working schedules found.

                                    </td>

                                </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>


                {{-- Pagination --}}
                @if ($schedules->hasPages())

                <div class="border-t border-slate-200 px-5 py-4">

                    {{ $schedules->links() }}

                </div>

                @endif

            </div>

        </div>
    </div>


    {{-- Search JavaScript --}}
    @push('scripts')

    <script>
    document.addEventListener('DOMContentLoaded', function() {

        const searchInput = document.getElementById('schedule-search');

        if (!searchInput) {
            return;
        }

        const rows = document.querySelectorAll('.schedule-row');

        searchInput.addEventListener('input', function() {

            const query = this.value.trim().toLowerCase();

            rows.forEach(function(row) {

                const text = row.textContent.toLowerCase();

                row.classList.toggle(
                    'hidden',
                    query !== '' && !text.includes(query)
                );

            });

        });

    });
    </script>

    @endpush

</x-app-layout>