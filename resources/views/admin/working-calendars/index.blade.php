<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ $title ?? __('Working Calendar Master') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

                {{-- Header --}}
                <div class="border-b border-slate-200 bg-slate-50 px-6 py-6">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div class="min-w-0">
                            <p
                                class="mb-2 inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.24em] text-emerald-800">
                                Working Calendar
                            </p>
                            <h3 class="text-2xl font-semibold tracking-tight text-slate-900">
                                Working Calendars
                            </h3>
                            <p class="mt-2 max-w-2xl text-sm text-slate-600">
                                {{ $description ?? 'Manage organisation working calendars and calendar configurations.' }}
                            </p>
                        </div>

                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                            <div class="relative w-full min-w-[220px] sm:w-auto">
                                <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
                                    fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" />
                                </svg>
                                <input id="calendar-search" type="text" placeholder="Search calendars"
                                    class="w-full rounded-2xl border border-slate-200 bg-white py-2.5 pl-10 pr-4 text-sm text-slate-900 shadow-sm outline-none transition focus:border-emerald-400 focus:ring-2 focus:ring-emerald-100" />
                            </div>
                            <button type="button" onclick="openCalendarModal()"
                                class="inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">
                                Add New
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Export --}}
                <div class="border-b border-slate-200 bg-slate-50 px-6 py-4">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="text-sm text-slate-600">
                            Export working calendar data for reporting or offline review.
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" onclick="exportCalendarTable('csv')"
                                class="rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                                CSV
                            </button>

                            <button type="button" onclick="exportCalendarTable('xlsx')"
                                class="rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                                XLSX
                            </button>

                            <button type="button" onclick="exportCalendarTable('pdf')"
                                class="rounded-2xl border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">
                                PDF
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Messages --}}
                @if(session('success') || session('error'))
                <div class="px-5 py-4" id="calendar-message-container">
                    <div id="calendar-message" class="relative rounded-2xl px-4 py-3 text-sm font-semibold shadow-sm
                            {{ session('success')
                                ? 'bg-emerald-50 text-emerald-700'
                                : 'bg-rose-50 text-rose-700' }}">

                        <span>
                            {{ session('success') ?? session('error') }}
                        </span>

                        <button type="button" onclick="closeCalendarMessage()"
                            class="absolute right-3 top-3 rounded-full bg-white/80 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-white">
                            Close
                        </button>

                    </div>
                </div>
                @endif

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <div class="max-h-[560px] overflow-auto">

                        <table class="min-w-full divide-y divide-slate-200 text-sm">

                            <thead class="sticky top-0 z-10 bg-slate-50">
                                <tr>
                                    <th
                                        class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">
                                        Calendar Code
                                    </th>
                                    <th
                                        class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">
                                        Calendar Name
                                    </th>
                                    <th
                                        class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">
                                        Organisation
                                    </th>
                                    <th
                                        class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">
                                        Timezone
                                    </th>
                                    <th
                                        class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">
                                        Status
                                    </th>
                                    <th
                                        class="whitespace-nowrap px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.24em] text-slate-500">
                                        Actions
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-slate-200 bg-white">

                                @forelse($calendars as $calendar)

                                <tr>

                                    <td class="px-5 py-4 font-semibold text-slate-900">
                                        {{ $calendar->calendar_code }}
                                    </td>
                                    <td class="px-5 py-4 text-slate-700">
                                        {{ $calendar->calendar_name }}
                                    </td>
                                    <td class="px-5 py-4 text-slate-600">
                                        {{ $calendar->organisation_name ?? $calendar->organisation_id ?? '-' }}
                                    </td>
                                    <td class="px-5 py-4 text-slate-600">
                                        {{ $calendar->timezone ?? '-' }}
                                    </td>
                                    <td class="px-5 py-4">
                                        <span
                                            class="inline-flex rounded-full px-3 py-1 text-xs font-semibold tracking-[0.18em] uppercase
                                            {{ (int)$calendar->is_active === 1 ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                            {{ (int)$calendar->is_active === 1 ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <a href="{{ route('working-calendars.show', $calendar->calendar_id) }}"
                                                class="inline-flex items-center rounded-full border border-slate-200 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50">
                                                View
                                            </a>
                                            <button type="button" data-calendar-id="{{ $calendar->calendar_id }}"
                                                data-calendar-code="{{ $calendar->calendar_code }}"
                                                data-calendar-name="{{ $calendar->calendar_name }}"
                                                data-organisation-id="{{ $calendar->organisation_id ?? '' }}"
                                                data-timezone="{{ $calendar->timezone ?? '' }}"
                                                onclick="editCalendar(this.dataset)"
                                                class="inline-flex items-center rounded-full bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white transition hover:bg-slate-800">
                                                Edit
                                            </button>
                                            <form method="POST"
                                                action="{{ route('working-calendars.toggle', $calendar->calendar_id) }}"
                                                class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="inline-flex items-center rounded-full px-3 py-1.5 text-xs font-semibold transition
                                                        {{ (int)$calendar->is_active === 1 ? 'border border-rose-200 bg-rose-50 text-rose-700 hover:bg-rose-100' : 'border border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                                                    {{ (int)$calendar->is_active === 1 ? 'Disable' : 'Activate' }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>

                                </tr>

                                @empty

                                <tr class="empty-row">

                                    <td colspan="6" class="px-5 py-8 text-center text-sm text-slate-500">

                                        No working calendars found.

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

    {{-- Calendar Modal --}}
    <div id="calendar-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-slate-900/60 px-4 py-8">
        <div class="w-full max-w-2xl overflow-hidden rounded-3xl bg-white shadow-2xl ring-1 ring-slate-200">
            <form id="calendar-form" method="POST" action="">
                @csrf
                <div class="flex items-start justify-between border-b border-slate-200 px-6 py-5">
                    <div>
                        <h3 id="calendar-modal-title" class="text-xl font-semibold text-slate-900">
                            Add Working Calendar
                        </h3>
                        <p class="mt-1 text-sm text-slate-600">Create or update a working calendar.</p>
                    </div>
                    <button type="button" onclick="closeCalendarModal()"
                        class="rounded-full bg-slate-100 p-2 text-slate-700 transition hover:bg-slate-200">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <input type="hidden" id="calendar_id" name="calendar_id">
                <input type="hidden" id="calendar_form_method" name="_method" value="POST">

                <div class="space-y-6 px-6 py-6">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">Calendar Code</label>
                            <input id="calendar_code" name="calendar_code" type="text" required maxlength="50"
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-emerald-400 focus:bg-white focus:ring-2 focus:ring-emerald-100"
                                placeholder="e.g. INDIA-GENERAL">
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">Calendar Name</label>
                            <input id="calendar_name" name="calendar_name" type="text" required maxlength="150"
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-emerald-400 focus:bg-white focus:ring-2 focus:ring-emerald-100"
                                placeholder="Enter calendar name">
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">Organisation</label>
                            <select id="organisation_id" name="organisation_id"
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-emerald-400 focus:bg-white focus:ring-2 focus:ring-emerald-100">
                                <option value="">Select Organisation</option>
                                @foreach($organisations ?? [] as $organisation)
                                <option value="{{ $organisation->organisation_id }}">
                                    {{ $organisation->organisation_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700">Timezone</label>
                            <select id="timezone" name="timezone"
                                class="w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-emerald-400 focus:bg-white focus:ring-2 focus:ring-emerald-100">
                                <option value="">Select Timezone</option>
                                <option value="Asia/Kolkata">Asia/Kolkata</option>
                                <option value="UTC">UTC</option>
                                <option value="Asia/Dubai">Asia/Dubai</option>
                                <option value="Asia/Singapore">Asia/Singapore</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 border-t border-slate-200 pt-4 sm:flex-row sm:justify-end">
                        <button type="button" onclick="closeCalendarModal()"
                            class="rounded-2xl border border-slate-300 bg-slate-100 px-5 py-2 text-sm font-semibold text-slate-700 transition hover:bg-slate-200">
                            Cancel
                        </button>
                        <button id="calendar-modal-submit" type="submit"
                            class="rounded-2xl bg-emerald-600 px-5 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">
                            Save
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
    function openCalendarModal() {
        document.getElementById('calendar-modal-title').textContent = 'Add Working Calendar';
        document.getElementById('calendar-form').action = "{{ route('working-calendars.store') }}";
        document.getElementById('calendar_form_method').value = 'POST';
        document.getElementById('calendar_id').value = '';
        document.getElementById('calendar_code').value = '';
        document.getElementById('calendar_name').value = '';
        document.getElementById('organisation_id').value = '';
        document.getElementById('timezone').value = 'Asia/Kolkata';
        document.getElementById('calendar-modal-submit').textContent = 'Save';
        document.getElementById('calendar-modal').classList.remove('hidden');
    }

    function closeCalendarModal() {
        document.getElementById('calendar-modal').classList.add('hidden');
    }

    function editCalendar(calendar) {
        document.getElementById('calendar-modal-title').textContent = 'Edit Working Calendar';
        document.getElementById('calendar-form').action = "{{ url('working-calendars') }}/" + calendar.calendarId;
        document.getElementById('calendar_form_method').value = 'PUT';
        document.getElementById('calendar_id').value = calendar.calendarId;
        document.getElementById('calendar_code').value = calendar.calendarCode;
        document.getElementById('calendar_name').value = calendar.calendarName;
        document.getElementById('organisation_id').value = calendar.organisationId || '';
        document.getElementById('timezone').value = calendar.timezone || '';
        document.getElementById('calendar-modal-submit').textContent = 'Update';
        document.getElementById('calendar-modal').classList.remove('hidden');
    }

    function exportCalendarTable(format) {
        const params = new URLSearchParams({
            format: format
        });
        window.location.href = "{{ route('working-calendars.index') }}?" + params.toString();
    }

    function closeCalendarMessage() {
        const container = document.getElementById('calendar-message-container');
        if (container) {
            container.style.transition = 'opacity 0.4s ease';
            container.style.opacity = '0';
            setTimeout(() => container.remove(), 400);
        }
    }

    function filterCalendars() {
        const query = document.getElementById('calendar-search').value.trim().toLowerCase();
        const rows = document.querySelectorAll('tbody tr');
        let visibleCount = 0;
        rows.forEach(row => {
            if (row.classList.contains('empty-row')) {
                return;
            }
            const match = query === '' || row.textContent.toLowerCase().includes(query);
            row.classList.toggle('hidden', !match);
            if (match) {
                visibleCount++;
            }
        });
        const emptyRow = document.querySelector('tbody .empty-row');
        if (emptyRow) {
            emptyRow.classList.toggle('hidden', visibleCount > 0);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const message = document.getElementById('calendar-message');
        if (message) {
            setTimeout(closeCalendarMessage, 10000);
        }
        const search = document.getElementById('calendar-search');
        if (search) {
            search.addEventListener('input', filterCalendars);
        }
    });
    </script>

</x-app-layout>