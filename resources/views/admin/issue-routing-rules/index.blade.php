<x-app-layout>

    <x-slot name="header">

        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">

            <div>

                <h2 class="text-xl font-semibold text-slate-800">
                    Issue Routing Rules
                </h2>

                <p class="mt-1 text-sm text-slate-500">
                    Manage project-based issue routing configuration.
                </p>

            </div>


            <a href="{{ route('admin.issue-routing-rules.create') }}"
                class="inline-flex items-center justify-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">

                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>

                Add Routing Rule

            </a>

        </div>

    </x-slot>


    <div class="py-8">

        <div class="mx-auto max-w-[1600px] px-4 sm:px-6 lg:px-8">


            {{-- =====================================================
                FLASH MESSAGE
            ====================================================== --}}

            @if(session('success'))

            <div
                class="mb-6 flex items-center gap-3 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">

                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>

                {{ session('success') }}

            </div>

            @endif


            @if(session('error'))

            <div
                class="mb-6 flex items-center gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">

                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 12v.01M12 8v4m-9 4h18" />
                </svg>

                {{ session('error') }}

            </div>

            @endif


            {{-- =====================================================
                FILTERS
            ====================================================== --}}

            <div class="rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-200 px-6 py-5">

                    <div class="flex items-center gap-3">

                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100">

                            <svg class="h-5 w-5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707L15 12v6l-6 3v-9L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>

                        </div>

                        <div>

                            <h3 class="font-semibold text-slate-800">
                                Search & Filter
                            </h3>

                            <p class="text-sm text-slate-500">
                                Find routing rules quickly.
                            </p>

                        </div>

                    </div>

                </div>


                <form method="GET" action="{{ route('admin.issue-routing-rules.index') }}" class="p-6">

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">

                        {{-- Search --}}
                        <div class="lg:col-span-2">

                            <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Search
                            </label>

                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Rule code, name, category, type..."
                                class="w-full rounded-xl border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">

                        </div>


                        {{-- Project --}}
                        <div>

                            <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Project
                            </label>

                            <select name="project_id"
                                class="w-full rounded-xl border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">

                                <option value="">
                                    All Projects
                                </option>

                                @foreach($projects as $project)

                                <option value="{{ $project->project_id }}" @selected( request('project_id')==$project->
                                    project_id
                                    )
                                    >
                                    {{ $project->project_code }}
                                    -
                                    {{ $project->project_name }}
                                </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- Routing Level --}}
                        <div>

                            <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Routing Level
                            </label>

                            <select name="routing_level"
                                class="w-full rounded-xl border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">

                                <option value="">
                                    All Levels
                                </option>

                                @foreach([1,2,3,4,5] as $level)

                                <option value="{{ $level }}" @selected( request('routing_level')==$level )>
                                    Level {{ $level }}
                                </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- Status --}}
                        <div>

                            <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Status
                            </label>

                            <select name="is_active"
                                class="w-full rounded-xl border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">

                                <option value="">
                                    All Status
                                </option>

                                <option value="1" @selected(request('is_active')==='1' )>
                                    Active
                                </option>

                                <option value="0" @selected(request('is_active')==='0' )>
                                    Inactive
                                </option>

                            </select>

                        </div>


                        {{-- Default --}}
                        <div>

                            <label class="mb-2 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Default Rule
                            </label>

                            <select name="is_default"
                                class="w-full rounded-xl border-slate-300 px-4 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">

                                <option value="">
                                    All
                                </option>

                                <option value="1" @selected(request('is_default')==='1' )>
                                    Default
                                </option>

                                <option value="0" @selected(request('is_default')==='0' )>
                                    Non Default
                                </option>

                            </select>

                        </div>


                        {{-- Actions --}}
                        <div class="flex items-end gap-2">

                            <button type="submit"
                                class="flex-1 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700">
                                Search
                            </button>

                            <a href="{{ route('admin.issue-routing-rules.index') }}"
                                class="rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                                Reset
                            </a>

                        </div>

                    </div>

                </form>

            </div>


            {{-- =====================================================
                TABLE
            ====================================================== --}}

            <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

                <div
                    class="flex flex-col gap-3 border-b border-slate-200 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">

                    <div>

                        <h3 class="font-semibold text-slate-800">
                            Routing Rules
                        </h3>

                        <p class="text-sm text-slate-500">
                            {{ $rules->total() }} rule(s) found.
                        </p>

                    </div>

                </div>


                <div class="overflow-x-auto">

                    <table class="min-w-full divide-y divide-slate-200">

                        <thead class="bg-slate-50">

                            <tr>

                                <th
                                    class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Rule
                                </th>

                                <th
                                    class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Project
                                </th>

                                <th
                                    class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Scope
                                </th>

                                <th
                                    class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Matching
                                </th>

                                <th
                                    class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Level
                                </th>

                                <th
                                    class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Target
                                </th>

                                <th
                                    class="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Status
                                </th>

                                <th
                                    class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">
                                    Actions
                                </th>

                            </tr>

                        </thead>


                        <tbody class="divide-y divide-slate-100 bg-white">

                            @forelse($rules as $rule)

                            <tr class="hover:bg-slate-50">

                                {{-- Rule --}}
                                <td class="whitespace-nowrap px-5 py-4">

                                    <div class="font-semibold text-slate-800">
                                        {{ $rule->rule_code }}
                                    </div>

                                    <div class="mt-1 text-xs text-slate-500">
                                        {{ $rule->rule_name }}
                                    </div>

                                    @if($rule->is_default)

                                    <span
                                        class="mt-2 inline-flex rounded-full bg-amber-50 px-2 py-1 text-xs font-medium text-amber-700">
                                        Default
                                    </span>

                                    @endif

                                </td>


                                {{-- Project --}}
                                <td class="px-5 py-4">

                                    <div class="text-sm font-medium text-slate-700">

                                        @if($rule->project)
                                        {{ $rule->project->project_name }}
                                        @else
                                        -
                                        @endif

                                    </div>

                                </td>


                                {{-- Scope --}}
                                <td class="px-5 py-4">

                                    <div class="space-y-1 text-xs text-slate-600">

                                        <div>
                                            <span class="font-medium">
                                                Config:
                                            </span>

                                            {{ optional($rule->supportConfig)->configuration_name ?? '-' }}
                                        </div>

                                        <div>
                                            <span class="font-medium">
                                                Application:
                                            </span>

                                            {{ optional($rule->application)->application_name ?? '-' }}
                                        </div>

                                        <div>
                                            <span class="font-medium">
                                                State:
                                            </span>

                                            {{ optional($rule->state)->state_name ?? '-' }}
                                        </div>

                                    </div>

                                </td>


                                {{-- Matching --}}
                                <td class="px-5 py-4">

                                    <div class="space-y-1 text-xs">

                                        <div>
                                            <span class="font-medium text-slate-600">
                                                Category:
                                            </span>

                                            <span class="text-slate-500">
                                                {{ $rule->issue_category ?: 'Any' }}
                                            </span>
                                        </div>

                                        <div>
                                            <span class="font-medium text-slate-600">
                                                Type:
                                            </span>

                                            <span class="text-slate-500">
                                                {{ $rule->issue_type ?: 'Any' }}
                                            </span>
                                        </div>

                                        <div>
                                            <span class="font-medium text-slate-600">
                                                Priority:
                                            </span>

                                            <span class="text-slate-500">
                                                {{ $rule->priority ?: 'Any' }}
                                            </span>
                                        </div>

                                    </div>

                                </td>


                                {{-- Level --}}
                                <td class="px-5 py-4 text-center">

                                    <span
                                        class="inline-flex rounded-lg bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700">
                                        L{{ $rule->routing_level }}
                                    </span>

                                </td>


                                {{-- Target --}}
                                <td class="px-5 py-4">

                                    @if($rule->hoit_id)

                                    <div class="text-sm font-medium text-slate-700">
                                        HO IT
                                    </div>

                                    <div class="text-xs text-slate-500">
                                        {{ optional($rule->hoIt)->hoit_name ?? 'HO IT #' . $rule->hoit_id }}
                                    </div>

                                    @elseif($rule->vendor_id)

                                    <div class="text-sm font-medium text-slate-700">
                                        Vendor
                                    </div>

                                    <div class="text-xs text-slate-500">
                                        {{ optional($rule->vendor)->vendor_name ?? 'Vendor #' . $rule->vendor_id }}
                                    </div>

                                    @else

                                    <span class="text-sm text-slate-400">
                                        Not configured
                                    </span>

                                    @endif

                                </td>


                                {{-- Status --}}
                                <td class="px-5 py-4 text-center">

                                    @if($rule->is_active)

                                    <span
                                        class="inline-flex rounded-full bg-green-50 px-2.5 py-1 text-xs font-semibold text-green-700">
                                        Active
                                    </span>

                                    @else

                                    <span
                                        class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">
                                        Inactive
                                    </span>

                                    @endif

                                </td>


                                {{-- Actions --}}
                                <td class="px-5 py-4">

                                    <div class="flex items-center justify-end gap-2">

                                        {{-- View --}}
                                        <a href="{{ route(
                                                    'admin.issue-routing-rules.show',
                                                    $rule
                                                ) }}" title="View"
                                            class="rounded-lg p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-800">

                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />

                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>

                                        </a>


                                        {{-- Edit --}}
                                        <a href="{{ route(
                                                    'admin.issue-routing-rules.edit',
                                                    $rule
                                                ) }}" title="Edit"
                                            class="rounded-lg p-2 text-indigo-500 hover:bg-indigo-50 hover:text-indigo-700">

                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5" />

                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z" />
                                            </svg>

                                        </a>


                                        {{-- Toggle --}}
                                        <form method="POST" action="{{ route(
                                                    'admin.issue-routing-rules.toggle',
                                                    $rule
                                                ) }}" class="inline">

                                            @csrf
                                            @method('PATCH')

                                            <button type="submit"
                                                title="{{ $rule->is_active ? 'Deactivate' : 'Activate' }}"
                                                onclick="return confirm('{{ $rule->is_active ? 'Deactivate this routing rule?' : 'Activate this routing rule?' }}')"
                                                class="rounded-lg p-2 {{ $rule->is_active ? 'text-amber-500 hover:bg-amber-50' : 'text-green-500 hover:bg-green-50' }}">

                                                @if($rule->is_active)

                                                <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                </svg>

                                                @else

                                                <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>

                                                @endif

                                            </button>

                                        </form>


                                        {{-- Delete --}}
                                        @if(!$rule->is_active)

                                        <form method="POST" action="{{ route(
                                                        'admin.issue-routing-rules.destroy',
                                                        $rule
                                                    ) }}" class="inline">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit" title="Delete"
                                                onclick="return confirm('Delete this routing rule permanently?')"
                                                class="rounded-lg p-2 text-red-500 hover:bg-red-50 hover:text-red-700">

                                                <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m-7 0h10" />
                                                </svg>

                                            </button>

                                        </form>

                                        @endif

                                    </div>

                                </td>

                            </tr>

                            @empty

                            <tr>

                                <td colspan="8" class="px-6 py-16 text-center">

                                    <div class="mx-auto max-w-sm">

                                        <div
                                            class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100">

                                            <svg class="h-6 w-6 text-slate-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5H5a2 2 0 00-2 2v10a2 2 0 002 2h14a2 2 0 002-2V7a2 2 0 00-2-2h-4" />
                                            </svg>

                                        </div>

                                        <h3 class="mt-4 text-sm font-semibold text-slate-800">
                                            No routing rules found
                                        </h3>

                                        <p class="mt-1 text-sm text-slate-500">
                                            Create your first issue routing rule.
                                        </p>

                                        <a href="{{ route('admin.issue-routing-rules.create') }}"
                                            class="mt-5 inline-flex rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">
                                            Add Routing Rule
                                        </a>

                                    </div>

                                </td>

                            </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>


                {{-- Pagination --}}

                @if($rules->hasPages())

                <div class="border-t border-slate-200 px-6 py-4">

                    {{ $rules->links() }}

                </div>

                @endif

            </div>

        </div>

    </div>

</x-app-layout>