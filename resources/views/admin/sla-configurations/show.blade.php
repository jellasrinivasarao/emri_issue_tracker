<x-app-layout>

    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold text-slate-800">
                {{ $slaConfiguration->sla_name }}
            </h2>

            <p class="mt-1 text-sm text-slate-500">
                {{ $slaConfiguration->sla_code }}
            </p>
        </div>
    </x-slot>

    <div class="py-8">

        <div class="mx-auto max-w-5xl px-4">

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">

                {{-- Card Header --}}
                <div class="flex items-center justify-between border-b border-slate-200 px-6 py-5">

                    <h3 class="font-semibold text-slate-800">
                        SLA Details
                    </h3>

                    <a href="{{ route('sla-configurations.index') }}"
                        class="inline-flex items-center rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-50">
                        Back
                    </a>

                </div>

                {{-- SLA Details --}}
                <div class="grid grid-cols-1 gap-6 p-6 md:grid-cols-3">

                    {{-- SLA Code --}}
                    <div>
                        <div class="text-xs text-slate-400">
                            SLA Code
                        </div>

                        <div class="mt-1 font-semibold text-slate-800">
                            {{ $slaConfiguration->sla_code }}
                        </div>
                    </div>

                    {{-- SLA Name --}}
                    <div>
                        <div class="text-xs text-slate-400">
                            SLA Name
                        </div>

                        <div class="mt-1 font-semibold text-slate-800">
                            {{ $slaConfiguration->sla_name }}
                        </div>
                    </div>

                    {{-- Support Level --}}
                    <div>
                        <div class="text-xs text-slate-400">
                            Support Level
                        </div>

                        <div class="mt-1 font-semibold text-slate-800">
                            {{ $slaConfiguration->support_level_name }}
                        </div>
                    </div>

                    {{-- Response SLA --}}
                    <div>
                        <div class="text-xs text-slate-400">
                            Response SLA
                        </div>

                        <div class="mt-1 text-lg font-semibold text-slate-800">
                            {{ $slaConfiguration->response_sla_hours }} Hours
                        </div>
                    </div>

                    {{-- Resolution SLA --}}
                    <div>
                        <div class="text-xs text-slate-400">
                            Resolution SLA
                        </div>

                        <div class="mt-1 text-lg font-semibold text-slate-800">
                            {{ $slaConfiguration->resolution_sla_hours }} Hours
                        </div>
                    </div>

                    {{-- Escalation SLA --}}
                    <div>
                        <div class="text-xs text-slate-400">
                            Escalation SLA
                        </div>

                        <div class="mt-1 text-lg font-semibold text-slate-800">

                            @if($slaConfiguration->escalation_sla_hours !== null)

                            {{ $slaConfiguration->escalation_sla_hours }} Hours

                            @else

                            —

                            @endif

                        </div>
                    </div>

                    {{-- Working Calendar --}}
                    <div class="md:col-span-2">

                        <div class="text-xs text-slate-400">
                            Working Calendar
                        </div>

                        <div class="mt-1 font-semibold text-slate-800">

                            @if($slaConfiguration->workingCalendar)

                            {{ $slaConfiguration->workingCalendar->calendar_name }}

                            <span class="ml-2 text-xs text-slate-400">
                                {{ $slaConfiguration->workingCalendar->calendar_code }}
                            </span>

                            @else

                            Not configured

                            @endif

                        </div>

                    </div>

                    {{-- Status --}}
                    <div>

                        <div class="text-xs text-slate-400">
                            Status
                        </div>

                        <div class="mt-2">

                            @if($slaConfiguration->is_active)

                            <span
                                class="inline-flex rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700">
                                Active
                            </span>

                            @else

                            <span
                                class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold text-slate-600">
                                Inactive
                            </span>

                            @endif

                        </div>

                    </div>

                    {{-- Description --}}
                    <div class="md:col-span-3">

                        <div class="text-xs text-slate-400">
                            Description
                        </div>

                        <div class="mt-1 text-sm text-slate-700">
                            {{ $slaConfiguration->description ?: '—' }}
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>