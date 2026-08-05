<x-app-layout>

    <x-slot name="header">

        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ $title ?? __('Issue Routing Rules') }}
        </h2>

    </x-slot>


    <div class="py-8">

        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">


                {{-- HEADER --}}

                <div class="border-b border-slate-200 bg-slate-50 px-5 py-5">

                    <div class="grid gap-4 md:grid-cols-[1fr_auto_auto] md:items-center">

                        <div>

                            <p class="text-sm text-slate-600">
                                {{ $description ?? 'Configure issue routing rules.' }}
                            </p>

                        </div>


                        <div
                            class="flex items-center justify-end rounded-2xl border border-slate-200 bg-white px-3 py-2 shadow-sm">

                            <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">

                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" />

                            </svg>

                            <input id="routing-search" type="text" placeholder="Search"
                                class="ml-2 w-36 bg-transparent text-sm outline-none" />

                        </div>


                        <button type="button" onclick="openRoutingModal()"
                            class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                            Add New
                        </button>

                    </div>

                </div>


                {{-- EXPORT --}}

                <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">

                    <div class="flex flex-wrap gap-3">

                        <button onclick="exportRoutingTable('csv')"
                            class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-sm font-semibold text-blue-700">
                            Export CSV
                        </button>

                        <button onclick="exportRoutingTable('xlsx')"
                            class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-semibold text-emerald-700">
                            Export XLSX
                        </button>

                        <button onclick="exportRoutingTable('pdf')"
                            class="rounded-lg border border-purple-200 bg-purple-50 px-3 py-2 text-sm font-semibold text-purple-700">
                            Export PDF
                        </button>

                    </div>

                </div>


                {{-- MESSAGE --}}

                @if(session('success') || session('error'))

                <div id="routing-message-container" class="px-5 py-4">

                    <div id="routing-message" class="relative rounded-2xl px-4 py-3 text-sm font-semibold
                            {{ session('success')
                                ? 'bg-emerald-50 text-emerald-700'
                                : 'bg-rose-50 text-rose-700' }}">

                        {{ session('success') ?? session('error') }}

                        <button onclick="closeRoutingMessage()"
                            class="absolute right-3 top-3 rounded-full bg-white px-2 py-1 text-xs">
                            Close
                        </button>

                    </div>

                </div>

                @endif


                {{-- TABLE --}}

                <div class="overflow-x-auto">

                    <div class="max-h-[500px] overflow-auto">

                        <table class="min-w-full divide-y divide-slate-200">

                            <thead class="sticky top-0 z-10 bg-purple-100">

                                <tr>

                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-purple-900">
                                        Rule Code
                                    </th>

                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-purple-900">
                                        Rule Name
                                    </th>

                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-purple-900">
                                        Project
                                    </th>

                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-purple-900">
                                        Category
                                    </th>

                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-purple-900">
                                        Priority
                                    </th>

                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-purple-900">
                                        Level
                                    </th>

                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-purple-900">
                                        Team
                                    </th>

                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-purple-900">
                                        SLA
                                    </th>

                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-purple-900">
                                        Status
                                    </th>

                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase text-purple-900">
                                        Actions
                                    </th>

                                </tr>

                            </thead>


                            <tbody class="divide-y divide-slate-200 bg-white">

                                @forelse($rules as $rule)

                                <tr>

                                    <td class="px-5 py-3 text-sm font-semibold">
                                        {{ $rule->rule_code }}
                                    </td>

                                    <td class="px-5 py-3 text-sm">
                                        {{ $rule->rule_name }}
                                    </td>

                                    <td class="px-5 py-3 text-sm">
                                        {{ $rule->project_id }}
                                    </td>

                                    <td class="px-5 py-3 text-sm">
                                        {{ $rule->issue_category_id ?? '-' }}
                                    </td>

                                    <td class="px-5 py-3 text-sm">
                                        {{ $rule->priority_id ?? '-' }}
                                    </td>

                                    <td class="px-5 py-3 text-sm">
                                        L{{ $rule->support_level }}
                                    </td>

                                    <td class="px-5 py-3 text-sm">
                                        {{ $rule->support_team_id }}
                                    </td>

                                    <td class="px-5 py-3 text-sm">
                                        {{ $rule->sla_hours ?? '-' }} hrs
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
                                                data-code="{{ $rule->rule_code }}" data-name="{{ $rule->rule_name }}"
                                                data-project="{{ $rule->project_id }}"
                                                data-config="{{ $rule->support_configuration_id }}"
                                                data-category="{{ $rule->issue_category_id }}"
                                                data-type="{{ $rule->issue_type_id }}"
                                                data-priority="{{ $rule->priority_id }}"
                                                data-level="{{ $rule->support_level }}"
                                                data-team="{{ $rule->support_team_id }}"
                                                data-sla="{{ $rule->sla_hours }}"
                                                data-routing-priority="{{ $rule->routing_priority }}"
                                                data-description="{{ $rule->description }}"
                                                onclick="editRouting(this.dataset)"
                                                class="rounded-lg bg-slate-900 px-2.5 py-1.5 text-xs font-semibold text-white">
                                                Edit
                                            </button>


                                            <form method="POST"
                                                action="{{ route('issue-routing-rules.toggle', $rule->routing_rule_id) }}">

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

                                <tr class="empty-row">

                                    <td colspan="10" class="px-5 py-6 text-center text-sm text-slate-500">
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


    {{-- MODAL --}}

    <div id="routing-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 px-4 py-8">

        <div class="mx-auto max-w-3xl rounded-3xl bg-white shadow-2xl">

            <form id="routing-form" method="POST">

                @csrf

                <input type="hidden" id="routing_form_method" name="_method" value="POST" />

                <input type="hidden" id="routing_rule_id" name="routing_rule_id" />


                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">

                    <div>

                        <h3 id="routing-modal-title" class="text-lg font-semibold text-slate-900">
                            Add Routing Rule
                        </h3>

                        <p class="text-sm text-slate-600">
                            Configure issue routing.
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
                                Rule Code
                            </label>

                            <input id="rule_code" name="rule_code" required
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm uppercase" />

                        </div>


                        <div>

                            <label class="mb-1 block text-sm font-medium">
                                Rule Name
                            </label>

                            <input id="rule_name" name="rule_name" required
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm" />

                        </div>

                    </div>


                    <div class="grid gap-4 md:grid-cols-3">

                        <div>

                            <label class="mb-1 block text-sm font-medium">
                                Project ID
                            </label>

                            <input id="routing_project_id" name="project_id" type="number" required
                                class="w-full rounded-xl border px-3 py-2.5 text-sm" />

                        </div>


                        <div>

                            <label class="mb-1 block text-sm font-medium">
                                Category ID
                            </label>

                            <input id="issue_category_id" name="issue_category_id" type="number"
                                class="w-full rounded-xl border px-3 py-2.5 text-sm" />

                        </div>


                        <div>

                            <label class="mb-1 block text-sm font-medium">
                                Priority ID
                            </label>

                            <input id="priority_id" name="priority_id" type="number"
                                class="w-full rounded-xl border px-3 py-2.5 text-sm" />

                        </div>

                    </div>


                    <div class="grid gap-4 md:grid-cols-4">

                        <div>

                            <label class="mb-1 block text-sm font-medium">
                                Support Level
                            </label>

                            <select id="routing_support_level" name="support_level"
                                class="w-full rounded-xl border px-3 py-2.5 text-sm">

                                <option value="1">HO IT Level 1</option>

                                <option value="2">Vendor Level 2</option>

                                <option value="3">Level 3</option>

                            </select>

                        </div>


                        <div>

                            <label class="mb-1 block text-sm font-medium">
                                Support Team ID
                            </label>

                            <input id="support_team_id" name="support_team_id" type="number" required
                                class="w-full rounded-xl border px-3 py-2.5 text-sm" />

                        </div>


                        <div>

                            <label class="mb-1 block text-sm font-medium">
                                SLA Hours
                            </label>

                            <input id="routing_sla_hours" name="sla_hours" type="number" step="0.01"
                                class="w-full rounded-xl border px-3 py-2.5 text-sm" />

                        </div>


                        <div>

                            <label class="mb-1 block text-sm font-medium">
                                Routing Priority
                            </label>

                            <input id="routing_priority" name="routing_priority" type="number" value="100" required
                                class="w-full rounded-xl border px-3 py-2.5 text-sm" />

                        </div>

                    </div>


                    <div>

                        <label class="mb-1 block text-sm font-medium">
                            Description
                        </label>

                        <textarea id="routing_description" name="description" rows="3"
                            class="w-full rounded-xl border px-3 py-2.5 text-sm"></textarea>

                    </div>


                    <div class="flex justify-end gap-3 border-t pt-4">

                        <button type="button" onclick="closeRoutingModal()"
                            class="rounded-xl border bg-slate-100 px-4 py-2 text-sm font-semibold">
                            Cancel
                        </button>

                        <button id="routing-submit" type="submit"
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

        document.getElementById('routing-modal-title').textContent = 'Add Routing Rule';

        document.getElementById('routing-form').action = "{{ route('issue-routing-rules.store') }}";

        document.getElementById('routing_form_method').value = 'POST';

        document.getElementById('routing_rule_id').value = '';

        document.getElementById('rule_code').value = '';
        document.getElementById('rule_name').value = '';

        document.getElementById('routing_project_id').value = '';

        document.getElementById('issue_category_id').value = '';

        document.getElementById('priority_id').value = '';

        document.getElementById('routing_support_level').value = '1';

        document.getElementById('support_team_id').value = '';

        document.getElementById('routing_sla_hours').value = '';

        document.getElementById('routing_priority').value = '100';

        document.getElementById('routing_description').value = '';

        document.getElementById('routing-submit').textContent = 'Save';

        document.getElementById('routing-modal')
            .classList.remove('hidden');
    }


    function editRouting(data) {

        document.getElementById('routing-modal-title').textContent =
            'Edit Routing Rule';

        document.getElementById('routing-form').action = "{{ url('/issue-routing-rules') }}/" + data.id;

        document.getElementById('routing_form_method').value = 'PUT';

        document.getElementById('routing_rule_id').value =
            data.id;

        document.getElementById('rule_code').value =
            data.code;

        document.getElementById('rule_name').value =
            data.name;

        document.getElementById('routing_project_id').value =
            data.project;

        document.getElementById('issue_category_id').value =
            data.category || '';

        document.getElementById('priority_id').value =
            data.priority || '';

        document.getElementById('routing_support_level').value =
            data.level;

        document.getElementById('support_team_id').value =
            data.team;

        document.getElementById('routing_sla_hours').value =
            data.sla || '';

        document.getElementById('routing_priority').value =
            data.routingPriority;

        document.getElementById('routing_description').value =
            data.description || '';

        document.getElementById('routing-submit').textContent =
            'Update';

        document.getElementById('routing-modal')
            .classList.remove('hidden');
    }


    function closeRoutingModal() {

        document.getElementById('routing-modal')
            .classList.add('hidden');

    }


    function closeRoutingMessage() {

        const container =
            document.getElementById('routing-message-container');

        if (container) {

            container.style.opacity = '0';

            setTimeout(() => container.remove(), 400);

        }

    }


    function filterRouting() {

        const query =
            document.getElementById('routing-search')
            .value
            .trim()
            .toLowerCase();

        document.querySelectorAll('tbody tr').forEach(row => {

            if (row.classList.contains('empty-row')) {
                return;
            }

            row.classList.toggle(
                'hidden',
                !row.textContent.toLowerCase().includes(query)
            );

        });

    }


    function exportRoutingTable(format) {

        const params =
            new URLSearchParams({
                format
            });

        window.location.href = "{{ route('issue-routing-rules.index') }}?" + params.toString();

    }


    document.addEventListener('DOMContentLoaded', function() {

        const search =
            document.getElementById('routing-search');

        if (search) {
            search.addEventListener('input', filterRouting);
        }

    });
    </script>

</x-app-layout>