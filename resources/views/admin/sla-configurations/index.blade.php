<x-app-layout>

    <x-slot name="header">

        <div class="flex items-center justify-between">

            <div>

                <h2 class="text-xl font-semibold text-slate-800">
                    SLA Configuration
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Configure response, resolution and escalation SLAs.
                </p>

            </div>

            <a href="{{ route('sla-configurations.create') }}" class="inline-flex items-center gap-2 rounded-xl
                       bg-slate-900 px-4 py-2.5 text-sm font-semibold
                       text-white hover:bg-slate-800">

                <span class="text-lg">+</span>

                Add SLA

            </a>

        </div>

    </x-slot>


    <div class="py-8">

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">


            @if(session('success'))

            <div class="mb-5 rounded-xl border border-green-200
                           bg-green-50 px-4 py-3 text-sm text-green-700">

                {{ session('success') }}

            </div>

            @endif


            @if(session('error'))

            <div class="mb-5 rounded-xl border border-red-200
                           bg-red-50 px-4 py-3 text-sm text-red-700">

                {{ session('error') }}

            </div>

            @endif


            <div class="overflow-hidden rounded-2xl border
                       border-slate-200 bg-white shadow-sm">


                {{-- Filters --}}

                <div class="border-b border-slate-200
                           bg-slate-50/70 p-4">

                    <form method="GET" action="{{ route('sla-configurations.index') }}"
                        class="grid grid-cols-1 gap-3 md:grid-cols-5">


                        <div class="md:col-span-2">

                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Search SLA code or name..." class="w-full rounded-xl
                                       border-slate-300 text-sm
                                       focus:border-slate-500
                                       focus:ring-slate-500">

                        </div>


                        <div>

                            <select name="support_level" class="w-full rounded-xl
                                       border-slate-300 text-sm
                                       focus:border-slate-500
                                       focus:ring-slate-500">

                                <option value="">
                                    All Support Levels
                                </option>

                                <option value="1" @selected( request('support_level')=='1' )>

                                    HO IT Level-1

                                </option>

                                <option value="2" @selected( request('support_level')=='2' )>

                                    Vendor Level-2

                                </option>

                            </select>

                        </div>


                        <div>

                            <select name="status" class="w-full rounded-xl
                                       border-slate-300 text-sm
                                       focus:border-slate-500
                                       focus:ring-slate-500">

                                <option value="">
                                    All Status
                                </option>

                                <option value="active" @selected( request('status')==='active' )>

                                    Active

                                </option>

                                <option value="inactive" @selected( request('status')==='inactive' )>

                                    Inactive

                                </option>

                            </select>

                        </div>


                        <div class="flex gap-2">

                            <button type="submit" class="rounded-xl bg-slate-800
                                       px-4 py-2 text-sm font-medium
                                       text-white">

                                Search

                            </button>

                            <a href="{{ route('sla-configurations.index') }}" class="rounded-xl border
                                       border-slate-300 bg-white
                                       px-4 py-2 text-sm font-medium
                                       text-slate-700">

                                Reset

                            </a>

                        </div>

                    </form>

                </div>


                {{-- Table --}}

                <div class="overflow-x-auto">

                    <table class="min-w-full text-sm">

                        <thead class="bg-slate-50">

                            <tr>

                                <th class="px-6 py-4 text-left
                                           font-semibold text-slate-600">

                                    SLA Code

                                </th>

                                <th class="px-6 py-4 text-left
                                           font-semibold text-slate-600">

                                    SLA Name

                                </th>

                                <th class="px-6 py-4 text-left
                                           font-semibold text-slate-600">

                                    Support Level

                                </th>

                                <th class="px-6 py-4 text-center
                                           font-semibold text-slate-600">

                                    Response

                                </th>

                                <th class="px-6 py-4 text-center
                                           font-semibold text-slate-600">

                                    Resolution

                                </th>

                                <th class="px-6 py-4 text-center
                                           font-semibold text-slate-600">

                                    Escalation

                                </th>

                                <th class="px-6 py-4 text-left
                                           font-semibold text-slate-600">

                                    Calendar

                                </th>

                                <th class="px-6 py-4 text-left
                                           font-semibold text-slate-600">

                                    Status

                                </th>

                                <th class="px-6 py-4 text-right
                                           font-semibold text-slate-600">

                                    Actions

                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-slate-100">

                            @forelse($slas as $sla)

                            <tr class="hover:bg-slate-50">


                                <td class="px-6 py-4">

                                    <span class="font-semibold
                                                   text-slate-800">

                                        {{ $sla->sla_code }}

                                    </span>

                                </td>


                                <td class="px-6 py-4">

                                    <div class="font-medium
                                                   text-slate-800">

                                        {{ $sla->sla_name }}

                                    </div>

                                    @if($sla->description)

                                    <div class="mt-1 max-w-xs
                                                       truncate text-xs
                                                       text-slate-500">

                                        {{ $sla->description }}

                                    </div>

                                    @endif

                                </td>


                                <td class="px-6 py-4">

                                    @if($sla->support_level == 1)

                                    <span class="rounded-full
                                                       bg-purple-50
                                                       px-2.5 py-1
                                                       text-xs font-semibold
                                                       text-purple-700">

                                        HO IT Level-1

                                    </span>

                                    @elseif($sla->support_level == 2)

                                    <span class="rounded-full
                                                       bg-blue-50
                                                       px-2.5 py-1
                                                       text-xs font-semibold
                                                       text-blue-700">

                                        Vendor Level-2

                                    </span>

                                    @else

                                    <span class="text-xs
                                                       text-slate-400">

                                        Not Defined

                                    </span>

                                    @endif

                                </td>


                                <td class="px-6 py-4 text-center
                                               font-semibold text-slate-700">

                                    {{ number_format(
                                            $sla->response_sla_hours,
                                            2
                                        ) }}h

                                </td>


                                <td class="px-6 py-4 text-center
                                               font-semibold text-slate-700">

                                    {{ number_format(
                                            $sla->resolution_sla_hours,
                                            2
                                        ) }}h

                                </td>


                                <td class="px-6 py-4 text-center
                                               font-semibold text-slate-700">

                                    @if($sla->escalation_sla_hours !== null)

                                    {{ number_format(
                                                $sla->escalation_sla_hours,
                                                2
                                            ) }}h

                                    @else

                                    —

                                    @endif

                                </td>


                                <td class="px-6 py-4">

                                    @if($sla->workingCalendar)

                                    <div class="font-medium
                                                       text-slate-700">

                                        {{
                                                    $sla->workingCalendar
                                                        ->calendar_name
                                                }}

                                    </div>

                                    <div class="text-xs
                                                       text-slate-400">

                                        {{
                                                    $sla->workingCalendar
                                                        ->calendar_code
                                                }}

                                    </div>

                                    @else

                                    <span class="text-xs
                                                       text-red-500">

                                        Not configured

                                    </span>

                                    @endif

                                </td>


                                <td class="px-6 py-4">

                                    @if($sla->is_active)

                                    <span class="inline-flex
                                                       items-center gap-1.5
                                                       rounded-full
                                                       bg-green-50 px-2.5 py-1
                                                       text-xs font-semibold
                                                       text-green-700">

                                        <span class="h-1.5 w-1.5
                                                           rounded-full
                                                           bg-green-500">
                                        </span>

                                        Active

                                    </span>

                                    @else

                                    <span class="inline-flex
                                                       items-center gap-1.5
                                                       rounded-full
                                                       bg-slate-100 px-2.5 py-1
                                                       text-xs font-semibold
                                                       text-slate-600">

                                        Inactive

                                    </span>

                                    @endif

                                </td>


                                <td class="px-6 py-4">

                                    <div class="flex justify-end gap-2">

                                        <a href="{{ route('sla-configurations.show',$sla) }}" class="rounded-lg border
                                                       border-slate-200
                                                       px-3 py-1.5 text-xs
                                                       font-medium
                                                       text-slate-600
                                                       hover:bg-slate-50">

                                            View

                                        </a>


                                        <a href="{{ route('sla-configurations.edit',$sla) }}" class="rounded-lg border
                                                       border-slate-200
                                                       px-3 py-1.5 text-xs
                                                       font-medium
                                                       text-slate-600
                                                       hover:bg-slate-50">

                                            Edit

                                        </a>


                                        <button type="button" onclick="toggleSla({{ $sla->sla_configuration_id }})"
                                            class="rounded-lg border
                                                       border-slate-200
                                                       px-3 py-1.5 text-xs
                                                       font-medium
                                                       text-slate-600">

                                            {{ $sla->is_active
                                                    ? 'Deactivate'
                                                    : 'Activate' }}

                                        </button>


                                        <button type="button" onclick="deleteSla({{ $sla->sla_configuration_id }})"
                                            class="rounded-lg border
                                                       border-red-200
                                                       px-3 py-1.5 text-xs
                                                       font-medium
                                                       text-red-600
                                                       hover:bg-red-50">

                                            Delete

                                        </button>

                                    </div>

                                </td>

                            </tr>

                            @empty

                            <tr>

                                <td colspan="9" class="px-6 py-12 text-center">

                                    <div class="text-sm font-medium
                                                   text-slate-600">

                                        No SLA configurations found.

                                    </div>

                                </td>

                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>


                @if($slas->hasPages())

                <div class="border-t border-slate-200 px-6 py-4">

                    {{ $slas->links() }}

                </div>

                @endif

            </div>

        </div>

    </div>


    <script>
    async function toggleSla(id) {
        if (!confirm(
                'Are you sure you want to change this SLA status?'
            )) {
            return;
        }

        const response = await fetch(`/sla-configurations/${id}/toggle`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,

                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {

            window.location.reload();

        } else {

            alert(data.message);

        }
    }


    async function deleteSla(id) {
        if (!confirm(
                'Delete this SLA configuration?'
            )) {
            return;
        }

        const response = await fetch(`/sla-configurations/${id}`, {
            method: 'DELETE',

            headers: {
                'X-CSRF-TOKEN': document.querySelector(
                    'meta[name="csrf-token"]'
                ).content,

                'Accept': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {

            window.location.reload();

        } else {

            alert(data.message);

        }
    }
    </script>

</x-app-layout>