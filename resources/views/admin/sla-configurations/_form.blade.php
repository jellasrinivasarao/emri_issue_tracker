<div class="overflow-hidden rounded-2xl border
           border-slate-200 bg-white shadow-sm">

    {{-- Header --}}

    <div class="border-b border-slate-200 px-6 py-5">

        <div class="flex items-center gap-3">

            <div class="flex h-10 w-10 items-center justify-center
                       rounded-xl bg-slate-100">
                <svg class="h-5 w-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5
                           a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414
                           5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>

            <div>

                <h3 class="text-base font-semibold text-slate-800">
                    SLA Configuration
                </h3>

                <p class="text-sm text-slate-500">
                    Configure SLA timings and working calendar.
                </p>

            </div>

        </div>

    </div>


    {{-- Form Body --}}

    <div class="p-6">

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2">


            {{-- SLA Code --}}

            <div>

                <label for="sla_code" class="mb-1.5 block text-sm font-medium
                           text-slate-700">

                    SLA Code

                    <span class="text-red-500">*</span>

                </label>

                <input type="text" id="sla_code" name="sla_code" value="{{ old(
                        'sla_code',
                        $sla->sla_code ?? ''
                    ) }}" maxlength="50" required placeholder="Example: SLA-P1-CRITICAL" class="w-full rounded-xl border-slate-300
                           uppercase shadow-sm
                           focus:border-slate-500
                           focus:ring-slate-500">

                <p class="mt-1 text-xs text-slate-400">
                    Only letters, numbers, underscore and hyphen are allowed.
                </p>

                @error('sla_code')
                <p class="mt-1 text-xs text-red-600">
                    {{ $message }}
                </p>
                @enderror

            </div>


            {{-- SLA Name --}}

            <div>

                <label for="sla_name" class="mb-1.5 block text-sm font-medium
                           text-slate-700">

                    SLA Name

                    <span class="text-red-500">*</span>

                </label>

                <input type="text" id="sla_name" name="sla_name" value="{{ old(
                        'sla_name',
                        $sla->sla_name ?? ''
                    ) }}" maxlength="150" required placeholder="Example: Critical - Priority 1" class="w-full rounded-xl border-slate-300
                           shadow-sm
                           focus:border-slate-500
                           focus:ring-slate-500">

                @error('sla_name')
                <p class="mt-1 text-xs text-red-600">
                    {{ $message }}
                </p>
                @enderror

            </div>


            {{-- Support Level --}}

            <div>

                <label for="support_level" class="mb-1.5 block text-sm font-medium
                           text-slate-700">

                    Support Level

                    <span class="text-red-500">*</span>

                </label>

                <select id="support_level" name="support_level" required class="w-full rounded-xl border-slate-300
                           shadow-sm
                           focus:border-slate-500
                           focus:ring-slate-500">

                    <option value="">
                        Select Support Level
                    </option>

                    <option value="1" @selected( old( 'support_level' , $sla->support_level ?? ''
                        ) == 1
                        )
                        >
                        HO IT Level-1
                    </option>

                    <option value="2" @selected( old( 'support_level' , $sla->support_level ?? ''
                        ) == 2
                        )
                        >
                        Vendor Level-2
                    </option>

                </select>

                @error('support_level')
                <p class="mt-1 text-xs text-red-600">
                    {{ $message }}
                </p>
                @enderror

            </div>


            {{-- Working Calendar --}}

            <div>

                <label for="calendar_id" class="mb-1.5 block text-sm font-medium
                           text-slate-700">

                    Working Calendar

                    <span class="text-red-500">*</span>

                </label>

                <select id="calendar_id" name="calendar_id" required class="w-full rounded-xl border-slate-300
                           shadow-sm
                           focus:border-slate-500
                           focus:ring-slate-500">

                    <option value="">
                        Select Working Calendar
                    </option>

                    @foreach($calendars as $calendar)

                    <option value="{{ $calendar->calendar_id }}" @selected( old( 'calendar_id' , $sla->calendar_id ?? ''
                        ) == $calendar->calendar_id
                        )
                        >

                        {{ $calendar->calendar_name }}

                        @if($calendar->calendar_code)
                        ({{ $calendar->calendar_code }})
                        @endif

                    </option>

                    @endforeach

                </select>

                @error('calendar_id')
                <p class="mt-1 text-xs text-red-600">
                    {{ $message }}
                </p>
                @enderror

            </div>


            {{-- Response SLA --}}

            <div>

                <label for="response_sla_hours" class="mb-1.5 block text-sm font-medium
                           text-slate-700">

                    Response SLA

                    <span class="text-red-500">*</span>

                </label>

                <div class="relative">

                    <input type="number" id="response_sla_hours" name="response_sla_hours" value="{{ old(
                            'response_sla_hours',
                            $sla->response_sla_hours ?? ''
                        ) }}" min="0" step="0.25" required placeholder="1.00" class="w-full rounded-xl border-slate-300
                               pr-16 shadow-sm
                               focus:border-slate-500
                               focus:ring-slate-500">

                    <span class="absolute right-4 top-1/2
                               -translate-y-1/2
                               text-xs font-medium text-slate-400">
                        Hours
                    </span>

                </div>

                <p class="mt-1 text-xs text-slate-400">
                    Maximum time allowed to acknowledge/respond.
                </p>

                @error('response_sla_hours')
                <p class="mt-1 text-xs text-red-600">
                    {{ $message }}
                </p>
                @enderror

            </div>


            {{-- Resolution SLA --}}

            <div>

                <label for="resolution_sla_hours" class="mb-1.5 block text-sm font-medium
                           text-slate-700">

                    Resolution SLA

                    <span class="text-red-500">*</span>

                </label>

                <div class="relative">

                    <input type="number" id="resolution_sla_hours" name="resolution_sla_hours" value="{{ old(
                            'resolution_sla_hours',
                            $sla->resolution_sla_hours ?? ''
                        ) }}" min="0" step="0.25" required placeholder="4.00" class="w-full rounded-xl border-slate-300
                               pr-16 shadow-sm
                               focus:border-slate-500
                               focus:ring-slate-500">

                    <span class="absolute right-4 top-1/2
                               -translate-y-1/2
                               text-xs font-medium text-slate-400">
                        Hours
                    </span>

                </div>

                <p class="mt-1 text-xs text-slate-400">
                    Maximum business hours allowed for resolution.
                </p>

                @error('resolution_sla_hours')
                <p class="mt-1 text-xs text-red-600">
                    {{ $message }}
                </p>
                @enderror

            </div>


            {{-- Escalation SLA --}}

            <div>

                <label for="escalation_sla_hours" class="mb-1.5 block text-sm font-medium
                           text-slate-700">

                    Escalation SLA

                </label>

                <div class="relative">

                    <input type="number" id="escalation_sla_hours" name="escalation_sla_hours" value="{{ old(
                            'escalation_sla_hours',
                            $sla->escalation_sla_hours ?? ''
                        ) }}" min="0" step="0.25" placeholder="2.00" class="w-full rounded-xl border-slate-300
                               pr-16 shadow-sm
                               focus:border-slate-500
                               focus:ring-slate-500">

                    <span class="absolute right-4 top-1/2
                               -translate-y-1/2
                               text-xs font-medium text-slate-400">
                        Hours
                    </span>

                </div>

                <p class="mt-1 text-xs text-slate-400">
                    Time after which escalation should be triggered.
                </p>

                @error('escalation_sla_hours')
                <p class="mt-1 text-xs text-red-600">
                    {{ $message }}
                </p>
                @enderror

            </div>


            {{-- Description --}}

            <div class="md:col-span-2">

                <label for="description" class="mb-1.5 block text-sm font-medium
                           text-slate-700">
                    Description
                </label>

                <textarea id="description" name="description" rows="4" maxlength="1000"
                    placeholder="Enter SLA configuration description..." class="w-full rounded-xl border-slate-300
                           shadow-sm
                           focus:border-slate-500
                           focus:ring-slate-500">{{ old(
                    'description',
                    $sla->description ?? ''
                ) }}</textarea>

                @error('description')
                <p class="mt-1 text-xs text-red-600">
                    {{ $message }}
                </p>
                @enderror

            </div>


            {{-- Active Status --}}

            <div class="md:col-span-2">

                <div class="rounded-xl border border-slate-200
                           bg-slate-50 p-4">

                    <label for="is_active" class="flex cursor-pointer
                               items-center gap-3">

                        <input type="hidden" name="is_active" value="0">

                        <input type="checkbox" id="is_active" name="is_active" value="1" @checked( old( 'is_active' ,
                            $sla->is_active ?? true
                        )
                        )
                        class="h-5 w-5 rounded
                        border-slate-300
                        text-slate-800
                        focus:ring-slate-500"
                        >

                        <div>

                            <div class="text-sm font-semibold
                                       text-slate-700">
                                Active SLA
                            </div>

                            <div class="text-xs text-slate-500">
                                Active SLA configurations can be used
                                by Project Support Configuration and
                                Issue Routing.
                            </div>

                        </div>

                    </label>

                </div>

                @error('is_active')
                <p class="mt-1 text-xs text-red-600">
                    {{ $message }}
                </p>
                @enderror

            </div>

        </div>

    </div>


    {{-- Footer --}}

    <div class="flex items-center justify-end gap-3
               border-t border-slate-200 bg-slate-50
               px-6 py-4">

        <a href="{{ route(
                'admin.sla-configurations.index'
            ) }}" class="rounded-xl border border-slate-300
                   bg-white px-5 py-2.5 text-sm
                   font-medium text-slate-700
                   hover:bg-slate-50">
            Cancel
        </a>

        <button type="submit" class="rounded-xl bg-slate-900
                   px-5 py-2.5 text-sm font-semibold
                   text-white shadow-sm
                   hover:bg-slate-800">

            {{ isset($sla)
                ? 'Update SLA Configuration'
                : 'Save SLA Configuration'
            }}

        </button>

    </div>

</div>