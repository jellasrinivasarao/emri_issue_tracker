<x-app-layout>

    <x-slot name="header">

        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ $title ?? __('Edit Calendar Holiday') }}
        </h2>

    </x-slot>


    <div class="py-8">

        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">

            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

                {{-- Header --}}

                <div class="border-b border-slate-200 bg-slate-50 px-5 py-5">

                    <h3 class="text-lg font-semibold text-slate-900">
                        Edit Calendar Holiday
                    </h3>

                    <p class="mt-1 text-sm text-slate-600">
                        Update holiday configuration.
                    </p>

                </div>


                {{-- Errors --}}

                @if($errors->any())

                <div class="px-5 pt-5">

                    <div class="rounded-2xl bg-rose-50 px-4 py-3 text-sm text-rose-700">

                        <ul class="list-disc space-y-1 pl-5">

                            @foreach($errors->all() as $error)

                            <li>{{ $error }}</li>

                            @endforeach

                        </ul>

                    </div>

                </div>

                @endif


                <form method="POST"
                    action="{{ route('calendar-holidays.update', ['holiday_id' => $holiday->holiday_id]) }}">

                    @csrf
                    @method('PUT')


                    <div class="space-y-5 px-5 py-6">

                        {{-- Calendar --}}

                        <div>

                            <label class="mb-1 block text-sm font-medium text-slate-700">
                                Working Calendar
                            </label>

                            <select name="calendar_id" required
                                class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-slate-400">

                                <option value="">
                                    Select Calendar
                                </option>

                                @foreach($calendars as $calendar)

                                <option value="{{ $calendar->calendar_id }}"
                                    {{ old('calendar_id', $holiday->calendar_id) == $calendar->calendar_id ? 'selected' : '' }}>

                                    {{ $calendar->calendar_name }}

                                    @if($calendar->calendar_code)
                                    ({{ $calendar->calendar_code }})
                                    @endif

                                </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- Date / Type --}}

                        <div class="grid gap-5 md:grid-cols-2">

                            <div>

                                <label class="mb-1 block text-sm font-medium text-slate-700">
                                    Holiday Date
                                </label>

                                <input type="date" name="holiday_date" required
                                    value="{{ old('holiday_date', $holiday->holiday_date?->format('Y-m-d')) }}"
                                    class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400">

                            </div>


                            <div>

                                <label class="mb-1 block text-sm font-medium text-slate-700">
                                    Holiday Type
                                </label>

                                <select name="holiday_type"
                                    class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm outline-none focus:border-slate-400">

                                    @foreach([
                                    'PUBLIC' => 'Public',
                                    'NATIONAL' => 'National',
                                    'REGIONAL' => 'Regional',
                                    'COMPANY' => 'Company',
                                    'OPTIONAL' => 'Optional',
                                    ] as $value => $label)

                                    <option value="{{ $value }}"
                                        {{ old('holiday_type', $holiday->holiday_type) === $value ? 'selected' : '' }}>

                                        {{ $label }}

                                    </option>

                                    @endforeach

                                </select>

                            </div>

                        </div>


                        {{-- Holiday Name --}}

                        <div>

                            <label class="mb-1 block text-sm font-medium text-slate-700">
                                Holiday Name
                            </label>

                            <input type="text" name="holiday_name" maxlength="150" required
                                value="{{ old('holiday_name', $holiday->holiday_name) }}"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400">

                        </div>


                        {{-- Description --}}

                        <div>

                            <label class="mb-1 block text-sm font-medium text-slate-700">
                                Description
                            </label>

                            <textarea name="description" rows="4" maxlength="500"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400">{{ old('description', $holiday->description) }}</textarea>

                        </div>


                        {{-- Status --}}

                        <div>

                            <label class="mb-1 block text-sm font-medium text-slate-700">
                                Status
                            </label>

                            <div class="flex items-center gap-3">

                                <span class="rounded-full px-3 py-1 text-xs font-semibold
                                    {{ (int) $holiday->is_active === 1
                                        ? 'bg-emerald-50 text-emerald-700'
                                        : 'bg-amber-50 text-amber-700' }}">

                                    {{ (int) $holiday->is_active === 1
                                        ? 'Active'
                                        : 'Inactive' }}

                                </span>

                            </div>

                        </div>


                        {{-- Buttons --}}

                        <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-5">

                            <a href="{{ route('calendar-holidays.index') }}"
                                class="rounded-xl border border-slate-300 bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">

                                Cancel

                            </a>

                            <button type="submit"
                                class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">

                                Update Holiday

                            </button>

                        </div>

                    </div>

                </form>

            </div>

        </div>

    </div>

</x-app-layout>