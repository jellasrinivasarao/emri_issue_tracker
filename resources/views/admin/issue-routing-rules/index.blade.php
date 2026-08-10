<x-app-layout>

    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Issue Routing Rules
        </h2>
    </x-slot>

    <div class="py-8">

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">

                <div class="border-b border-slate-200 bg-slate-50 px-5 py-5">

                    <div class="grid gap-4 md:grid-cols-[1fr_auto_auto] md:items-center">

                        <div>

                            <p class="text-sm text-slate-600">
                                Configure automatic issue routing between HO IT Level 1 and Vendor Level 2.
                            </p>

                        </div>

                        <div class="flex items-center rounded-2xl border border-slate-200 bg-white px-3 py-2">

                            <span class="text-slate-500">⌕</span>

                            <input id="routing-search" type="text" placeholder="Search"
                                class="ml-2 w-36 bg-transparent text-sm outline-none">

                        </div>

                        <button type="button" onclick="openRoutingModal()"
                            class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                            Add New
                        </button>

                    </div>

                </div>


                @if(session('success'))

                <div class="px-5 py-4">

                    <div class="rounded-2xl bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-700">

                        {{ session('success') }}

                    </div>

                </div>

                @endif


                <div class="overflow-x-auto">

                    <div class="max-h-[500px] overflow-auto">

                        <table class="min-w-full divide-y divide-slate-200">

                            <thead class="sticky top-0 z-10 bg-purple-100">

                                <tr>

                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-purple-900">
                                        Rule
                                    </th>

                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-purple-900">
                                        Configuration
                                    </th>

                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-purple-900">
                                        Team
                                    </th>

                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-purple-900">
                                        Level
                                    </th>

                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-purple-900">
                                        Default
                                    </th>

                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-purple-900">
                                        Status
                                    </th>

                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-purple-900">
                                        Actions
                                    </th>

                                </tr>

                            </thead>

                            <tbody class="divide-y divide-slate-200">

                                @forelse($rules as $rule)

                                <tr>

                                    <td class="px-5 py-3">

                                        <div class="font-semibold text-slate-900">
                                            {{ $rule->rule_code }}
                                        </div>

                                        <div class="text-xs text-slate-500">
                                            {{ $rule->rule_name }}
                                        </div>

                                    </td>

                                    <td class="px-5 py-3 text-sm text-slate-600">
                                        {{ $rule->configuration?->config_name ?? '-' }}
                                    </td>

                                    <td class="px-5 py-3">

                                        <span
                                            class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700">

                                            {{ $rule->team?->team_name ?? '-' }}

                                        </span>

                                    </td>

                                    <td class="px-5 py-3 text-sm font-semibold">
                                        L{{ $rule->routing_level }}
                                    </td>

                                    <td class="px-5 py-3">

                                        @if($rule->is_default)

                                        <span
                                            class="rounded-full bg-purple-50 px-2.5 py-1 text-xs font-semibold text-purple-700">
                                            Default
                                        </span>

                                        @else

                                        <span class="text-xs text-slate-400">
                                            No
                                        </span>

                                        @endif

                                    </td>

                                    <td class="px-5 py-3">

                                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold
                                                {{ $rule->is_active
                                                    ? 'bg-emerald-50 text-emerald-700'
                                                    : 'bg-amber-50 text-amber-700' }}">

                                            {{ $rule->is_active ? 'Active' : 'Inactive' }}

                                        </span>

                                    </td>

                                    <td class="px-5 py-3">

                                        <div class="flex gap-2">

                                            <button type="button" data-id="{{ $rule->routing_rule_id }}"
                                                data-config="{{ $rule->support_config_id }}"
                                                data-team="{{ $rule->support_team_id }}"
                                                data-code="{{ $rule->rule_code }}" data-name="{{ $rule->rule_name }}"
                                                data-category="{{ $rule->issue_category }}"
                                                data-type="{{ $rule->issue_type }}"
                                                data-priority="{{ $rule->priority }}"
                                                data-level="{{ $rule->routing_level }}"
                                                data-default="{{ $rule->is_default }}"
                                                onclick="editRouting(this.dataset)"
                                                class="rounded-lg bg-slate-900 px-2.5 py-1.5 text-xs font-semibold text-white">
                                                Edit
                                            </button>

                                            <form method="POST" action="{{ route('issue.routing.toggle', $rule) }}">

                                                @csrf

                                                <button type="submit" class="rounded-lg px-2.5 py-1.5 text-xs font-semibold
                                                        {{ $rule->is_active
                                                            ? 'bg-rose-100 text-rose-700'
                                                            : 'bg-emerald-100 text-emerald-700' }}">
                                                    {{ $rule->is_active ? 'Disable' : 'Activate' }}
                                                </button>

                                            </form>

                                        </div>

                                    </td>

                                </tr>

                                @empty

                                <tr>

                                    <td colspan="7" class="px-5 py-6 text-center text-sm text-slate-500">

                                        No routing rules found.

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


    {{-- ROUTING MODAL --}}

    <div id="routing-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 px-4 py-8">

        <div class="mx-auto max-w-2xl rounded-3xl bg-white shadow-2xl">

            <form id="routing-form" method="POST" action="">

                @csrf

                <input type="hidden" id="routing-method" name="_method" value="POST">

                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">

                    <div>

                        <h3 id="routing-title" class="text-lg font-semibold text-slate-900">
                            Add Routing Rule
                        </h3>

                        <p class="text-sm text-slate-600">
                            Define the issue routing decision.
                        </p>

                    </div>

                    <button type="button" onclick="closeRoutingModal()" class="rounded-full bg-slate-100 p-2">
                        ✕
                    </button>

                </div>


                <div class="space-y-4 px-5 py-5">

                    <div class="grid gap-4 md:grid-cols-2">

                        <div>

                            <label class="mb-1 block text-sm font-medium">
                                Configuration
                            </label>

                            <select id="support_config_id" name="support_config_id" required
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm">

                                <option value="">
                                    Select Configuration
                                </option>

                                @foreach($configurations as $configuration)

                                <option value="{{ $configuration->support_config_id }}">
                                    {{ $configuration->config_name }}
                                </option>

                                @endforeach

                            </select>

                        </div>


                        <div>

                            <label class="mb-1 block text-sm font-medium">
                                Support Team
                            </label>

                            <select id="support_team_id" name="support_team_id" required
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm">

                                <option value="">
                                    Select Team
                                </option>

                                @foreach($teams as $team)

                                <option value="{{ $team->support_team_id }}">
                                    L{{ $team->support_level }} -
                                    {{ $team->team_name }}
                                </option>

                                @endforeach

                            </select>

                        </div>

                    </div>


                    <div class="grid gap-4 md:grid-cols-2">

                        <div>

                            <label class="mb-1 block text-sm font-medium">
                                Rule Code
                            </label>

                            <input id="rule_code" name="rule_code" required
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm uppercase"
                                placeholder="ROUTE_HO_IT_L1">

                        </div>

                        <div>

                            <label class="mb-1 block text-sm font-medium">
                                Rule Name
                            </label>

                            <input id="rule_name" name="rule_name" required
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm"
                                placeholder="HO IT Level 1">

                        </div>

                    </div>


                    <div class="grid gap-4 md:grid-cols-2">

                        <div>

                            <label class="mb-1 block text-sm font-medium">
                                Issue Category
                            </label>

                            <input id="issue_category" name="issue_category"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm"
                                placeholder="Optional">

                        </div>

                        <div>

                            <label class="mb-1 block text-sm font-medium">
                                Issue Type
                            </label>

                            <input id="issue_type" name="issue_type"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm"
                                placeholder="Optional">

                        </div>

                    </div>


                    <div class="grid gap-4 md:grid-cols-2">

                        <div>

                            <label class="mb-1 block text-sm font-medium">
                                Priority
                            </label>

                            <select id="priority" name="priority"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm">

                                <option value="">Any Priority</option>
                                <option value="LOW">Low</option>
                                <option value="MEDIUM">Medium</option>
                                <option value="HIGH">High</option>
                                <option value="CRITICAL">Critical</option>

                            </select>

                        </div>


                        <div>

                            <label class="mb-1 block text-sm font-medium">
                                Routing Level
                            </label>

                            <input id="routing_level" name="routing_level" type="number" min="1" required value="1"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm">

                        </div>

                    </div>


                    <label class="flex items-center gap-2">

                        <input type="checkbox" id="is_default" name="is_default" value="1"
                            class="rounded border-slate-300">

                        <span class="text-sm font-medium">
                            Default Routing Rule
                        </span>

                    </label>


                    <div class="flex justify-end gap-3 border-t border-slate-200 pt-4">

                        <button type="button" onclick="closeRoutingModal()"
                            class="rounded-xl border border-slate-300 bg-slate-100 px-4 py-2 text-sm font-semibold">
                            Cancel
                        </button>

                        <button type="submit" id="routing-submit"
                            class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white">
                            Save
                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>


    <script>
    function openRoutingModal() {

        document.getElementById('routing-title').textContent =
            'Add Routing Rule';

        document.getElementById('routing-form').action = "{{ route('issue.routing.store') }}";

        document.getElementById('routing-method').value = 'POST';

        document.getElementById('support_config_id').value = '';

        document.getElementById('support_team_id').value = '';

        document.getElementById('rule_code').value = '';

        document.getElementById('rule_name').value = '';

        document.getElementById('issue_category').value = '';

        document.getElementById('issue_type').value = '';

        document.getElementById('priority').value = '';

        document.getElementById('routing_level').value = '1';

        document.getElementById('is_default').checked = false;

        document.getElementById('routing-submit').textContent = 'Save';

        document.getElementById('routing-modal')
            .classList.remove('hidden');
    }


    function editRouting(data) {

        document.getElementById('routing-title').textContent =
            'Edit Routing Rule';

        document.getElementById('routing-form').action = "{{ url('/issue-routing') }}/" + data.id;

        document.getElementById('routing-method').value = 'PUT';

        document.getElementById('support_config_id').value =
            data.config;

        document.getElementById('support_team_id').value =
            data.team;

        document.getElementById('rule_code').value =
            data.code;

        document.getElementById('rule_name').value =
            data.name;

        document.getElementById('issue_category').value =
            data.category || '';

        document.getElementById('issue_type').value =
            data.type || '';

        document.getElementById('priority').value =
            data.priority || '';

        document.getElementById('routing_level').value =
            data.level || 1;

        document.getElementById('is_default').checked =
            data.default === '1';

        document.getElementById('routing-submit').textContent =
            'Update';

        document.getElementById('routing-modal')
            .classList.remove('hidden');
    }


    function closeRoutingModal() {

        document.getElementById('routing-modal')
            .classList.add('hidden');

    }


    document.addEventListener('DOMContentLoaded', function() {

        const search =
            document.getElementById('routing-search');

        if (search) {

            search.addEventListener('input', function() {

                const query =
                    this.value.toLowerCase().trim();

                document
                    .querySelectorAll('tbody tr')
                    .forEach(row => {

                        row.classList.toggle(
                            'hidden',
                            !row.textContent
                            .toLowerCase()
                            .includes(query)
                        );

                    });

            });

        }

    });
    </script>

</x-app-layout>