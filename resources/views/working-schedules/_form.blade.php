{{-- Validation Errors --}}
@if ($errors->any())

<div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">

    <div class="font-semibold">
        Please fix the following errors:
    </div>

    <ul class="mt-2 list-disc space-y-1 pl-5">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>

</div>

@endif


<div class="grid gap-6 md:grid-cols-2">

    {{-- Calendar --}}
    <div>
        <label for="calendar_id" class="mb-1 block text-sm font-medium text-slate-700">
            Calendar
        </label>

        <input type="number" id="calendar_id" name="calendar_id"
            value="{{ old('calendar_id', $workingSchedule->calendar_id ?? '') }}" required
            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-900 outline-none transition focus:border-purple-500 focus:ring-2 focus:ring-purple-100">

        @error('calendar_id')
        <p class="mt-1 text-xs text-rose-600">
            {{ $message }}
        </p>
        @enderror
    </div>


    {{-- Day --}}
    <div>
        <label for="day_of_week" class="mb-1 block text-sm font-medium text-slate-700">
            Day of Week
        </label>

        <select id="day_of_week" name="day_of_week" required
            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 outline-none transition focus:border-purple-500 focus:ring-2 focus:ring-purple-100">

            @foreach ([
            'MONDAY',
            'TUESDAY',
            'WEDNESDAY',
            'THURSDAY',
            'FRIDAY',
            'SATURDAY',
            'SUNDAY'
            ] as $day)

            <option value="{{ $day }}" @selected(old('day_of_week', $workingSchedule->day_of_week ?? '') === $day)
                >
                {{ $day }}
            </option>

            @endforeach

        </select>

        @error('day_of_week')
        <p class="mt-1 text-xs text-rose-600">
            {{ $message }}
        </p>
        @enderror
    </div>


    {{-- Schedule Name --}}
    <div>
        <label for="schedule_name" class="mb-1 block text-sm font-medium text-slate-700">
            Schedule Name
        </label>

        <input type="text" id="schedule_name" name="schedule_name"
            value="{{ old('schedule_name', $workingSchedule->schedule_name ?? '') }}" required
            placeholder="e.g. General Shift"
            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-900 outline-none transition focus:border-purple-500 focus:ring-2 focus:ring-purple-100">

        @error('schedule_name')
        <p class="mt-1 text-xs text-rose-600">
            {{ $message }}
        </p>
        @enderror
    </div>


    {{-- Shift Name --}}
    <div>
        <label for="shift_name" class="mb-1 block text-sm font-medium text-slate-700">
            Shift Name
        </label>

        <input type="text" id="shift_name" name="shift_name"
            value="{{ old('shift_name', $workingSchedule->shift_name ?? '') }}" placeholder="e.g. Morning Shift"
            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-900 outline-none transition focus:border-purple-500 focus:ring-2 focus:ring-purple-100">

        @error('shift_name')
        <p class="mt-1 text-xs text-rose-600">
            {{ $message }}
        </p>
        @enderror
    </div>


    {{-- Shift Number --}}
    <div>
        <label for="shift_no" class="mb-1 block text-sm font-medium text-slate-700">
            Shift Number
        </label>

        <input type="number" id="shift_no" name="shift_no"
            value="{{ old('shift_no', $workingSchedule->shift_no ?? '') }}" min="1"
            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-900 outline-none transition focus:border-purple-500 focus:ring-2 focus:ring-purple-100">

        @error('shift_no')
        <p class="mt-1 text-xs text-rose-600">
            {{ $message }}
        </p>
        @enderror
    </div>


    {{-- Sequence Number --}}
    <div>
        <label for="sequence_no" class="mb-1 block text-sm font-medium text-slate-700">
            Sequence Number
        </label>

        <input type="number" id="sequence_no" name="sequence_no"
            value="{{ old('sequence_no', $workingSchedule->sequence_no ?? '') }}" min="1"
            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm text-slate-900 outline-none transition focus:border-purple-500 focus:ring-2 focus:ring-purple-100">

        @error('sequence_no')
        <p class="mt-1 text-xs text-rose-600">
            {{ $message }}
        </p>
        @enderror
    </div>

</div>


{{-- Working Hours --}}
<div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">

    <h4 class="mb-4 text-sm font-semibold text-slate-900">
        Working Hours
    </h4>

    <div class="grid gap-6 md:grid-cols-3">

        {{-- Start --}}
        <div>
            <label for="start_time" class="mb-1 block text-sm font-medium text-slate-700">
                Start Time
            </label>

            <input type="time" id="start_time" name="start_time"
                value="{{ old('start_time', $workingSchedule->start_time ?? '') }}"
                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100">

            @error('start_time')
            <p class="mt-1 text-xs text-rose-600">
                {{ $message }}
            </p>
            @enderror
        </div>


        {{-- Break Start --}}
        <div>
            <label for="break_start" class="mb-1 block text-sm font-medium text-slate-700">
                Break Start
            </label>

            <input type="time" id="break_start" name="break_start"
                value="{{ old('break_start', $workingSchedule->break_start ?? '') }}"
                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100">

            @error('break_start')
            <p class="mt-1 text-xs text-rose-600">
                {{ $message }}
            </p>
            @enderror
        </div>


        {{-- Break End --}}
        <div>
            <label for="break_end" class="mb-1 block text-sm font-medium text-slate-700">
                Break End
            </label>

            <input type="time" id="break_end" name="break_end"
                value="{{ old('break_end', $workingSchedule->break_end ?? '') }}"
                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100">

            @error('break_end')
            <p class="mt-1 text-xs text-rose-600">
                {{ $message }}
            </p>
            @enderror
        </div>


        {{-- End --}}
        <div>
            <label for="end_time" class="mb-1 block text-sm font-medium text-slate-700">
                End Time
            </label>

            <input type="time" id="end_time" name="end_time"
                value="{{ old('end_time', $workingSchedule->end_time ?? '') }}"
                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100">

            @error('end_time')
            <p class="mt-1 text-xs text-rose-600">
                {{ $message }}
            </p>
            @enderror
        </div>

    </div>

</div>


{{-- Dates --}}
<div class="grid gap-6 md:grid-cols-2">

    {{-- Effective From --}}
    <div>
        <label for="effective_from" class="mb-1 block text-sm font-medium text-slate-700">
            Effective From
        </label>

        <input type="date" id="effective_from" name="effective_from"
            value="{{ old('effective_from', optional($workingSchedule->effective_from)->format('Y-m-d')) }}"
            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100">

        @error('effective_from')
        <p class="mt-1 text-xs text-rose-600">
            {{ $message }}
        </p>
        @enderror
    </div>


    {{-- Effective To --}}
    <div>
        <label for="effective_to" class="mb-1 block text-sm font-medium text-slate-700">
            Effective To
        </label>

        <input type="date" id="effective_to" name="effective_to"
            value="{{ old('effective_to', optional($workingSchedule->effective_to)->format('Y-m-d')) }}"
            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100">

        @error('effective_to')
        <p class="mt-1 text-xs text-rose-600">
            {{ $message }}
        </p>
        @enderror
    </div>

</div>


{{-- Options --}}
<div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">

    <h4 class="mb-4 text-sm font-semibold text-slate-900">
        Schedule Options
    </h4>

    {{-- Working Hours --}}
    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">

        <h4 class="mb-4 text-sm font-semibold text-slate-900">
            Working Hours
        </h4>

        <div class="grid gap-6 md:grid-cols-4">

            {{-- Start --}}
            <div>
                <label for="start_time" class="mb-1 block text-sm font-medium text-slate-700">
                    Start Time
                </label>

                <input type="time" id="start_time" name="start_time"
                    value="{{ old('start_time', $workingSchedule->start_time ? \Carbon\Carbon::parse($workingSchedule->start_time)->format('H:i') : '') }}"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100">

                @error('start_time')
                <p class="mt-1 text-xs text-rose-600">
                    {{ $message }}
                </p>
                @enderror
            </div>

            {{-- Break Start --}}
            <div>
                <label for="break_start" class="mb-1 block text-sm font-medium text-slate-700">
                    Break Start
                </label>

                <input type="time" id="break_start" name="break_start"
                    value="{{ old('break_start', $workingSchedule->break_start ? \Carbon\Carbon::parse($workingSchedule->break_start)->format('H:i') : '') }}"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100">

                @error('break_start')
                <p class="mt-1 text-xs text-rose-600">
                    {{ $message }}
                </p>
                @enderror
            </div>

            {{-- Break End --}}
            <div>
                <label for="break_end" class="mb-1 block text-sm font-medium text-slate-700">
                    Break End
                </label>

                <input type="time" id="break_end" name="break_end"
                    value="{{ old('break_end', $workingSchedule->break_end ? \Carbon\Carbon::parse($workingSchedule->break_end)->format('H:i') : '') }}"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100">

                @error('break_end')
                <p class="mt-1 text-xs text-rose-600">
                    {{ $message }}
                </p>
                @enderror
            </div>

            {{-- End --}}
            <div>
                <label for="end_time" class="mb-1 block text-sm font-medium text-slate-700">
                    End Time
                </label>

                <input type="time" id="end_time" name="end_time"
                    value="{{ old('end_time', $workingSchedule->end_time ? \Carbon\Carbon::parse($workingSchedule->end_time)->format('H:i') : '') }}"
                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-purple-500 focus:ring-2 focus:ring-purple-100">

                @error('end_time')
                <p class="mt-1 text-xs text-rose-600">
                    {{ $message }}
                </p>
                @enderror
            </div>

        </div>
    </div>

</div>


{{-- Buttons --}}
<div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-5">

    <a href="{{ route('working-schedules.index') }}"
        class="rounded-xl border border-slate-300 bg-slate-100 px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:bg-slate-200">
        Cancel
    </a>

    <button type="submit"
        class="rounded-xl bg-emerald-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">
        Update Working Schedule
    </button>

</div>