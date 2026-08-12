<x-app-layout>

    {{-- =========================================================
        HEADER
    ========================================================== --}}

    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="text-xl font-bold tracking-tight text-slate-800">
                Calendar Holidays
            </h2>

            <p class="text-sm text-slate-500">
                Manage holidays assigned to your working calendars.
            </p>
        </div>
    </x-slot>


    {{-- =========================================================
        DEFAULT VARIABLES
    ========================================================== --}}

    @php
    $holidays = $holidays ?? collect();
    $calendars = $calendars ?? collect();
    $organisations = $organisations ?? collect();
    @endphp


    {{-- =========================================================
        PAGE
    ========================================================== --}}

    <div class="min-h-screen bg-slate-50 py-6">

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">


                {{-- =================================================
                    TOOLBAR
                ================================================== --}}

                <div class="border-b border-slate-200 bg-white px-5 py-5">

                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

                        {{-- TITLE --}}

                        <div>
                            <h3 class="text-base font-semibold text-slate-800">
                                Holiday Configuration
                            </h3>

                            <p class="mt-1 text-sm text-slate-500">
                                Configure public holidays and calendar holidays.
                            </p>
                        </div>


                        {{-- ACTIONS --}}

                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">

                            {{-- SEARCH --}}

                            <form method="GET" action="{{ route('holiday.calendar') }}"
                                class="flex h-10 w-full items-center rounded-xl border border-slate-300 bg-white px-3 sm:w-72">

                                <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z" />
                                </svg>

                                <input name="search" type="search" value="{{ request('search') }}" autocomplete="off"
                                    placeholder="Search holidays..."
                                    class="ml-2 w-full border-0 bg-transparent text-sm text-slate-700 outline-none placeholder:text-slate-400 focus:ring-0">

                            </form>


                            {{-- ADD HOLIDAY --}}

                            <button type="button" onclick="openHolidayModal()"
                                class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">

                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14" />
                                </svg>

                                Add Holiday

                            </button>

                        </div>

                    </div>

                </div>


                {{-- =================================================
                    SUCCESS MESSAGE
                ================================================== --}}

                @if(session('success'))

                <div id="holiday-message-container" class="px-5 pt-5">

                    <div id="holiday-message"
                        class="relative flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">

                        <svg class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" fill="none" stroke="currentColor"
                            stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m5 12 4 4L19 6" />
                        </svg>

                        <span class="pr-8 font-medium">
                            {{ session('success') }}
                        </span>

                        <button type="button" onclick="closeHolidayMessage()"
                            class="absolute right-3 top-3 rounded-lg p-1 text-emerald-600 hover:bg-emerald-100">

                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>

                        </button>

                    </div>

                </div>

                @endif


                {{-- =================================================
                    VALIDATION ERROR MESSAGE
                ================================================== --}}

                @if($errors->any())

                <div class="px-5 pt-5">

                    <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">

                        <div class="flex items-start gap-3">

                            <svg class="mt-0.5 h-5 w-5 shrink-0 text-rose-600" fill="none" stroke="currentColor"
                                stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v4m0 4h.01M10.3 3.7 2.8 17a2 2 0 0 0 1.7 3h15a2 2 0 0 0 1.7-3L13.7 3.7a2 2 0 0 0-3.4 0Z" />
                            </svg>

                            <div>
                                <p class="font-semibold">
                                    Please correct the following errors:
                                </p>

                                <ul class="mt-1 list-disc pl-5">
                                    @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>

                        </div>

                    </div>

                </div>

                @endif


                {{-- =================================================
                    TABLE
                ================================================== --}}

                <div class="overflow-x-auto">

                    <div class="max-h-[540px] overflow-auto">

                        <table class="min-w-full divide-y divide-slate-200">

                            {{-- TABLE HEADER --}}

                            <thead class="sticky top-0 z-10 bg-slate-50">

                                <tr>

                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                        #
                                    </th>

                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                        Holiday
                                    </th>

                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                        Date
                                    </th>

                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                        Calendar
                                    </th>

                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                        Scope
                                    </th>

                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                        Status
                                    </th>

                                    <th
                                        class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">
                                        Actions
                                    </th>

                                </tr>

                            </thead>


                            {{-- TABLE BODY --}}

                            <tbody class="divide-y divide-slate-100 bg-white">

                                @php
                                $counterStart = method_exists($holidays, 'firstItem')
                                ? ($holidays->firstItem() ?? 1)
                                : 1;
                                @endphp


                                @forelse($holidays as $loopIndex => $holiday)

                                <tr class="transition hover:bg-slate-50">


                                    {{-- NUMBER --}}

                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-slate-700">
                                        {{ $counterStart + $loopIndex }}
                                    </td>


                                    {{-- HOLIDAY --}}

                                    <td class="px-6 py-4">

                                        <div>

                                            <p class="text-sm font-semibold text-slate-800">
                                                {{ $holiday->holiday_name }}
                                            </p>

                                            <p class="mt-0.5 text-xs text-slate-400">
                                                Holiday ID:
                                                {{ $holiday->holiday_id }}
                                            </p>

                                        </div>

                                    </td>


                                    {{-- DATE --}}

                                    <td class="whitespace-nowrap px-6 py-4">

                                        <span
                                            class="inline-flex items-center rounded-lg border border-indigo-100 bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700">

                                            @if($holiday->holiday_date)
                                            {{ \Carbon\Carbon::parse($holiday->holiday_date)->format('d M Y') }}
                                            @else
                                            -
                                            @endif

                                        </span>

                                    </td>


                                    {{-- CALENDAR --}}

                                    <td class="px-6 py-4">

                                        @if($holiday->calendar)

                                        <div>

                                            <p class="text-sm font-semibold text-slate-700">
                                                {{ $holiday->calendar->calendar_name }}
                                            </p>

                                            @if(!empty($holiday->calendar->calendar_code))

                                            <p class="mt-0.5 font-mono text-xs text-slate-400">
                                                {{ $holiday->calendar->calendar_code }}
                                            </p>

                                            @endif

                                        </div>

                                        @else

                                        <span class="text-sm text-slate-400">
                                            -
                                        </span>

                                        @endif

                                    </td>


                                    {{-- SCOPE --}}

                                    <td class="px-6 py-4">

                                        @if($holiday->scope)

                                        <span
                                            class="inline-flex items-center rounded-lg border border-slate-200 bg-slate-50 px-2.5 py-1 text-xs font-medium text-slate-700">
                                            {{ $holiday->scope }}
                                        </span>

                                        @else

                                        <span class="text-sm text-slate-400">
                                            Global
                                        </span>

                                        @endif

                                    </td>


                                    {{-- STATUS --}}

                                    <td class="whitespace-nowrap px-6 py-4">

                                        @if((int) $holiday->is_active === 1)

                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">

                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>

                                            Active

                                        </span>

                                        @else

                                        <span
                                            class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">

                                            <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>

                                            Inactive

                                        </span>

                                        @endif

                                    </td>


                                    {{-- ACTIONS --}}

                                    <td class="px-6 py-4">

                                        <div class="flex justify-end gap-2">


                                            {{-- EDIT --}}

                                            <button type="button" onclick="editHoliday(this)"
                                                data-holiday-id="{{ $holiday->holiday_id }}"
                                                data-holiday-name="{{ $holiday->holiday_name }}"
                                                data-holiday-date="{{ $holiday->holiday_date ? \Carbon\Carbon::parse($holiday->holiday_date)->format('Y-m-d') : '' }}"
                                                data-holiday-calendar="{{ $holiday->calendar_id }}"
                                                data-holiday-scope="{{ $holiday->scope ?? '' }}"
                                                data-holiday-active="{{ (int) $holiday->is_active }}"
                                                class="inline-flex h-8 items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-slate-700 transition hover:border-indigo-200 hover:bg-indigo-50 hover:text-indigo-700">

                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor"
                                                    stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M12 20h9M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1-1 4 4-1Z" />
                                                </svg>

                                                Edit

                                            </button>


                                            {{-- TOGGLE --}}

                                            <form method="POST"
                                                action="{{ route('holiday.toggle', ['holiday_id' => $holiday->holiday_id]) }}">

                                                @csrf

                                                <button type="submit"
                                                    class="inline-flex h-8 items-center gap-1.5 rounded-lg border border-amber-200 bg-amber-50 px-3 text-xs font-semibold text-amber-700 transition hover:bg-amber-100">

                                                    @if((int) $holiday->is_active === 1)
                                                    Disable
                                                    @else
                                                    Activate
                                                    @endif

                                                </button>

                                            </form>


                                            {{-- DELETE --}}

                                            <form method="POST"
                                                action="{{ route('holiday.destroy', ['holiday_id' => $holiday->holiday_id]) }}"
                                                onsubmit="return confirmDeleteHoliday(event)">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                    class="inline-flex h-8 items-center gap-1.5 rounded-lg border border-rose-200 bg-rose-50 px-3 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">

                                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor"
                                                        stroke-width="2" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M3 6h18M8 6V4h8v2m-9 0 1 15h8l1-15M10 11v6M14 11v6" />
                                                    </svg>

                                                    Delete

                                                </button>

                                            </form>

                                        </div>

                                    </td>

                                </tr>


                                @empty

                                {{-- EMPTY STATE --}}

                                <tr>

                                    <td colspan="7" class="px-6 py-14 text-center">

                                        <div class="flex flex-col items-center">

                                            <div
                                                class="flex h-14 w-14 items-center justify-center rounded-full bg-slate-100">

                                                <svg class="h-7 w-7 text-slate-400" fill="none" stroke="currentColor"
                                                    stroke-width="1.5" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M8 2v4m8-4v4M3 10h18M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" />
                                                </svg>

                                            </div>

                                            <h4 class="mt-4 text-sm font-semibold text-slate-800">
                                                No holidays configured
                                            </h4>

                                            <p class="mt-1 text-sm text-slate-500">
                                                Create a holiday to add it to your working calendar.
                                            </p>

                                            <button type="button" onclick="openHolidayModal()"
                                                class="mt-4 inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-indigo-700">

                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M12 5v14M5 12h14" />
                                                </svg>

                                                Add Holiday

                                            </button>

                                        </div>

                                    </td>

                                </tr>

                                @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>


                {{-- =================================================
                    PAGINATION
                ================================================== --}}

                @if(method_exists($holidays, 'links'))

                <div class="border-t border-slate-200 bg-slate-50 px-5 py-4">

                    {{ $holidays->links() }}

                </div>

                @endif

            </div>

        </div>

    </div>


    {{-- =========================================================
        CREATE / EDIT MODAL
    ========================================================== --}}

    <div id="holiday-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true"
        aria-labelledby="holiday-modal-title">

        {{-- BACKDROP --}}

        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeHolidayModal()"></div>


        {{-- MODAL CONTAINER --}}

        <div class="relative flex min-h-full items-center justify-center p-4">

            <div
                class="relative w-full max-w-2xl overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl">


                {{-- FORM --}}

                <form id="holiday-form" method="POST" action="{{ route('holiday.store') }}">

                    @csrf

                    {{-- HEADER --}}

                    <div class="flex items-start justify-between border-b border-slate-200 px-6 py-5">

                        <div>

                            <h3 id="holiday-modal-title" class="text-lg font-bold text-slate-800">
                                Add Holiday
                            </h3>

                            <p class="mt-0.5 text-sm text-slate-500">
                                Create or update a calendar holiday.
                            </p>

                        </div>


                        {{-- CLOSE --}}

                        <button type="button" onclick="closeHolidayModal()"
                            class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700">

                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>

                        </button>

                    </div>


                    {{-- HIDDEN FIELDS --}}

                    <input type="hidden" id="holiday_id" name="holiday_id">

                    <input type="hidden" id="holiday_form_method" name="_method" value="POST">


                    {{-- FORM BODY --}}

                    <div class="space-y-5 px-6 py-6">


                        {{-- HOLIDAY NAME --}}

                        <div>

                            <label for="holiday_name" class="mb-1.5 block text-sm font-semibold text-slate-700">
                                Holiday Name
                                <span class="text-rose-500">*</span>
                            </label>

                            <input id="holiday_name" name="holiday_name" type="text" required maxlength="255"
                                autocomplete="off" placeholder="e.g. Republic Day"
                                class="block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-sm outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">

                        </div>


                        {{-- DATE + CALENDAR --}}

                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">


                            {{-- DATE --}}

                            <div>

                                <label for="holiday_date" class="mb-1.5 block text-sm font-semibold text-slate-700">
                                    Holiday Date
                                    <span class="text-rose-500">*</span>
                                </label>

                                <input id="holiday_date" name="holiday_date" type="date" required
                                    class="block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">

                            </div>


                            {{-- CALENDAR --}}

                            <div>

                                <label for="calendar_id" class="mb-1.5 block text-sm font-semibold text-slate-700">
                                    Working Calendar
                                    <span class="text-rose-500">*</span>
                                </label>

                                <select id="calendar_id" name="calendar_id" required
                                    class="block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">

                                    <option value="">
                                        Select Calendar
                                    </option>

                                    @foreach($calendars as $calendar)

                                    <option value="{{ $calendar->calendar_id }}">
                                        {{ $calendar->calendar_name }}
                                    </option>

                                    @endforeach

                                </select>

                            </div>

                        </div>


                        {{-- SCOPE --}}

                        <div>

                            <label for="scope" class="mb-1.5 block text-sm font-semibold text-slate-700">
                                Scope
                            </label>

                            <input id="scope" name="scope" type="text" maxlength="255" autocomplete="off"
                                placeholder="e.g. National, Andhra Pradesh, Organisation"
                                class="block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 shadow-sm outline-none placeholder:text-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">

                            <p class="mt-1.5 text-xs text-slate-400">
                                Optional. Leave blank if this is a global holiday.
                            </p>

                        </div>


                        {{-- ACTIVE --}}

                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">

                            <label class="flex cursor-pointer items-center gap-3">

                                <input id="holiday_is_active" name="is_active" type="checkbox" value="1" checked
                                    class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">

                                <div>

                                    <span class="block text-sm font-semibold text-slate-700">
                                        Active Holiday
                                    </span>

                                    <span class="block text-xs text-slate-500">
                                        Active holidays will be considered during working-day calculations.
                                    </span>

                                </div>

                            </label>

                        </div>

                    </div>


                    {{-- FOOTER --}}

                    <div class="flex items-center justify-end gap-3 border-t border-slate-200 bg-slate-50 px-6 py-4">

                        <button type="button" onclick="closeHolidayModal()"
                            class="rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-100">
                            Cancel
                        </button>


                        <button id="holiday-modal-submit" type="submit"
                            class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Save Holiday
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
    /*
        |--------------------------------------------------------------------------
        | ELEMENTS
        |--------------------------------------------------------------------------
        */

    const holidayModal = document.getElementById('holiday-modal');

    const holidayForm = document.getElementById('holiday-form');

    const storeHolidayUrl = @json(route('holiday.store'));

    const updateHolidayBaseUrl = @json(url('/holiday-calendar'));


    /*
    |--------------------------------------------------------------------------
    | OPEN CREATE MODAL
    |--------------------------------------------------------------------------
    */

    function openHolidayModal() {

        document.getElementById('holiday-modal-title').textContent =
            'Add Holiday';

        document.getElementById('holiday-modal-submit').textContent =
            'Save Holiday';


        holidayForm.action = storeHolidayUrl;


        document.getElementById('holiday_form_method').value =
            'POST';

        document.getElementById('holiday_id').value =
            '';


        document.getElementById('holiday_name').value =
            '';

        document.getElementById('holiday_date').value =
            '';

        document.getElementById('calendar_id').value =
            '';

        document.getElementById('scope').value =
            '';

        document.getElementById('holiday_is_active').checked =
            true;


        holidayModal.classList.remove('hidden');

        document.body.classList.add('overflow-hidden');


        setTimeout(function() {

            document
                .getElementById('holiday_name')
                .focus();

        }, 100);
    }


    /*
    |--------------------------------------------------------------------------
    | OPEN EDIT MODAL
    |--------------------------------------------------------------------------
    */

    function editHoliday(button) {

        const id =
            button.dataset.holidayId || '';

        const name =
            button.dataset.holidayName || '';

        const date =
            button.dataset.holidayDate || '';

        const calendarId =
            button.dataset.holidayCalendar || '';

        const scope =
            button.dataset.holidayScope || '';

        const isActive =
            button.dataset.holidayActive || '0';


        /*
        |--------------------------------------------------------------------------
        | MODAL TITLE
        |--------------------------------------------------------------------------
        */

        document.getElementById('holiday-modal-title').textContent =
            'Edit Holiday';


        document.getElementById('holiday-modal-submit').textContent =
            'Update Holiday';


        /*
        |--------------------------------------------------------------------------
        | FORM ACTION
        |--------------------------------------------------------------------------
        */

        holidayForm.action =
            updateHolidayBaseUrl + '/' + encodeURIComponent(id);


        /*
        |--------------------------------------------------------------------------
        | METHOD
        |--------------------------------------------------------------------------
        */

        document.getElementById('holiday_form_method').value =
            'PUT';


        /*
        |--------------------------------------------------------------------------
        | FIELDS
        |--------------------------------------------------------------------------
        */

        document.getElementById('holiday_id').value =
            id;

        document.getElementById('holiday_name').value =
            name;

        document.getElementById('holiday_date').value =
            date;

        document.getElementById('calendar_id').value =
            calendarId;

        document.getElementById('scope').value =
            scope;

        document.getElementById('holiday_is_active').checked =
            Number(isActive) === 1;


        /*
        |--------------------------------------------------------------------------
        | SHOW MODAL
        |--------------------------------------------------------------------------
        */

        holidayModal.classList.remove('hidden');

        document.body.classList.add('overflow-hidden');


        setTimeout(function() {

            document
                .getElementById('holiday_name')
                .focus();

        }, 100);
    }


    /*
    |--------------------------------------------------------------------------
    | CLOSE MODAL
    |--------------------------------------------------------------------------
    */

    function closeHolidayModal() {

        holidayModal.classList.add('hidden');

        document.body.classList.remove('overflow-hidden');
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE CONFIRMATION
    |--------------------------------------------------------------------------
    */

    function confirmDeleteHoliday(event) {

        const confirmed = confirm(
            'Are you sure you want to delete this holiday?'
        );

        if (!confirmed) {

            event.preventDefault();

            return false;
        }

        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | CLOSE FLASH MESSAGE
    |--------------------------------------------------------------------------
    */

    function closeHolidayMessage() {

        const container =
            document.getElementById(
                'holiday-message-container'
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


    /*
    |--------------------------------------------------------------------------
    | ESCAPE KEY
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'keydown',
        function(event) {

            if (
                event.key === 'Escape' &&
                !holidayModal.classList.contains('hidden')
            ) {

                closeHolidayModal();

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | AUTO CLOSE SUCCESS MESSAGE
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

        }
    );


    /*
    |--------------------------------------------------------------------------
    | CLICK OUTSIDE MODAL
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'click',
        function(event) {

            if (
                event.target === holidayModal
            ) {

                closeHolidayModal();

            }

        }
    );
    </script>

</x-app-layout>