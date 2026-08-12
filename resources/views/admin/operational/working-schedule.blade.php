<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-1">
            <h2 class="text-xl font-bold tracking-tight text-slate-800">Working Schedule</h2>
            <p class="text-sm text-slate-500">Manage schedules for {{ $calendar->calendar_name }} ({{ $calendar->calendar_code }}).</p>
        </div>
    </x-slot>

    <div class="min-h-screen bg-slate-50 py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 bg-white px-5 py-5">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <h3 class="text-base font-semibold text-slate-800">Schedule Configuration</h3>
                            <p class="mt-1 text-sm text-slate-500">Create or update daily schedules used by SLA and calendar calculations.</p>
                        </div>

                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                            <div class="flex h-10 w-full items-center rounded-xl border border-slate-300 bg-white px-3 transition focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-500/20 sm:w-72">
                                <svg class="h-4 w-4 shrink-0 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m2.35-5.65a8 8 0 1 1-16 0 8 8 0 0 1 16 0Z" />
                                </svg>
                                <input id="schedule-search" type="search" autocomplete="off" placeholder="Search schedules..." class="ml-2 w-full border-0 bg-transparent text-sm text-slate-700 outline-none placeholder:text-slate-400 focus:ring-0">
                            </div>

                            <div class="flex items-center gap-3">
                                <a href="{{ route('working.calendars') }}" class="inline-flex h-10 items-center justify-center rounded-xl border border-slate-200 bg-slate-100 px-4 text-sm font-semibold text-slate-700 hover:border-slate-300 hover:bg-slate-200">Back to Calendars</a>
                                @if(data_get($permissions, 'create'))
                                    <button type="button" onclick="openScheduleModal()" class="inline-flex h-10 items-center justify-center gap-2 rounded-xl bg-indigo-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14" />
                                        </svg>
                                        Add Schedule
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                @if(session('success') || session('error'))
                    <div class="px-5 pt-5">
                        <div class="relative flex items-start gap-3 rounded-xl border px-4 py-3 text-sm {{ session('success') ? 'border-emerald-200 bg-emerald-50 text-emerald-800' : 'border-rose-200 bg-rose-50 text-rose-800' }}">
                            <svg class="mt-0.5 h-5 w-5 shrink-0 {{ session('success') ? 'text-emerald-600' : 'text-rose-600' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ session('success') ? 'm5 12 4 4L19 6' : 'M12 9v4m0 4h.01M10.3 3.6 2.9 17a2 2 0 0 0 1.75 3h14.7a2 2 0 0 0 1.75-3L13.7 3.6a2 2 0 0 0-3.4 0Z' }}" />
                            </svg>
                            <span class="pr-8 font-medium">{{ session('success') ?? session('error') }}</span>
                            <button type="button" onclick="closeScheduleMessage()" class="absolute right-3 top-3 rounded-lg p-1 hover:bg-white/80">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <div class="max-h-[580px] overflow-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="sticky top-0 z-10 bg-slate-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">#</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Day</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Shift</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">From - To</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Break</th>
                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Status</th>
                                    <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="schedule-table-body" class="divide-y divide-slate-100 bg-white">
                                @php $start = method_exists($schedules, 'firstItem') ? ($schedules->firstItem() ?? 1) : 1; @endphp
                                @forelse($schedules as $index => $schedule)
                                    <tr class="schedule-row" data-search="{{ strtolower($schedule->day_of_week . ' ' . $schedule->schedule_name . ' ' . $schedule->shift_name) }}">
                                        <td class="px-6 py-4 text-sm font-semibold text-slate-700">{{ $start + $index }}</td>
                                        <td class="px-6 py-4 text-sm text-slate-700">{{ ucfirst(strtolower($schedule->day_of_week)) }}</td>
                                        <td class="px-6 py-4 text-sm text-slate-700">{{ $schedule->shift_name ?: '-' }}</td>
                                        <td class="px-6 py-4 text-sm text-slate-700">{{ $schedule->is_working_day ? ($schedule->is_24_hours ? '24 Hours' : ($schedule->start_time && $schedule->end_time ? $schedule->start_time . ' - ' . $schedule->end_time : '-')) : 'Off' }}</td>
                                        <td class="px-6 py-4 text-sm text-slate-700">{{ $schedule->break_start && $schedule->break_end ? $schedule->break_start . ' - ' . $schedule->break_end : '-' }}</td>
                                        <td class="px-6 py-4 text-sm">
                                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $schedule->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">{{ $schedule->is_active ? 'Active' : 'Inactive' }}</span>
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm font-medium">
                                            <div class="inline-flex gap-2">
                                                @if(data_get($permissions, 'edit'))
                                                    <button type="button" onclick="editSchedule({{ json_encode($schedule) }})" class="rounded-lg bg-slate-900 px-3 py-1 text-xs font-semibold text-white hover:bg-slate-800">Edit</button>
                                                @endif
                                                @if(data_get($permissions, 'deactivate') || data_get($permissions, 'activate'))
                                                    <form method="POST" action="{{ route('working.calendars.schedules.toggle', ['calendar_id' => $calendar->calendar_id, 'schedule_id' => $schedule->schedule_id]) }}" class="inline">
                                                        @csrf
                                                        <button type="submit" class="rounded-lg bg-amber-50 px-3 py-1 text-xs font-semibold text-amber-700 hover:bg-amber-100">{{ $schedule->is_active ? 'Disable' : 'Activate' }}</button>
                                                    </form>
                                                @endif
                                                @if(data_get($permissions, 'delete'))
                                                    <form method="POST" action="{{ route('working.calendars.schedules.destroy', ['calendar_id' => $calendar->calendar_id, 'schedule_id' => $schedule->schedule_id]) }}" class="inline" onsubmit="return confirm('Delete schedule?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="rounded-lg bg-rose-50 px-3 py-1 text-xs font-semibold text-rose-700 hover:bg-rose-100">Delete</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-14 text-center text-sm text-slate-500">No schedules defined for this calendar yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="border-t border-slate-200 bg-slate-50 px-5 py-4">
                    {{ $schedules->links() }}
                </div>
            </div>
        </div>
    </div>

    <div id="schedule-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeScheduleModal()"></div>
        <div class="relative flex min-h-full items-center justify-center p-4">
            <div class="relative w-full max-w-3xl overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl">
                <form id="schedule-form" method="POST" action="{{ route('working.calendars.schedules.store', ['calendar_id' => $calendar->calendar_id]) }}">
                    @csrf
                    <input type="hidden" id="schedule_id" name="schedule_id" value="" />
                    <input type="hidden" id="schedule_form_method" name="_method" value="POST" />
                    <div class="flex items-start justify-between border-b border-slate-200 px-6 py-5">
                        <div>
                            <h3 id="schedule-modal-title" class="text-lg font-bold text-slate-800">Add Schedule</h3>
                            <p class="mt-1 text-sm text-slate-500">Define day-level working hours and off-day rules.</p>
                        </div>
                        <button type="button" onclick="closeScheduleModal()" class="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="space-y-5 px-6 py-6">
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-sm font-semibold text-slate-700">Day of Week</label>
                                <select id="day_of_week" name="day_of_week" required class="block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20">
                                    <option value="">Select a day</option>
                                    @foreach($daysOfWeek as $day)
                                        <option value="{{ $day }}">{{ ucfirst(strtolower($day)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-semibold text-slate-700">Schedule Name</label>
                                <input id="schedule_name" name="schedule_name" type="text" required maxlength="100" class="block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20" placeholder="General Shift" />
                            </div>
                        </div>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-sm font-semibold text-slate-700">Sequence No.</label>
                                <input id="sequence_no" name="sequence_no" type="number" min="1" value="1" required class="block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20" />
                            </div>
                            <div class="flex items-center gap-4">
                                <label class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                                    <input id="is_working_day" name="is_working_day" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" />
                                    Working Day
                                </label>
                                <label class="flex items-center gap-2 text-sm font-semibold text-slate-700">
                                    <input id="is_24_hours" name="is_24_hours" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" />
                                    24 Hours
                                </label>
                            </div>
                        </div>
                        <div class="grid gap-4 md:grid-cols-3">
                            <div>
                                <label class="mb-1.5 block text-sm font-semibold text-slate-700">Start Time</label>
                                <input id="start_time" name="start_time" type="time" class="block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20" />
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-semibold text-slate-700">End Time</label>
                                <input id="end_time" name="end_time" type="time" class="block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20" />
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-semibold text-slate-700">Active</label>
                                <label class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-700">
                                    <input id="is_active" name="is_active" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500" checked />
                                    Active
                                </label>
                            </div>
                        </div>
                        <div class="grid gap-4 md:grid-cols-3">
                            <div>
                                <label class="mb-1.5 block text-sm font-semibold text-slate-700">Break Start</label>
                                <input id="break_start" name="break_start" type="time" class="block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20" />
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-semibold text-slate-700">Break End</label>
                                <input id="break_end" name="break_end" type="time" class="block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20" />
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-semibold text-slate-700">Shift Name</label>
                                <input id="shift_name" name="shift_name" type="text" maxlength="50" class="block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20" placeholder="General" />
                            </div>
                        </div>
                        <div class="grid gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-1.5 block text-sm font-semibold text-slate-700">Effective From</label>
                                <input id="effective_from" name="effective_from" type="date" class="block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20" />
                            </div>
                            <div>
                                <label class="mb-1.5 block text-sm font-semibold text-slate-700">Effective To</label>
                                <input id="effective_to" name="effective_to" type="date" class="block w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-800 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20" />
                            </div>
                        </div>
                    </div>
                    <div class="flex flex-col gap-3 border-t border-slate-200 px-6 py-5 sm:flex-row sm:justify-end">
                        <button type="button" onclick="closeScheduleModal()" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">Cancel</button>
                        <button type="reset" onclick="resetScheduleForm()" class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-white px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50">Reset</button>
                        <button type="submit" id="schedule-modal-submit" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white hover:bg-indigo-700">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const scheduleModal = document.getElementById('schedule-modal');
        const scheduleForm = document.getElementById('schedule-form');

        function openScheduleModal() {
            resetScheduleForm();
            scheduleModal.classList.remove('hidden');
            scheduleModal.classList.add('flex');
            document.getElementById('schedule-modal-title').textContent = 'Add Schedule';
            document.getElementById('schedule-modal-submit').textContent = 'Save';
            document.getElementById('schedule_form_method').value = 'POST';
            scheduleForm.action = '{{ route('working.calendars.schedules.store', ['calendar_id' => $calendar->calendar_id]) }}';
        }

        function closeScheduleModal() {
            scheduleModal.classList.add('hidden');
            scheduleModal.classList.remove('flex');
        }

        function resetScheduleForm() {
            scheduleForm.reset();
            document.getElementById('schedule_id').value = '';
            document.getElementById('is_active').checked = true;
            document.getElementById('sequence_no').value = 1;
        }

        function editSchedule(schedule) {
            openScheduleModal();
            document.getElementById('schedule-modal-title').textContent = 'Edit Schedule';
            document.getElementById('schedule-modal-submit').textContent = 'Update';
            document.getElementById('schedule_id').value = schedule.schedule_id;
            document.getElementById('day_of_week').value = schedule.day_of_week;
            document.getElementById('schedule_name').value = schedule.schedule_name;
            document.getElementById('sequence_no').value = schedule.sequence_no || 1;
            document.getElementById('is_working_day').checked = schedule.is_working_day;
            document.getElementById('is_24_hours').checked = schedule.is_24_hours;
            document.getElementById('start_time').value = schedule.start_time || '';
            document.getElementById('end_time').value = schedule.end_time || '';
            document.getElementById('break_start').value = schedule.break_start || '';
            document.getElementById('break_end').value = schedule.break_end || '';
            document.getElementById('shift_name').value = schedule.shift_name || '';
            document.getElementById('effective_from').value = schedule.effective_from || '';
            document.getElementById('effective_to').value = schedule.effective_to || '';
            document.getElementById('is_active').checked = schedule.is_active;
            document.getElementById('schedule_form_method').value = 'PUT';
            scheduleForm.action = '{{ url('/working-calendars/'.$calendar->calendar_id.'/schedules') }}/' + schedule.schedule_id;
        }

        function closeScheduleMessage() {
            const msg = document.querySelector('#schedule-modal + div .relative');
            if (msg) msg.remove();
        }
    </script>
</x-app-layout>
