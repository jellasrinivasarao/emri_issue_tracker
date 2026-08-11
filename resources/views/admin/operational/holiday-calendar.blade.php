<x-app-layout>

    {{-- =========================================================
        PAGE HEADER
    ========================================================== --}}
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="text-xl font-bold tracking-tight text-slate-800">
                Holiday Calendar
            </h2>

            <p class="text-sm text-slate-500">
                Manage public and organization-specific holidays.
            </p>
        </div>
    </x-slot>


    @php
    $holidays = $holidays ?? collect();
    $permissions = $permissions ?? [];

    $canCreate = data_get($permissions, 'create', true);
    $canEdit = data_get($permissions, 'edit', true);
    $canDelete = data_get($permissions, 'delete', true);
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
                                Holiday List
                            </h3>

                            <p class="mt-1 text-sm text-slate-500">
                                Configure holidays used by the working calendar and routing engine.
                            </p>
                        </div>


                        {{-- ACTIONS --}}
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">

                            {{-- SEARCH --}}
                            <div class="flex h-10 w-full items-center rounded-xl border border-slate-300 bg-white px-3
                                       transition focus-within:border-indigo-500 focus-within:ring-2
                                       focus-within:ring-indigo-500/20 sm:w-72">

                                <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" stroke="currentColor"
                                    stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z" />
                                </svg>

                                <input id="holiday-search" type="search" autocomplete="off"
                                    placeholder="Search holidays..." class="ml-2 w-full border-0 bg-transparent text-sm text-slate-700
                                           outline-none placeholder:text-slate-400
                                           focus:ring-0" />
                            </div>


                            {{-- ADD BUTTON --}}
                            @if($canCreate)

                            <button type="button" onclick="openHolidayModal()" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl
                                           bg-indigo-600 px-4 text-sm font-semibold text-white shadow-sm
                                           transition hover:bg-indigo-700 focus:outline-none focus:ring-2
                                           focus:ring-indigo-500 focus:ring-offset-2">

                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14" />
                                </svg>

                                Add Holiday
                            </button>

                            @endif

                        </div>

                    </div>
                </div>


                {{-- =================================================
                    TABLE
                ================================================== --}}
                <div class="overflow-x-auto">

                    <table class="min-w-full divide-y divide-slate-200">

                        {{-- TABLE HEADER --}}
                        <thead class="bg-slate-50">

                            <tr>

                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase
                                           tracking-wider text-slate-500">
                                    Date
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase
                                           tracking-wider text-slate-500">
                                    Holiday
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase
                                           tracking-wider text-slate-500">
                                    Scope
                                </th>

                                <th class="px-6 py-3 text-left text-xs font-semibold uppercase
                                           tracking-wider text-slate-500">
                                    Status
                                </th>

                                <th class="px-6 py-3 text-right text-xs font-semibold uppercase
                                           tracking-wider text-slate-500">
                                    Actions
                                </th>

                            </tr>

                        </thead>


                        {{-- TABLE BODY --}}
                        <tbody id="holiday-table-body" class="divide-y divide-slate-100 bg-white">

                            @forelse($holidays as $holiday)

                            <tr class="holiday-row transition hover:bg-slate-50" data-search="{{ strtolower(
                                        ($holiday->holiday_date ?? '') . ' ' .
                                        ($holiday->holiday_name ?? '') . ' ' .
                                        ($holiday->scope ?? '')
                                    ) }}">

                                {{-- DATE --}}
                                <td class="whitespace-nowrap px-6 py-4">

                                    <div class="flex items-center gap-3">

                                        <div class="flex h-9 w-9 items-center justify-center
                                                       rounded-lg bg-indigo-50 text-indigo-600">
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M8 2v4m8-4v4M3 10h18M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" />
                                            </svg>
                                        </div>

                                        <div>
                                            <p class="text-sm font-medium text-slate-800">
                                                {{ $holiday->holiday_date ?? '-' }}
                                            </p>
                                        </div>

                                    </div>

                                </td>


                                {{-- HOLIDAY --}}
                                <td class="px-6 py-4">

                                    <div>
                                        <p class="text-sm font-semibold text-slate-800">
                                            {{ $holiday->holiday_name ?? '-' }}
                                        </p>

                                        @if(!empty($holiday->description))
                                        <p class="mt-0.5 max-w-md truncate text-xs text-slate-500">
                                            {{ $holiday->description }}
                                        </p>
                                        @endif
                                    </div>

                                </td>


                                {{-- SCOPE --}}
                                <td class="px-6 py-4">

                                    @php
                                    $scope = $holiday->scope ?? 'Global';
                                    @endphp

                                    <span class="inline-flex items-center rounded-lg border
                                                   border-slate-200 bg-slate-50 px-2.5 py-1
                                                   text-xs font-medium text-slate-600">
                                        {{ $scope }}
                                    </span>

                                </td>


                                {{-- STATUS --}}
                                <td class="px-6 py-4">

                                    @if((int) ($holiday->is_active ?? 1) === 1)

                                    <span class="inline-flex items-center gap-1.5 rounded-full
                                                       bg-emerald-50 px-2.5 py-1 text-xs
                                                       font-semibold text-emerald-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>

                                        Active
                                    </span>

                                    @else

                                    <span class="inline-flex items-center gap-1.5 rounded-full
                                                       bg-slate-100 px-2.5 py-1 text-xs
                                                       font-semibold text-slate-600">
                                        <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>

                                        Inactive
                                    </span>

                                    @endif

                                </td>


                                {{-- ACTIONS --}}
                                <td class="px-6 py-4">

                                    <div class="flex justify-end gap-2">

                                        {{-- EDIT --}}
                                        @if($canEdit)

                                        <button type="button" onclick='editHoliday(@json($holiday))'
                                            title="Edit Holiday" class="inline-flex h-8 items-center gap-1.5 rounded-lg
                                                           border border-slate-200 bg-white px-3
                                                           text-xs font-semibold text-slate-700
                                                           transition hover:border-indigo-200
                                                           hover:bg-indigo-50 hover:text-indigo-700">

                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M12 20h9M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z" />
                                            </svg>

                                            Edit

                                        </button>

                                        @endif


                                        {{-- DELETE --}}
                                        @if($canDelete)

                                        <form method="POST"
                                            action="{{ route('holiday.calendar.destroy', $holiday->holiday_id) }}"
                                            onsubmit="return confirmDeleteHoliday(event)">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" title="Delete Holiday" class="inline-flex h-8 items-center gap-1.5 rounded-lg
                                                               border border-rose-200 bg-rose-50 px-3
                                                               text-xs font-semibold text-rose-700
                                                               transition hover:bg-rose-100">

                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor"
                                                    stroke-width="2" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M3 6h18M8 6V4h8v2m-9 0 1 14h8l1-14" />
                                                </svg>

                                                Delete

                                            </button>

                                        </form>

                                        @endif

                                    </div>

                                </td>

                            </tr>

                            @empty

                            <tr id="holiday-empty-row">

                                <td colspan="5" class="px-6 py-14 text-center">

                                    <div class="flex flex-col items-center">

                                        <div class="flex h-14 w-14 items-center justify-center
                                                       rounded-full bg-slate-100">
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
                                            Add your first holiday to the working calendar.
                                        </p>

                                    </div>

                                </td>

                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>


                {{-- =================================================
                    FOOTER
                ================================================== --}}
                @if($holidays->count() > 0)

                <div class="border-t border-slate-200 bg-slate-50 px-6 py-3">
                    <p class="text-xs text-slate-500">
                        Total holidays:
                        <span class="font-semibold text-slate-700">
                            {{ $holidays->count() }}
                        </span>
                    </p>
                </div>

                @endif

            </div>

        </div>

    </div>


    {{-- =========================================================
        HOLIDAY MODAL
    ========================================================== --}}
    <div id="holiday-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="holiday-modal-title"
        aria-modal="true" role="dialog">

        {{-- BACKDROP --}}
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeHolidayModal()"></div>


        {{-- MODAL WRAPPER --}}
        <div class="relative flex min-h-full items-center justify-center p-4">

            <div class="relative w-full max-w-2xl overflow-hidden rounded-2xl
                       border border-slate-200 bg-white shadow-2xl">

                <form id="holiday-form" method="POST" action="{{ route('holiday.calendar.store') }}">

                    @csrf

                    {{-- =============================================
                        MODAL HEADER
                    ============================================== --}}
                    <div class="flex items-start justify-between border-b
                               border-slate-200 px-6 py-5">

                        <div>

                            <div class="flex items-center gap-3">

                                <div class="flex h-10 w-10 items-center justify-center
                                           rounded-xl bg-indigo-50 text-indigo-600">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M8 2v4m8-4v4M3 10h18M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z" />
                                    </svg>
                                </div>

                                <div>

                                    <h3 id="holiday-modal-title" class="text-lg font-bold text-slate-800">
                                        Add Holiday
                                    </h3>

                                    <p class="mt-0.5 text-sm text-slate-500">
                                        Create or update a holiday calendar entry.
                                    </p>

                                </div>

                            </div>

                        </div>


                        {{-- CLOSE --}}
                        <button type="button" onclick="closeHolidayModal()" class="rounded-lg p-2 text-slate-400 transition
                                   hover:bg-slate-100 hover:text-slate-700">

                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>

                        </button>

                    </div>


                    {{-- =============================================
                        HIDDEN FIELDS
                    ============================================== --}}
                    <input type="hidden" id="holiday_id" name="holiday_id">

                    <input type="hidden" id="holiday_form_method" name="_method" value="POST">


                    {{-- =============================================
                        FORM BODY
                    ============================================== --}}
                    <div class="space-y-5 px-6 py-6">

                        {{-- HOLIDAY NAME --}}
                        <div>

                            <label for="holiday_name" class="mb-1.5 block text-sm font-semibold text-slate-700">
                                Holiday Name
                                <span class="text-rose-500">*</span>
                            </label>

                            <input id="holiday_name" name="holiday_name" type="text" required maxlength="150"
                                placeholder="e.g. Independence Day" class="block w-full rounded-xl border border-slate-300
                                       bg-white px-3.5 py-2.5 text-sm text-slate-800
                                       shadow-sm outline-none transition
                                       placeholder:text-slate-400
                                       focus:border-indigo-500 focus:ring-2
                                       focus:ring-indigo-500/20">

                        </div>


                        {{-- DATE + SCOPE --}}
                        <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                            {{-- DATE --}}
                            <div>

                                <label for="holiday_date" class="mb-1.5 block text-sm font-semibold text-slate-700">
                                    Holiday Date
                                    <span class="text-rose-500">*</span>
                                </label>

                                <input id="holiday_date" name="holiday_date" type="date" required class="block w-full rounded-xl border border-slate-300
                                           bg-white px-3.5 py-2.5 text-sm text-slate-800
                                           shadow-sm outline-none transition
                                           focus:border-indigo-500 focus:ring-2
                                           focus:ring-indigo-500/20">

                            </div>


                            {{-- SCOPE --}}
                            <div>

                                <label for="holiday_scope" class="mb-1.5 block text-sm font-semibold text-slate-700">
                                    Scope
                                </label>

                                <select id="holiday_scope" name="scope" class="block w-full rounded-xl border border-slate-300
                                           bg-white px-3.5 py-2.5 text-sm text-slate-800
                                           shadow-sm outline-none transition
                                           focus:border-indigo-500 focus:ring-2
                                           focus:ring-indigo-500/20">
                                    <option value="Global">
                                        Global
                                    </option>

                                    <option value="Organisation">
                                        Organisation
                                    </option>
                                </select>

                            </div>

                        </div>


                        {{-- DESCRIPTION --}}
                        <div>

                            <label for="holiday_description" class="mb-1.5 block text-sm font-semibold text-slate-700">
                                Description
                            </label>

                            <textarea id="holiday_description" name="description" rows="3" maxlength="500"
                                placeholder="Optional description..." class="block w-full resize-none rounded-xl border
                                       border-slate-300 bg-white px-3.5 py-2.5
                                       text-sm text-slate-800 shadow-sm outline-none
                                       transition placeholder:text-slate-400
                                       focus:border-indigo-500 focus:ring-2
                                       focus:ring-indigo-500/20"></textarea>

                        </div>


                        {{-- ACTIVE STATUS --}}
                        <div class="rounded-xl border border-slate-200 bg-slate-50 p-4">

                            <label class="flex cursor-pointer items-center gap-3">

                                <input id="holiday_is_active" name="is_active" type="checkbox" value="1" checked class="h-4 w-4 rounded border-slate-300 text-indigo-600
                                           focus:ring-indigo-500">

                                <span>

                                    <span class="block text-sm font-semibold text-slate-700">
                                        Active Holiday
                                    </span>

                                    <span class="block text-xs text-slate-500">
                                        Active holidays will be considered by the working calendar.
                                    </span>

                                </span>

                            </label>

                        </div>

                    </div>


                    {{-- =============================================
                        MODAL FOOTER
                    ============================================== --}}
                    <div class="flex items-center justify-end gap-3 border-t
                               border-slate-200 bg-slate-50 px-6 py-4">

                        <button type="button" onclick="closeHolidayModal()" class="rounded-xl border border-slate-300 bg-white px-4
                                   py-2.5 text-sm font-semibold text-slate-700
                                   transition hover:bg-slate-100">
                            Cancel
                        </button>

                        <button id="holiday-modal-submit" type="submit" class="inline-flex items-center justify-center rounded-xl
                                   bg-indigo-600 px-5 py-2.5 text-sm font-semibold
                                   text-white shadow-sm transition hover:bg-indigo-700
                                   focus:outline-none focus:ring-2 focus:ring-indigo-500
                                   focus:ring-offset-2">
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
    const holidayModal = document.getElementById('holiday-modal');
    const holidayForm = document.getElementById('holiday-form');

    const storeUrl = @json(route('holiday.calendar.store'));

    function openHolidayModal() {

        document.getElementById('holiday-modal-title').textContent =
            'Add Holiday';

        document.getElementById('holiday-modal-submit').textContent =
            'Save Holiday';

        holidayForm.action = storeUrl;

        document.getElementById('holiday_form_method').value = 'POST';

        document.getElementById('holiday_id').value = '';

        document.getElementById('holiday_name').value = '';

        document.getElementById('holiday_date').value = '';

        document.getElementById('holiday_scope').value = 'Global';

        document.getElementById('holiday_description').value = '';

        document.getElementById('holiday_is_active').checked = true;

        holidayModal.classList.remove('hidden');

        document.body.classList.add('overflow-hidden');

        setTimeout(() => {
            document.getElementById('holiday_name').focus();
        }, 100);
    }


    function closeHolidayModal() {

        holidayModal.classList.add('hidden');

        document.body.classList.remove('overflow-hidden');
    }


    function editHoliday(holiday) {

        document.getElementById('holiday-modal-title').textContent =
            'Edit Holiday';

        document.getElementById('holiday-modal-submit').textContent =
            'Update Holiday';

        holidayForm.action =
            @json(url('/holiday-calendar')) + '/' + holiday.holiday_id;

        document.getElementById('holiday_form_method').value = 'PUT';

        document.getElementById('holiday_id').value =
            holiday.holiday_id ?? '';

        document.getElementById('holiday_name').value =
            holiday.holiday_name ?? '';

        document.getElementById('holiday_date').value =
            holiday.holiday_date ?? '';

        document.getElementById('holiday_scope').value =
            holiday.scope ?? 'Global';

        document.getElementById('holiday_description').value =
            holiday.description ?? '';

        document.getElementById('holiday_is_active').checked =
            Number(holiday.is_active ?? 1) === 1;

        holidayModal.classList.remove('hidden');

        document.body.classList.add('overflow-hidden');

        setTimeout(() => {
            document.getElementById('holiday_name').focus();
        }, 100);
    }


    function confirmDeleteHoliday(event) {

        if (!confirm(
                'Are you sure you want to delete this holiday?'
            )) {

            event.preventDefault();

            return false;
        }

        return true;
    }


    // =========================================================
    // SEARCH
    // =========================================================

    document.addEventListener('DOMContentLoaded', function() {

        const searchInput =
            document.getElementById('holiday-search');

        if (!searchInput) {
            return;
        }

        searchInput.addEventListener('input', function() {

            const search =
                this.value.trim().toLowerCase();

            const rows =
                document.querySelectorAll('.holiday-row');

            let visibleCount = 0;

            rows.forEach(function(row) {

                const content =
                    row.dataset.search || '';

                const visible =
                    content.includes(search);

                row.classList.toggle(
                    'hidden',
                    !visible
                );

                if (visible) {
                    visibleCount++;
                }
            });

        });

    });


    // =========================================================
    // ESCAPE KEY
    // =========================================================

    document.addEventListener('keydown', function(event) {

        if (
            event.key === 'Escape' &&
            !holidayModal.classList.contains('hidden')
        ) {
            closeHolidayModal();
        }

    });
    </script>

</x-app-layout>