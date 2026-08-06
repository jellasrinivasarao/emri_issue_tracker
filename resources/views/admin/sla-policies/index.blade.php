<x-app-layout>

    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            SLA Policy Master
        </h2>
    </x-slot>

    <div class="py-8">

        <div class="mx-auto max-w-7xl px-4">

            <div class="rounded-3xl border border-slate-200 bg-white shadow-sm">

                {{-- Header --}}
                <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-6 py-5">

                    <div>

                        <h3 class="text-lg font-semibold">
                            SLA Policy List
                        </h3>

                        <p class="text-sm text-slate-600">
                            Configure Response & Resolution SLA Policies.
                        </p>

                    </div>

                    <a href="{{ route('sla-policies.create') }}"
                        class="rounded-xl bg-emerald-600 px-5 py-2.5 text-white hover:bg-emerald-700">

                        + New SLA Policy

                    </a>

                </div>

                {{-- Search --}}

                <form method="GET" action="{{ route('sla-policies.index') }}"
                    class="border-b border-slate-200 px-6 py-5">

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-6">

                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..."
                            class="rounded-xl border border-slate-200 px-3 py-2.5">

                        <select name="project_id" class="rounded-xl border border-slate-200 px-3 py-2.5">

                            <option value="">
                                Project
                            </option>

                            @foreach($projects as $project)

                            <option value="{{ $project->project_id }}" @selected(request('project_id')==$project->
                                project_id)>

                                {{ $project->project_name }}

                            </option>

                            @endforeach

                        </select>

                        <select name="application_id" class="rounded-xl border border-slate-200 px-3 py-2.5">

                            <option value="">
                                Application
                            </option>

                            @foreach($applications as $application)

                            <option value="{{ $application->application_id }}"
                                @selected(request('application_id')==$application->application_id)>

                                {{ $application->application_name }}

                            </option>

                            @endforeach

                        </select>

                        <select name="service_id" class="rounded-xl border border-slate-200 px-3 py-2.5">

                            <option value="">
                                Service
                            </option>

                            @foreach($services as $service)

                            <option value="{{ $service->service_id }}" @selected(request('service_id')==$service->
                                service_id)>

                                {{ $service->service_name }}

                            </option>

                            @endforeach

                        </select>

                        <select name="priority_id" class="rounded-xl border border-slate-200 px-3 py-2.5">

                            <option value="">
                                Priority
                            </option>

                            @foreach($priorities as $priority)

                            <option value="{{ $priority->priority_id }}" @selected(request('priority_id')==$priority->
                                priority_id)>

                                {{ $priority->priority_name }}

                            </option>

                            @endforeach

                        </select>

                        <div class="flex gap-2">

                            <button class="rounded-xl bg-slate-700 px-5 py-2.5 text-white">

                                Search

                            </button>

                            <a href="{{ route('sla-policies.index') }}" class="rounded-xl border px-5 py-2.5">

                                Reset

                            </a>

                        </div>

                    </div>

                </form>

                {{-- Flash Message --}}

                @if(session('success'))

                <div class="m-5 rounded-xl bg-green-100 px-4 py-3 text-green-700">

                    {{ session('success') }}

                </div>

                @endif

                {{-- Table --}}

                <div class="overflow-x-auto">

                    <table class="min-w-full">

                        <thead class="bg-slate-100">

                            <tr>

                                <th class="px-5 py-3 text-left">#</th>

                                <th class="px-5 py-3 text-left">
                                    Project
                                </th>

                                <th class="px-5 py-3 text-left">
                                    Application
                                </th>

                                <th class="px-5 py-3 text-left">
                                    Service
                                </th>

                                <th class="px-5 py-3 text-left">
                                    Priority
                                </th>

                                <th class="px-5 py-3 text-center">
                                    Response
                                </th>

                                <th class="px-5 py-3 text-center">
                                    Resolution
                                </th>

                                <th class="px-5 py-3 text-center">
                                    Calendar
                                </th>

                                <th class="px-5 py-3 text-center">
                                    Status
                                </th>

                                <th class="px-5 py-3 text-center">
                                    Action
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($policies as $policy)

                            <tr class="border-t">

                                <td class="px-5 py-4">

                                    {{ $policies->firstItem() + $loop->index }}

                                </td>

                                <td class="px-5 py-4">

                                    {{ $policy->project->project_name }}

                                </td>

                                <td class="px-5 py-4">

                                    {{ $policy->application->application_name }}

                                </td>

                                <td class="px-5 py-4">

                                    {{ $policy->service->service_name }}

                                </td>

                                <td class="px-5 py-4">

                                    {{ $policy->priority->priority_name }}

                                </td>

                                <td class="px-5 py-4 text-center">

                                    {{ $policy->response_time_minutes }} Min

                                </td>

                                <td class="px-5 py-4 text-center">

                                    {{ $policy->resolution_time_minutes }} Min

                                </td>

                                <td class="px-5 py-4 text-center">

                                    {{ $policy->calendar->calendar_name }}

                                </td>

                                <td class="px-5 py-4 text-center">

                                    @if($policy->is_active)

                                    <span class="rounded-full bg-green-100 px-3 py-1 text-xs text-green-700">

                                        Active

                                    </span>

                                    @else

                                    <span class="rounded-full bg-red-100 px-3 py-1 text-xs text-red-700">

                                        Inactive

                                    </span>

                                    @endif

                                </td>

                                <td class="px-5 py-4">

                                    <div class="flex justify-center gap-2">

                                        <a href="{{ route('sla-policies.edit',$policy->sla_policy_id) }}"
                                            class="rounded-lg bg-blue-600 px-3 py-2 text-xs text-white">

                                            Edit

                                        </a>

                                        <form method="POST"
                                            action="{{ route('sla-policies.destroy',$policy->sla_policy_id) }}"
                                            onsubmit="return confirm('Delete SLA Policy?')">

                                            @csrf
                                            @method('DELETE')

                                            <button class="rounded-lg bg-red-600 px-3 py-2 text-xs text-white">

                                                Delete

                                            </button>

                                        </form>

                                    </div>

                                </td>

                            </tr>

                            @empty

                            <tr>

                                <td colspan="10" class="py-10 text-center text-slate-500">

                                    No SLA Policies Found

                                </td>

                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

                {{-- Pagination --}}

                <div class="border-t border-slate-200 px-6 py-4">

                    {{ $policies->links() }}

                </div>

            </div>

        </div>

    </div>

</x-app-layout>