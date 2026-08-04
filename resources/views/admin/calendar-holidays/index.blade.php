<x-app-layout>

    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ $title ?? __('Calendar Holiday Master') }}
        </h2>
    </x-slot>

    <div class="py-8">

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

                {{-- =========================================================
                    HEADER
                ========================================================== --}}

                <div class="border-b border-slate-200 bg-slate-50 px-5 py-5">

                    <div class="grid gap-4 md:grid-cols-[1fr_auto_auto] md:items-center">

                        <div>

                            <p class="text-sm text-slate-600">
                                {{ $description ?? __('Manage calendar holidays and non-working dates.') }}
                            </p>

                        </div>

                        {{-- Search --}}

                        <div
                            class="flex items-center justify-end rounded-2xl border border-slate-200 bg-white px-3 py-2 shadow-sm">

                            <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">

                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z">
                                </path>

                            </svg>

                            <input id="holiday-search" type="text" placeholder="Search"
                                class="ml-2 w-36 bg-transparent text-sm text-slate-900 placeholder:text-slate-400 outline-none" />

                        </div>

                        {{-- Add --}}

                        <button type="button" onclick="openHolidayMasterModal()"
                            class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">

                            Add New

                        </button>

                    </div>

                </div>


                {{-- =========================================================
                    EXPORT / FILTER
                ========================================================== --}}

                <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">

                    <div class="flex flex-wrap items-center gap-3">

                        {{-- Calendar Filter --}}

                        <select id="holiday-calendar-filter"
                            class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 outline-none focus:border-slate-400">

                            <option value="">All Calendars</option>

                            @foreach($calendars as $calendar)

                            <option value="{{ $calendar->calendar_id }}">

                                {{ $calendar->calendar_name }}

                                @if($calendar->calendar_code)
                                ({{ $calendar->calendar_code }})
                                @endif

                            </option>

                            @endforeach

                        </select>


                        {{-- Holiday Type --}}

                        <select id="holiday-type-filter"
                            class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 outline-none focus:border-slate-400">

                            <option value="">All Types</option>
                            <option value="PUBLIC">Public</option>
                            <option value="NATIONAL">National</option>
                            <option value="REGIONAL">Regional</option>
                            <option value="COMPANY">Company</option>
                            <option value="OPTIONAL">Optional</option>

                        </select>


                        {{-- Export --}}

                        <button type="button" onclick="exportHolidayTable('csv')"
                            class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-100">

                            Export CSV

                        </button>

                        <button type="button" onclick="exportHolidayTable('xlsx')"
                            class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-semibold text-emerald-700 hover:bg-emerald-100">

                            Export XLSX

                        </button>

                        <button type="button" onclick="exportHolidayTable('pdf')"
                            class="rounded-lg border border-purple-200 bg-purple-50 px-3 py-2 text-sm font-semibold text-purple-700 hover:bg-purple-100">

                            Export PDF

                        </button>

                    </div>

                </div>


                {{-- =========================================================
                    FLASH MESSAGE
                ========================================================== --}}

                @if(session('success') || session('error'))

                <div class="px-5 py-4" id="holiday-message-container">

                    <div id="holiday-message" class="relative rounded-2xl px-4 py-3 text-sm font-semibold shadow-sm
                            {{ session('success')
                                ? 'bg-emerald-50 text-emerald-700'
                                : 'bg-rose-50 text-rose-700' }}">

                        <span>
                            {{ session('success') ?? session('error') }}
                        </span>

                        <button type="button" onclick="closeHolidayMessage()"
                            class="absolute right-3 top-3 rounded-full bg-white/80 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-white">

                            Close

                        </button>

                    </div>

                </div>

                @endif


                {{-- =========================================================
                    VALIDATION ERRORS
                ========================================================== --}}

                @if($errors->any())

                <div class="px-5 py-4">

                    <div class="rounded-2xl bg-rose-50 px-4 py-3 text-sm text-rose-700">

                        <ul class="list-disc space-y-1 pl-5">

                            @foreach($errors->all() as $error)

                            <li>{{ $error }}</li>

                            @endforeach

                        </ul>

                    </div>

                </div>

                @endif


                {{-- =========================================================
                    TABLE
                ========================================================== --}}

                <div class="overflow-x-auto">

                    <div class="max-h-[500px] overflow-auto">

                        <table class="min-w-full divide-y divide-slate-200" id="holiday-table">

                            <thead class="sticky top-0 z-10 bg-purple-100">

                                <tr>

                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">
                                        Calendar
                                    </th>

                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">
                                        Holiday Date
                                    </th>

                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">
                                        Holiday Name
                                    </th>

                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">
                                        Type
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

                                @forelse($holidays as $holiday)

                                <tr data-calendar-id="{{ $holiday->calendar_id }}"
                                    data-holiday-type="{{ strtoupper($holiday->holiday_type ?? '') }}">

                                    {{-- Calendar --}}

                                    <td class="px-5 py-3 text-sm">

                                        <div class="font-semibold text-slate-900">

                                            {{ $holiday->calendar->calendar_name ?? '-' }}

                                        </div>

                                        @if($holiday->calendar?->calendar_code)

                                        <div class="text-xs text-slate-500">

                                            {{ $holiday->calendar->calendar_code }}

                                        </div>

                                        @endif

                                    </td>


                                    {{-- Date --}}

                                    <td class="px-5 py-3 text-sm font-semibold text-slate-700">

                                        {{ $holiday->holiday_date?->format('d-M-Y') ?? '-' }}

                                    </td>


                                    {{-- Holiday --}}

                                    <td class="px-5 py-3 text-sm text-slate-900">

                                        {{ $holiday->holiday_name }}

                                    </td>


                                    {{-- Type --}}

                                    <td class="px-5 py-3 text-sm">

                                        @php

                                        $typeClass = match(strtoupper($holiday->holiday_type ?? '')) {

                                        'NATIONAL' =>
                                        'bg-purple-50 text-purple-700',

                                        'REGIONAL' =>
                                        'bg-blue-50 text-blue-700',

                                        'COMPANY' =>
                                        'bg-amber-50 text-amber-700',

                                        'OPTIONAL' =>
                                        'bg-cyan-50 text-cyan-700',

                                        default =>
                                        'bg-slate-100 text-slate-700',

                                        };

                                        @endphp

                                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $typeClass }}">

                                            {{ $holiday->holiday_type_label ?? ($holiday->holiday_type ?? 'Holiday') }}

                                        </span>

                                    </td>


                                    {{-- Status --}}

                                    <td class="px-5 py-3 text-sm">

                                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold
                                                {{ (int) $holiday->is_active === 1
                                                    ? 'bg-emerald-50 text-emerald-700'
                                                    : 'bg-amber-50 text-amber-700' }}">

                                            {{ (int) $holiday->is_active === 1
                                                    ? 'Active'
                                                    : 'Inactive' }}

                                        </span>

                                    </td>


                                    {{-- Actions --}}

                                    <td class="px-5 py-3 text-sm">

                                        <div class="flex flex-wrap items-center gap-2">

                                            {{-- Edit --}}

                                            <button type="button" data-holiday-id="{{ $holiday->holiday_id }}"
                                                data-calendar-id="{{ $holiday->calendar_id }}"
                                                data-holiday-date="{{ $holiday->holiday_date?->format('Y-m-d') }}"
                                                data-holiday-name="{{ $holiday->holiday_name }}"
                                                data-holiday-type="{{ $holiday->holiday_type }}"
                                                data-description="{{ $holiday->description ?? '' }}"
                                                onclick="editHoliday(this.dataset)"
                                                class="rounded-lg bg-slate-900 px-2.5 py-1.5 text-xs font-semibold text-white hover:bg-slate-800">

                                                Edit

                                            </button>


                                            {{-- Toggle --}}

                                            <form method="POST"
                                                action="{{ route('calendar-holidays.toggle', ['holiday_id' => $holiday->holiday_id]) }}"
                                                class="inline">

                                                @csrf

                                                <button type="submit"
                                                    class="rounded-lg px-2.5 py-1.5 text-xs font-semibold
                                                        {{ (int) $holiday->is_active === 1
                                                            ? 'bg-rose-100 text-rose-700 hover:bg-rose-200'
                                                            : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' }}">

                                                    {{ (int) $holiday->is_active === 1
                                                            ? 'Disable'
                                                            : 'Activate' }}

                                                </button>

                                            </form>


                                            {{-- View --}}

                                            <a href="{{ route('calendar-holidays.show', $holiday->holiday_id) }}"
                                                class="rounded-lg bg-blue-100 px-2.5 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-200">
                                                View

                                            </a>

                                        </div>

                                    </td>

                                </tr>

                                @empty

                                <tr class="empty-row">

                                    <td colspan="6" class="px-5 py-8 text-center text-sm text-slate-500">

                                        No calendar holidays found.

                                    </td>

                                </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>


                {{-- =========================================================
                    PAGINATION
                ========================================================== --}}

                @if($holidays->hasPages())

                <div class="border-t border-slate-200 bg-slate-50 px-5 py-4">

                    {{ $holidays->links() }}

                </div>

                @endif

            </div>

        </div>

    </div>


    {{-- =============================================================
        ADD / EDIT MODAL
    ============================================================== --}}

    <div id="holiday-master-modal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-slate-900/60 px-4 py-8">

        <div class="mx-auto flex max-w-2xl flex-col rounded-3xl bg-white shadow-2xl">

            <form id="holiday-master-form" method="POST" action="">

                @csrf

                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">

                    <div>

                        <h3 id="holiday-modal-title" class="text-lg font-semibold text-slate-900">

                            Add Calendar Holiday

                        </h3>

                        <p class="text-sm text-slate-600">

                            Create or update a holiday entry.

                        </p>

                    </div>

                    <button type="button" onclick="closeHolidayMasterModal()"
                        class="rounded-full bg-slate-100 p-2 text-slate-700 hover:bg-slate-200">

                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">

                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12">
                            </path>

                        </svg>

                    </button>

                </div>


                <input type="hidden" id="holiday_id" name="holiday_id" value="">

                <input type="hidden" id="holiday_form_method" name="_method" value="POST">


                <div class="space-y-4 px-5 py-5">

                    {{-- Calendar --}}

                    <div>

                        <label class="mb-1 block text-sm font-medium text-slate-700">

                            Working Calendar

                        </label>

                        <select id="calendar_id" name="calendar_id" required
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-slate-400">

                            <option value="">
                                Select Calendar
                            </option>

                            @foreach($calendars as $calendar)

                            <option value="{{ $calendar->calendar_id }}">

                                {{ $calendar->calendar_name }}

                                @if($calendar->calendar_code)
                                - {{ $calendar->calendar_code }}
                                @endif

                            </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- Date + Type --}}

                    <div class="grid gap-4 md:grid-cols-2">

                        <div>

                            <label class="mb-1 block text-sm font-medium text-slate-700">

                                Holiday Date

                            </label>

                            <input id="holiday_date" name="holiday_date" type="date" required
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400">

                        </div>


                        <div>

                            <label class="mb-1 block text-sm font-medium text-slate-700">

                                Holiday Type

                            </label>

                            <select id="holiday_type" name="holiday_type"
                                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-slate-400">

                                <option value="PUBLIC">
                                    Public
                                </option>

                                <option value="NATIONAL">
                                    National
                                </option>

                                <option value="REGIONAL">
                                    Regional
                                </option>

                                <option value="COMPANY">
                                    Company
                                </option>

                                <option value="OPTIONAL">
                                    Optional
                                </option>

                            </select>

                        </div>

                    </div>


                    {{-- Holiday Name --}}

                    <div>

                        <label class="mb-1 block text-sm font-medium text-slate-700">

                            Holiday Name

                        </label>

                        <input id="holiday_name" name="holiday_name" type="text" maxlength="150" required
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400"
                            placeholder="Enter holiday name">

                    </div>


                    {{-- Description --}}

                    <div>

                        <label class="mb-1 block text-sm font-medium text-slate-700">

                            Description

                        </label>

                        <textarea id="description" name="description" rows="3" maxlength="500"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400"
                            placeholder="Enter description"></textarea>

                    </div>


                    {{-- Footer --}}

                    <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-4">

                        <button type="button" onclick="closeHolidayMasterModal()"
                            class="rounded-xl border border-slate-300 bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">

                            Cancel

                        </button>

                        <button type="submit" id="holiday-modal-submit"
                            class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">

                            Save

                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>


    <script>
    /*
        |--------------------------------------------------------------------------
        | Open Add Modal
        |--------------------------------------------------------------------------
        */

    function openHolidayMasterModal() {

        document.getElementById('holiday-modal-title').textContent =
            'Add Calendar Holiday';

        document.getElementById('holiday-master-form').action =
            "{{ route('calendar-holidays.store') }}";

        document.getElementById('holiday_form_method').value =
            'POST';

        document.getElementById('holiday_id').value =
            '';

        document.getElementById('calendar_id').value =
            '';

        document.getElementById('holiday_date').value =
            '';

        document.getElementById('holiday_name').value =
            '';

        document.getElementById('holiday_type').value =
            'PUBLIC';

        document.getElementById('description').value =
            '';

        document.getElementById('holiday-modal-submit').textContent =
            'Save';

        document.getElementById('holiday-master-modal')
            .classList.remove('hidden');
    }


    /*
    |--------------------------------------------------------------------------
    | Close Modal
    |--------------------------------------------------------------------------
    */

    function closeHolidayMasterModal() {

        document.getElementById('holiday-master-modal')
            .classList.add('hidden');
    }


    /*
    |--------------------------------------------------------------------------
    | Edit Holiday
    |--------------------------------------------------------------------------
    */

    function editHoliday(holiday) {

        document.getElementById('holiday-modal-title').textContent =
            'Edit Calendar Holiday';


        document.getElementById('holiday-master-form').action =
            "{{ url('/calendar-holidays') }}/" + holiday.holidayId;

        document.getElementById('holiday_form_method').value =
            'PUT';

        document.getElementById('holiday_id').value =
            holiday.holidayId;

        document.getElementById('calendar_id').value =
            holiday.calendarId;

        document.getElementById('holiday_date').value =
            holiday.holidayDate;

        document.getElementById('holiday_name').value =
            holiday.holidayName;

        document.getElementById('holiday_type').value =
            holiday.holidayType || 'PUBLIC';

        document.getElementById('description').value =
            holiday.description || '';

        document.getElementById('holiday-modal-submit').textContent =
            'Update';

        document.getElementById('holiday-master-modal')
            .classList.remove('hidden');
    }


    /*
    |--------------------------------------------------------------------------
    | Export
    |--------------------------------------------------------------------------
    */

    function exportHolidayTable(format) {

        const params =
            new URLSearchParams({
                format: format
            });


        window.location.href = "{{ route('calendar-holidays.index') }}?" + params.toString();
    }


    /*
    |--------------------------------------------------------------------------
    | Close Message
    |--------------------------------------------------------------------------
    */

    function closeHolidayMessage() {

        const container =
            document.getElementById(
                'holiday-message-container'
            );

        if (container) {

            container.style.transition =
                'opacity 0.4s ease';

            container.style.opacity = '0';

            setTimeout(() => {

                container.remove();

            }, 400);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Filter
    |--------------------------------------------------------------------------
    */

    function filterHolidays() {

        const search =
            document
            .getElementById('holiday-search')
            .value
            .trim()
            .toLowerCase();

        const calendar =
            document
            .getElementById('holiday-calendar-filter')
            .value;

        const type =
            document
            .getElementById('holiday-type-filter')
            .value
            .toUpperCase();

        const rows =
            document.querySelectorAll(
                '#holiday-table tbody tr'
            );

        let visibleCount = 0;

        rows.forEach((row) => {

            if (
                row.classList.contains('empty-row')
            ) {
                return;
            }

            const text =
                row.textContent.toLowerCase();

            const rowCalendar =
                row.dataset.calendarId || '';

            const rowType =
                row.dataset.holidayType || '';

            const searchMatch =
                search === '' ||
                text.includes(search);

            const calendarMatch =
                calendar === '' ||
                calendar === rowCalendar;

            const typeMatch =
                type === '' ||
                type === rowType;

            const match =
                searchMatch &&
                calendarMatch &&
                typeMatch;

            if (match) {

                row.classList.remove('hidden');

                visibleCount++;

            } else {

                row.classList.add('hidden');
            }

        });


        const emptyRow =
            document.querySelector(
                '#holiday-table tbody tr.empty-row'
            );

        if (emptyRow) {

            emptyRow.classList.toggle(
                'hidden',
                visibleCount !== 0
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | DOM Ready
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'DOMContentLoaded',
        function() {

            const message =
                document.getElementById(
                    'holiday-message'
                );

            if (message) {

                setTimeout(
                    closeHolidayMessage,
                    10000
                );
            }


            const search =
                document.getElementById(
                    'holiday-search'
                );

            const calendarFilter =
                document.getElementById(
                    'holiday-calendar-filter'
                );

            const typeFilter =
                document.getElementById(
                    'holiday-type-filter'
                );


            if (search) {

                search.addEventListener(
                    'input',
                    filterHolidays
                );
            }


            if (calendarFilter) {

                calendarFilter.addEventListener(
                    'change',
                    filterHolidays
                );
            }


            if (typeFilter) {

                typeFilter.addEventListener(
                    'change',
                    filterHolidays
                );
            }

        }
    );
    </script>

</x-app-layout>