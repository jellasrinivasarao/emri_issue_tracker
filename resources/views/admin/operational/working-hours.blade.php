<x-app-layout>

    {{-- =========================================================
        HEADER
    ========================================================== --}}
    <x-slot name="header">

        <div class="flex flex-col gap-1">

            <h2 class="text-xl font-bold tracking-tight text-slate-800">
                Working Calendars
            </h2>

            <p class="text-sm text-slate-500">
                Manage working calendars, schedules and holidays.
            </p>

        </div>

    </x-slot>


    {{-- =========================================================
        DEFAULT VARIABLES
    ========================================================== --}}
    @php
    $calendars = $calendars ?? collect();
    $organisations = $organisations ?? collect();
    $states = $states ?? collect();
    @endphp


    {{-- =========================================================
        PAGE
    ========================================================== --}}
    <div class="min-h-screen bg-slate-50 py-6">

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            {{-- =================================================
                MAIN CARD
            ================================================== --}}
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">


                {{-- =================================================
                    TOOLBAR
                ================================================== --}}
                <div class="border-b border-slate-200 bg-white px-5 py-5">

                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

                        {{-- TITLE --}}
                        <div>

                            <h3 class="text-base font-semibold text-slate-800">
                                Calendar Configuration
                            </h3>

                            <p class="mt-1 text-sm text-slate-500">
                                Configure business calendars used for schedules, holidays and SLA calculations.
                            </p>

                        </div>


                        {{-- ACTIONS --}}
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">

                            {{-- SEARCH --}}
                            <div class="flex h-10 w-full items-center rounded-xl border
                                       border-slate-300 bg-white px-3 transition
                                       focus-within:border-indigo-500
                                       focus-within:ring-2
                                       focus-within:ring-indigo-500/20
                                       sm:w-72">

                                <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z" />
                                </svg>

                                <input id="calendar-search" type="search" autocomplete="off"
                                    placeholder="Search calendars..." class="ml-2 w-full border-0 bg-transparent
                                           text-sm text-slate-700 outline-none
                                           placeholder:text-slate-400 focus:ring-0">

                            </div>


                            {{-- ADD --}}
                            <button type="button" onclick="openCalendarModal()" class="inline-flex h-10 items-center justify-center
                                       gap-2 rounded-xl bg-indigo-600 px-4
                                       text-sm font-semibold text-white shadow-sm
                                       transition hover:bg-indigo-700
                                       focus:outline-none focus:ring-2
                                       focus:ring-indigo-500 focus:ring-offset-2">

                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14" />
                                </svg>

                                Add Calendar

                            </button>

                        </div>

                    </div>

                </div>


                {{-- =================================================
                    FLASH MESSAGE
                ================================================== --}}
                @if(session('success') || session('error'))

                <div id="calendar-message-container" class="px-5 pt-5">

                    @if(session('success'))

                    <div id="calendar-message" class="relative flex items-start gap-3
                                       rounded-xl border border-emerald-200
                                       bg-emerald-50 px-4 py-3 text-sm
                                       text-emerald-800">

                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" fill="none" stroke="currentColor"
                            stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" />
                        </svg>

                        <span class="pr-8 font-medium">
                            {{ session('success') }}
                        </span>

                        <button type="button" onclick="closeCalendarMessage()" class="absolute right-3 top-3
                                           rounded-lg p-1 text-emerald-600
                                           hover:bg-emerald-100">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>

                    </div>

                    @else

                    <div id="calendar-message" class="relative flex items-start gap-3
                                       rounded-xl border border-rose-200
                                       bg-rose-50 px-4 py-3 text-sm
                                       text-rose-800">

                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-rose-600" fill="none" stroke="currentColor"
                            stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v4m0 4h.01M10.3 3.6 2.9 17a2 2 0 0 0 1.75 3h14.7a2 2 0 0 0 1.75-3L13.7 3.6a2 2 0 0 0-3.4 0Z" />
                        </svg>

                        <span class="pr-8 font-medium">
                            {{ session('error') }}
                        </span>

                        <button type="button" onclick="closeCalendarMessage()" class="absolute right-3 top-3
                                           rounded-lg p-1 text-rose-600
                                           hover:bg-rose-100">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>

                    </div>

                    @endif

                </div>

                @endif


                {{-- =================================================
                    TABLE
                ================================================== --}}
                <div class="overflow-x-auto">

                    <div class="max-h-[540px] overflow-auto">

                        <table class="min-w-full divide-y divide-slate-200">

                            {{-- HEADER --}}
                            <thead class="sticky top-0 z-10 bg-slate-50">

                                <tr>

                                    <th class="px-6 py-3 text-left text-xs font-semibold
                                               uppercase tracking-wider text-slate-500">
                                        #
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-semibold
                                               uppercase tracking-wider text-slate-500">
                                        Code
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-semibold
                                               uppercase tracking-wider text-slate-500">
                                        Calendar Name
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-semibold
                                               uppercase tracking-wider text-slate-500">
                                        Timezone
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-semibold
                                               uppercase tracking-wider text-slate-500">
                                        Organisation
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-semibold
                                               uppercase tracking-wider text-slate-500">
                                        Status
                                    </th>

                                    <th class="px-6 py-3 text-left text-xs font-semibold
                                               uppercase tracking-wider text-slate-500">
                                        State
                                    </th>

                                    <th class="px-6 py-3 text-right text-xs font-semibold
                                               uppercase tracking-wider text-slate-500">
                                        Actions
                                    </th>

                                </tr>

                            </thead>


                            {{-- BODY --}}
                            <tbody id="calendar-table-body" class="divide-y divide-slate-100 bg-white">

                                @php
                                $counterStart = method_exists($calendars, 'firstItem')
                                ? ($calendars->firstItem() ?? 1)
                                : 1;
                                @endphp


                                @forelse($calendars as $loopIndex => $cal)

                                <tr class="calendar-row transition hover:bg-slate-50" data-search="{{ strtolower(
        ($cal->calendar_code ?? '') . ' ' .
        ($cal->calendar_name ?? '') . ' ' .
        ($cal->timezone ?? '') . ' ' .
        (optional($cal->organisation)->organisation_name ?? '') . ' ' .
        (optional($cal->state)->state_name ?? '')
    ) }}">

                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-slate-700">
                                        {{ $counterStart + $loopIndex }}
                                    </td>

                                    {{-- CODE --}}
                                    <td class="whitespace-nowrap px-6 py-4">

                                        <span class="inline-flex items-center rounded-lg
                                                       border border-slate-200
                                                       bg-slate-50 px-2.5 py-1
                                                       font-mono text-xs font-semibold
                                                       text-slate-700">
                                            {{ $cal->calendar_code }}
                                        </span>

                                    </td>


                                    {{-- NAME --}}
                                    <td class="px-6 py-4">

                                        <div>

                                            <p class="text-sm font-semibold
                                                           text-slate-800">
                                                {{ $cal->calendar_name }}
                                            </p>

                                            <p class="mt-0.5 text-xs text-slate-400">
                                                Calendar ID:
                                                {{ $cal->calendar_id }}
                                            </p>

                                        </div>

                                    </td>


                                    {{-- TIMEZONE --}}
                                    <td class="whitespace-nowrap px-6 py-4">

                                        <div class="flex items-center gap-2">

                                            <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor"
                                                stroke-width="1.8" viewBox="0 0 24 24">
                                                <circle cx="12" cy="12" r="9" />
                                                <path stroke-linecap="round"
                                                    d="M3 12h18M12 3c2.5 2.5 3.5 5.5 3.5 9s-1 6.5-3.5 9c-2.5-2.5-3.5-5.5-3.5-9S9.5 5.5 12 3Z" />
                                            </svg>

                                            <span class="text-sm text-slate-600">
                                                {{ $cal->timezone ?: '-' }}
                                            </span>

                                        </div>

                                    </td>


                                    {{-- ORGANISATION --}}
                                    <td class="px-6 py-4">

                                        @if(optional($cal->organisation)->organisation_name)

                                        <span class="inline-flex items-center
                                                           rounded-lg border
                                                           border-indigo-100
                                                           bg-indigo-50 px-2.5 py-1
                                                           text-xs font-medium
                                                           text-indigo-700">
                                            {{ $cal->organisation->organisation_name }}
                                        </span>

                                        @else

                                        <span class="text-sm text-slate-400">
                                            Global
                                        </span>

                                        @endif

                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-700">
                                        @if(optional($cal->state)->state_name)
                                        <span
                                            class="inline-flex items-center rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-medium text-slate-700">
                                            {{ $cal->state->state_name }}
                                        </span>
                                        @else
                                        <span class="text-sm text-slate-400">Global</span>
                                        @endif
                                    </td>


                                    {{-- STATUS --}}
                                    <td class="whitespace-nowrap px-6 py-4">

                                        @if((int) $cal->is_active === 1)

                                        <span class="inline-flex items-center gap-1.5
                                                           rounded-full bg-emerald-50
                                                           px-2.5 py-1 text-xs
                                                           font-semibold text-emerald-700">

                                            <span class="h-1.5 w-1.5 rounded-full
                                                               bg-emerald-500"></span>

                                            Active

                                        </span>

                                        @else

                                        <span class="inline-flex items-center gap-1.5
                                                           rounded-full bg-slate-100
                                                           px-2.5 py-1 text-xs
                                                           font-semibold text-slate-600">

                                            <span class="h-1.5 w-1.5 rounded-full
                                                               bg-slate-400"></span>

                                            Inactive

                                        </span>

                                        @endif

                                    </td>


                                    {{-- ACTIONS --}}
                                    <td class="px-6 py-4">

                                        <div class="flex justify-end gap-2">

                                            {{-- EDIT --}}
                                            @php
                                            $calendarData = [
                                            'id' => $cal->calendar_id,
                                            'code' => $cal->calendar_code,
                                            'name' => $cal->calendar_name,
                                            'timezone' => $cal->timezone,
                                            'organisation_id' => $cal->organisation_id ?? '',
                                            'is_active' => (int) $cal->is_active,
                                            ];
                                            @endphp

                                            <button type="button" onclick="editCalendar(this)"
                                                data-calendar-id="{{ $calendarData['id'] }}"
                                                data-calendar-code="{{ $calendarData['code'] }}"
                                                data-calendar-name="{{ $calendarData['name'] }}"
                                                data-calendar-timezone="{{ $calendarData['timezone'] }}"
                                                data-calendar-organisation="{{ $calendarData['organisation_id'] }}"
                                                data-calendar-state="{{ $cal->state_id ?? '' }}"
                                                data-calendar-active="{{ $calendarData['is_active'] }}" class="inline-flex h-8 items-center gap-1.5 rounded-lg
           border border-slate-200 bg-white px-3
           text-xs font-semibold text-slate-700
           transition hover:border-indigo-200
           hover:bg-indigo-50 hover:text-indigo-700">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor"
                                                    stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M12 20h9M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1-1 4 4-1Z" />
                                                </svg>

                                                Edit
                                            </button>

                                            <a href="{{ route('working.calendars.schedules', ['calendar_id' => $cal->calendar_id]) }}"
                                                class="inline-flex h-8 items-center gap-1.5 rounded-lg border border-slate-200 bg-slate-100 px-3 text-xs font-semibold text-slate-700 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor"
                                                    stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M4 6h16M4 12h16M4 18h16" />
                                                </svg>

                                                Schedules
                                            </a>





                                            {{-- TOGGLE --}}
                                            <form method="POST" action="{{ route(
                                                        'working.calendars.toggle',
                                                        ['calendar_id' => $cal->calendar_id]
                                                    ) }}">

                                                @csrf

                                                <button type="submit" class="inline-flex h-8 items-center
                                                               gap-1.5 rounded-lg
                                                               border border-amber-200
                                                               bg-amber-50 px-3
                                                               text-xs font-semibold
                                                               text-amber-700
                                                               transition hover:bg-amber-100">

                                                    @if((int) $cal->is_active === 1)

                                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor"
                                                        stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M6 18 18 6M6 6l12 12" />
                                                    </svg>

                                                    Disable

                                                    @else

                                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor"
                                                        stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="m5 12 4 4L19 6" />
                                                    </svg>

                                                    Activate

                                                    @endif

                                                </button>

                                            </form>


                                            {{-- DELETE --}}
                                            <form method="POST" action="{{ route(
                                                        'working.calendars.destroy',
                                                        ['calendar_id' => $cal->calendar_id]
                                                    ) }}" onsubmit="return confirmDeleteCalendar(event)">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="inline-flex h-8 items-center
                                                               gap-1.5 rounded-lg
                                                               border border-rose-200
                                                               bg-rose-50 px-3
                                                               text-xs font-semibold
                                                               text-rose-700
                                                               transition hover:bg-rose-100">

                                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor"
                                                        stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M3 6h18M8 6V4h8v2m-9 0 1 14h8l1-14" />
                                                    </svg>

                                                    Delete

                                                </button>

                                            </form>

                                        </div>

                                    </td>

                                </tr>

                                @empty

                                <tr id="calendar-empty-row">

                                    <td colspan="7" class="px-6 py-14 text-center">

                                        <div class="flex flex-col items-center">

                                            <div class="flex h-14 w-14 items-center
                                                           justify-center rounded-full
                                                           bg-slate-100">

                                                <svg class="h-7 w-7 text-slate-400" fill="none" stroke="currentColor"
                                                    stroke-width="1.5" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M8 2v4m8-4v4M3 10h18M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" />
                                                </svg>

                                            </div>

                                            <h4 class="mt-4 text-sm font-semibold
                                                           text-slate-800">
                                                No working calendars configured
                                            </h4>

                                            <p class="mt-1 text-sm text-slate-500">
                                                Create a calendar to configure
                                                working schedules and holidays.
                                            </p>

                                        </div>

                                    </td>

                                </tr>

                                @endforelse


                                {{-- SEARCH EMPTY --}}
                                <tr id="calendar-search-empty" class="hidden">

                                    <td colspan="7" class="px-6 py-12 text-center">

                                        <p class="text-sm font-semibold text-slate-700">
                                            No matching calendars
                                        </p>

                                        <p class="mt-1 text-sm text-slate-500">
                                            Try changing your search criteria.
                                        </p>

                                    </td>

                                </tr>

                            </tbody>

                        </table>

                    </div>

                </div>


                {{-- =================================================
                    PAGINATION
                ================================================== --}}
                @if(method_exists($calendars, 'links'))

                <div class="border-t border-slate-200 bg-slate-50 px-5 py-4">

                    {{ $calendars->links() }}

                </div>

                @endif

            </div>

        </div>

    </div>


    {{-- =========================================================
        CREATE / EDIT MODAL
    ========================================================== --}}
    <div id="calendar-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">

        {{-- BACKDROP --}}
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeCalendarModal()"></div>


        {{-- MODAL --}}
        <div class="relative flex min-h-full items-center
                   justify-center p-4">

            <div class="relative w-full max-w-2xl overflow-hidden
                       rounded-2xl border border-slate-200
                       bg-white shadow-2xl">

                <form id="calendar-form" method="POST" action="{{ route('working.calendars.store') }}">

                    @csrf


                    {{-- =============================================
                        HEADER
                    ============================================== --}}
                    <div class="flex items-start justify-between
                               border-b border-slate-200 px-6 py-5">

                        <div class="flex items-center gap-3">

                            <div class="flex h-10 w-10 items-center
                                       justify-center rounded-xl
                                       bg-indigo-50 text-indigo-600">

                                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M8 2v4m8-4v4M3 10h18M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" />
                                </svg>

                            </div>

                            <div>

                                <h3 id="calendar-modal-title" class="text-lg font-bold text-slate-800">
                                    Add Calendar
                                </h3>

                                <p class="mt-0.5 text-sm text-slate-500">
                                    Create or update a working calendar.
                                </p>

                            </div>

                        </div>


                        {{-- CLOSE --}}
                        <button type="button" onclick="closeCalendarModal()" class="rounded-lg p-2 text-slate-400
                                   transition hover:bg-slate-100
                                   hover:text-slate-700">

                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>

                        </button>

                    </div>


                    {{-- HIDDEN --}}
                    <input type="hidden" id="calendar_id" name="calendar_id">

                    <input type="hidden" id="calendar_form_method" name="_method" value="POST">


                    {{-- =============================================
                        FORM
                    ============================================== --}}
                    <div class="space-y-5 px-6 py-6">

                        {{-- NAME + CODE --}}
                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                            <div>

                                <label for="calendar_name" class="mb-1.5 block text-sm font-semibold
                                           text-slate-700">
                                    Calendar Name
                                    <span class="text-rose-500">*</span>
                                </label>

                                <input id="calendar_name" name="calendar_name" type="text" required maxlength="150"
                                    placeholder="e.g. India Business Calendar" class="block w-full rounded-xl border
                                           border-slate-300 bg-white
                                           px-3.5 py-2.5 text-sm
                                           text-slate-800 shadow-sm
                                           outline-none transition
                                           placeholder:text-slate-400
                                           focus:border-indigo-500
                                           focus:ring-2
                                           focus:ring-indigo-500/20">

                            </div>


                            <div>

                                <label for="calendar_code" class="mb-1.5 block text-sm font-semibold
                                           text-slate-700">
                                    Calendar Code
                                    <span class="text-rose-500">*</span>
                                </label>

                                <input id="calendar_code" name="calendar_code" type="text" required maxlength="50"
                                    placeholder="e.g. WC-IND-01" class="block w-full rounded-xl border
                                           border-slate-300 bg-white
                                           px-3.5 py-2.5 text-sm
                                           font-mono text-slate-800
                                           shadow-sm outline-none
                                           transition
                                           placeholder:text-slate-400
                                           focus:border-indigo-500
                                           focus:ring-2
                                           focus:ring-indigo-500/20">

                            </div>

                        </div>


                        {{-- TIMEZONE + ORGANISATION --}}
                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                            {{-- TIMEZONE --}}
                            <div>

                                <label for="timezone" class="mb-1.5 block text-sm font-semibold
                                           text-slate-700">
                                    Timezone
                                </label>

                                <select id="timezone" name="timezone" class="block w-full rounded-xl border
                                           border-slate-300 bg-white
                                           px-3.5 py-2.5 text-sm
                                           text-slate-800 shadow-sm
                                           outline-none transition
                                           focus:border-indigo-500
                                           focus:ring-2
                                           focus:ring-indigo-500/20">

                                    <option value="Asia/Kolkata">
                                        Asia/Kolkata
                                    </option>

                                    <option value="UTC">
                                        UTC
                                    </option>

                                    <option value="Asia/Dubai">
                                        Asia/Dubai
                                    </option>

                                    <option value="Asia/Singapore">
                                        Asia/Singapore
                                    </option>

                                    <option value="Asia/Kolkata">
                                        Asia/Kolkata
                                    </option>

                                </select>

                            </div>


                            {{-- ORGANISATION --}}
                            <div>

                                <label for="organisation_id" class="mb-1.5 block text-sm font-semibold
                                           text-slate-700">
                                    Organisation
                                </label>

                                <select id="organisation_id" name="organisation_id" class="block w-full rounded-xl border
                                           border-slate-300 bg-white
                                           px-3.5 py-2.5 text-sm
                                           text-slate-800 shadow-sm
                                           outline-none transition
                                           focus:border-indigo-500
                                           focus:ring-2
                                           focus:ring-indigo-500/20">

                                    <option value="">
                                        Global / All Organisations
                                    </option>

                                    @foreach($organisations as $org)

                                    <option value="{{ $org->organisation_id }}">
                                        {{ $org->organisation_name }}
                                    </option>

                                    @endforeach

                                </select>

                            </div>

                            {{-- STATE --}}
                            <div>

                                <label for="state_id" class="mb-1.5 block text-sm font-semibold
                                           text-slate-700">
                                    State
                                </label>

                                <select id="state_id" name="state_id" class="block w-full rounded-xl border
                                           border-slate-300 bg-white
                                           px-3.5 py-2.5 text-sm
                                           text-slate-800 shadow-sm
                                           outline-none transition
                                           focus:border-indigo-500
                                           focus:ring-2
                                           focus:ring-indigo-500/20">

                                    <option value="">
                                        Global / All States
                                    </option>

                                    @foreach($states as $state)

                                    <option value="{{ $state->state_id }}">
                                        {{ $state->state_name }}
                                    </option>

                                    @endforeach

                                </select>

                            </div>

                        </div>


                        {{-- ACTIVE --}}
                        <div class="rounded-xl border border-slate-200
                                   bg-slate-50 p-4">

                            <label class="flex cursor-pointer items-center gap-3">

                                <input id="calendar_is_active" name="is_active" type="checkbox" value="1" checked class="h-4 w-4 rounded border-slate-300
                                           text-indigo-600
                                           focus:ring-indigo-500">

                                <div>

                                    <span class="block text-sm font-semibold
                                               text-slate-700">
                                        Active Calendar
                                    </span>

                                    <span class="block text-xs text-slate-500">
                                        This calendar can be used for working
                                        schedules and SLA calculations.
                                    </span>

                                </div>

                            </label>

                        </div>

                    </div>


                    {{-- =============================================
                        FOOTER
                    ============================================== --}}
                    <div class="flex items-center justify-end gap-3
                               border-t border-slate-200
                               bg-slate-50 px-6 py-4">

                        <button type="button" onclick="closeCalendarModal()" class="rounded-xl border border-slate-300
                                   bg-white px-4 py-2.5 text-sm
                                   font-semibold text-slate-700
                                   transition hover:bg-slate-100">
                            Cancel
                        </button>

                        <button id="calendar-modal-submit" type="submit" class="inline-flex items-center justify-center
                                   rounded-xl bg-indigo-600 px-5 py-2.5
                                   text-sm font-semibold text-white
                                   shadow-sm transition hover:bg-indigo-700
                                   focus:outline-none focus:ring-2
                                   focus:ring-indigo-500
                                   focus:ring-offset-2">
                            Save Calendar
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>


    {{-- =========================================================
        JAVASCRIPT
    ========================================================== --}}
    <script>
    const calendarModal =
        document.getElementById('calendar-modal');

    const calendarForm =
        document.getElementById('calendar-form');

    const storeCalendarUrl =
        "{{ route('working.calendars.store') }}";

    const updateCalendarBaseUrl =
        "{{ url('/working-calendars') }}";


    // =========================================================
    // OPEN CREATE MODAL
    // =========================================================

    function openCalendarModal() {

        document.getElementById('calendar-modal-title').textContent =
            'Add Calendar';

        document.getElementById('calendar-modal-submit').textContent =
            'Save Calendar';

        calendarForm.action =
            storeCalendarUrl;

        document.getElementById('calendar_form_method').value =
            'POST';

        document.getElementById('calendar_id').value =
            '';

        document.getElementById('calendar_name').value =
            '';

        document.getElementById('calendar_code').value =
            '';

        document.getElementById('timezone').value =
            "{{ config('app.timezone', 'Asia/Kolkata') }}";

        document.getElementById('organisation_id').value =
            '';

        document.getElementById('calendar_is_active').checked =
            true;

        document.getElementById('state_id').value =
            '';

        calendarModal.classList.remove('hidden');

        document.body.classList.add('overflow-hidden');

        setTimeout(function() {

            document
                .getElementById('calendar_name')
                .focus();

        }, 100);
    }


    // =========================================================
    // CLOSE MODAL
    // =========================================================

    function closeCalendarModal() {

        calendarModal.classList.add('hidden');

        document.body.classList.remove('overflow-hidden');
    }


    // =========================================================
    // EDIT
    // =========================================================


    function editCalendar(button) {

        const id =
            button.dataset.calendarId;

        const code =
            button.dataset.calendarCode;

        const name =
            button.dataset.calendarName;

        const timezone =
            button.dataset.calendarTimezone;

        const organisationId =
            button.dataset.calendarOrganisation;

        const stateId =
            button.dataset.calendarState;

        const isActive =
            button.dataset.calendarActive;


        // Modal title
        document.getElementById(
            'calendar-modal-title'
        ).textContent = 'Edit Calendar';


        // Submit button
        document.getElementById(
            'calendar-modal-submit'
        ).textContent = 'Update Calendar';


        // Form action
        document.getElementById(
                'calendar-form'
            ).action =
            updateCalendarBaseUrl + '/' + id;


        // HTTP method
        document.getElementById(
            'calendar_form_method'
        ).value = 'PUT';


        // Hidden ID
        document.getElementById(
            'calendar_id'
        ).value = id;


        // Calendar name
        document.getElementById(
            'calendar_name'
        ).value = name || '';


        // Calendar code
        document.getElementById(
            'calendar_code'
        ).value = code || '';


        // Timezone
        document.getElementById(
            'timezone'
        ).value = timezone || '';


        // Organisation
        document.getElementById(
            'organisation_id'
        ).value = organisationId || '';


        // State
        document.getElementById(
            'state_id'
        ).value = stateId || '';


        // Active
        const activeCheckbox =
            document.getElementById(
                'calendar_is_active'
            );

        if (activeCheckbox) {

            activeCheckbox.checked =
                Number(isActive) === 1;

        }


        // Open modal
        document.getElementById(
            'calendar-modal'
        ).classList.remove('hidden');


        // Prevent background scrolling
        document.body.classList.add(
            'overflow-hidden'
        );


        // Focus
        setTimeout(function() {

            document
                .getElementById('calendar_name')
                .focus();

        }, 100);
    }





    // =========================================================
    // DELETE CONFIRMATION
    // =========================================================

    function confirmDeleteCalendar(event) {

        const confirmed = confirm(
            'Are you sure you want to delete this working calendar?'
        );

        if (!confirmed) {

            event.preventDefault();

            return false;
        }

        return true;
    }


    // =========================================================
    // FLASH MESSAGE
    // =========================================================

    function closeCalendarMessage() {

        const container =
            document.getElementById(
                'calendar-message-container'
            );

        if (!container) {
            return;
        }

        container.style.transition =
            'opacity 0.3s ease';

        container.style.opacity =
            '0';

        setTimeout(function() {

            container.remove();

        }, 300);
    }


    // =========================================================
    // SEARCH
    // =========================================================

    function filterCalendars() {

        const query =
            document
            .getElementById('calendar-search')
            .value
            .trim()
            .toLowerCase();


        const rows =
            document.querySelectorAll(
                '.calendar-row'
            );

        const searchEmpty =
            document.getElementById(
                'calendar-search-empty'
            );


        let visibleCount = 0;


        rows.forEach(function(row) {

            const content =
                row.dataset.search || '';

            const match =
                query === '' ||
                content.includes(query);


            row.classList.toggle(
                'hidden',
                !match
            );


            if (match) {
                visibleCount++;
            }

        });


        if (searchEmpty) {

            searchEmpty.classList.toggle(
                'hidden',
                !(query !== '' && visibleCount === 0)
            );

        }

    }


    // =========================================================
    // KEYBOARD
    // =========================================================

    document.addEventListener(
        'keydown',
        function(event) {

            if (
                event.key === 'Escape' &&
                !calendarModal.classList.contains('hidden')
            ) {

                closeCalendarModal();

            }

        }
    );


    // =========================================================
    // PAGE LOAD
    // =========================================================

    document.addEventListener(
        'DOMContentLoaded',
        function() {

            const searchInput =
                document.getElementById(
                    'calendar-search'
                );

            if (searchInput) {

                searchInput.addEventListener(
                    'input',
                    filterCalendars
                );

            }


            const message =
                document.getElementById(
                    'calendar-message'
                );

            if (message) {

                setTimeout(
                    closeCalendarMessage,
                    10000
                );

            }

        }
    );
    </script>

</x-app-layout>