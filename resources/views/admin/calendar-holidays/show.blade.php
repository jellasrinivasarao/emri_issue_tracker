<x-app-layout>

    <x-slot name="header">

        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ $title ?? __('Calendar Holiday Details') }}
        </h2>

    </x-slot>


    <div class="py-8">

        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">

            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

                {{-- Header --}}

                <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-5 py-5">

                    <div>

                        <h3 class="text-lg font-semibold text-slate-900">
                            Calendar Holiday Details
                        </h3>

                        <p class="mt-1 text-sm text-slate-600">
                            View holiday master configuration.
                        </p>

                    </div>


                    <div>

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


                {{-- Details --}}

                <div class="px-5 py-6">

                    <div class="grid gap-5 md:grid-cols-2">

                        {{-- Holiday ID --}}

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">

                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Holiday ID
                            </p>

                            <p class="mt-1 text-sm font-semibold text-slate-900">
                                {{ $holiday->holiday_id }}
                            </p>

                        </div>


                        {{-- Calendar --}}

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">

                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Working Calendar
                            </p>

                            <p class="mt-1 text-sm font-semibold text-slate-900">

                                {{ $holiday->calendar->calendar_name ?? '-' }}

                            </p>

                            @if($holiday->calendar?->calendar_code)

                            <p class="mt-1 text-xs text-slate-500">

                                Code:
                                {{ $holiday->calendar->calendar_code }}

                            </p>

                            @endif

                        </div>


                        {{-- Holiday Date --}}

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">

                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Holiday Date
                            </p>

                            <p class="mt-1 text-sm font-semibold text-slate-900">

                                {{ $holiday->holiday_date?->format('d-M-Y') ?? '-' }}

                            </p>

                            @if($holiday->holiday_date)

                            <p class="mt-1 text-xs text-slate-500">

                                {{ $holiday->holiday_date->format('l') }}

                            </p>

                            @endif

                        </div>


                        {{-- Holiday Type --}}

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">

                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Holiday Type
                            </p>

                            <div class="mt-2">

                                <span class="rounded-full bg-purple-50 px-3 py-1 text-xs font-semibold text-purple-700">

                                    {{ $holiday->holiday_type_label ?? ($holiday->holiday_type ?? 'Holiday') }}

                                </span>

                            </div>

                        </div>


                        {{-- Holiday Name --}}

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 md:col-span-2">

                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Holiday Name
                            </p>

                            <p class="mt-1 text-sm font-semibold text-slate-900">
                                {{ $holiday->holiday_name }}
                            </p>

                        </div>


                        {{-- Description --}}

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 md:col-span-2">

                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Description
                            </p>

                            <p class="mt-1 whitespace-pre-line text-sm text-slate-700">

                                {{ $holiday->description ?: '-' }}

                            </p>

                        </div>


                        {{-- Created --}}

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">

                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Created At
                            </p>

                            <p class="mt-1 text-sm text-slate-700">

                                {{ $holiday->created_at?->format('d-M-Y H:i:s') ?? '-' }}

                            </p>

                        </div>


                        {{-- Status --}}

                        <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">

                            <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">
                                Status
                            </p>

                            <p class="mt-2">

                                <span class="rounded-full px-3 py-1 text-xs font-semibold
                                    {{ (int) $holiday->is_active === 1
                                        ? 'bg-emerald-50 text-emerald-700'
                                        : 'bg-amber-50 text-amber-700' }}">

                                    {{ (int) $holiday->is_active === 1
                                        ? 'Active'
                                        : 'Inactive' }}

                                </span>

                            </p>

                        </div>

                    </div>


                    {{-- Actions --}}

                    <div class="mt-6 flex flex-wrap items-center justify-end gap-3 border-t border-slate-200 pt-5">

                        <a href="{{ route('calendar-holidays.index') }}"
                            class="rounded-xl border border-slate-300 bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">

                            Back

                        </a>


                        <a href="{{ route('calendar-holidays.index') }}"
                            onclick="event.preventDefault(); window.location.href='{{ route('calendar-holidays.index') }}';"
                            class="rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">

                            Edit

                        </a>


                        <form method="POST"
                            action="{{ route('calendar-holidays.toggle', ['holiday_id' => $holiday->holiday_id]) }}"
                            class="inline">

                            @csrf

                            <button type="submit" class="rounded-xl px-4 py-2 text-sm font-semibold
                                {{ (int) $holiday->is_active === 1
                                    ? 'bg-rose-100 text-rose-700 hover:bg-rose-200'
                                    : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' }}">

                                {{ (int) $holiday->is_active === 1
                                    ? 'Disable'
                                    : 'Activate' }}

                            </button>

                        </form>

                    </div>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>