<x-app-layout>

    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ $title ?? __('Project Support Configuration') }}
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
                                {{ $description ?? 'Configure project support routing.' }}
                            </p>
                        </div>

                        <div
                            class="flex items-center justify-end rounded-2xl border border-slate-200 bg-white px-3 py-2 shadow-sm">

                            <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">

                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" />

                            </svg>

                            <input id="support-search" type="text" placeholder="Search"
                                class="ml-2 w-36 bg-transparent text-sm text-slate-900 placeholder:text-slate-400 outline-none" />

                        </div>

                        <button type="button" onclick="openSupportModal()"
                            class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">
                            Add New
                        </button>

                    </div>

                </div>


                {{-- EXPORT --}}
                <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">

                    <div class="flex flex-wrap items-center gap-3">

                        <button type="button" onclick="exportSupportTable('csv')"
                            class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-100">
                            Export CSV
                        </button>

                        <button type="button" onclick="exportSupportTable('xlsx')"
                            class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-semibold text-emerald-700 hover:bg-emerald-100">
                            Export XLSX
                        </button>

                        <button type="button" onclick="exportSupportTable('pdf')"
                            class="rounded-lg border border-purple-200 bg-purple-50 px-3 py-2 text-sm font-semibold text-purple-700 hover:bg-purple-100">
                            Export PDF
                        </button>

                    </div>

                </div>


                {{-- MESSAGE --}}
                @if(session('success') || session('error'))

                <div class="px-5 py-4" id="support-message-container">

                    <div id="support-message" class="relative rounded-2xl px-4 py-3 text-sm font-semibold shadow-sm
                            {{ session('success')
                                ? 'bg-emerald-50 text-emerald-700'
                                : 'bg-rose-50 text-rose-700' }}">

                        <span>
                            {{ session('success') ?? session('error') }}
                        </span>

                        <button type="button" onclick="closeSupportMessage()"
                            class="absolute right-3 top-3 rounded-full bg-white/80 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-white">
                            Close
                        </button>

                    </div>

                </div>

                @endif


                {{-- TABLE --}}
                <div class="overflow-x-auto">

                    <div class="max-h-[420px] overflow-auto">

                        <table class="min-w-full divide-y divide-slate-200">

                            <thead class="sticky top-0 z-10 bg-purple-100">

                                <tr>

                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">
                                        Project
                                    </th>

                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">
                                        Code
                                    </th>

                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">
                                        Configuration
                                    </th>

                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">
                                        Level
                                    </th>

                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">
                                        Team
                                    </th>

                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">
                                        SLA
                                    </th>

                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">
                                        Status
                                    </th>

                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">
                                        Actions
                                    </th>

                                </tr>

                            </thead>


                            <tbody class="divide-y divide-slate-200 bg-white">

                                @forelse($configurations as $configuration)

                                <tr>

                                    <td class="px-5 py-3 text-sm font-semibold text-slate-900">
                                        {{ $configuration->project_id }}
                                    </td>

                                    <td class="px-5 py-3 text-sm text-slate-600">
                                        {{ $configuration->configuration_code }}
                                    </td>

                                    <td class="px-5 py-3 text-sm text-slate-600">
                                        {{ $configuration->configuration_name }}
                                    </td>

                                    <td class="px-5 py-3 text-sm text-slate-600">
                                        Level {{ $configuration->default_support_level }}
                                    </td>

                                    <td class="px-5 py-3 text-sm text-slate-600">
                                        {{ $configuration->default_team_type }}
                                    </td>

                                    <td class="px-5 py-3 text-sm text-slate-600">
                                        {{ $configuration->sla_hours ?? '-' }} hrs
                                    </td>

                                    <td class="px-5 py-3 text-sm">

                                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold
                                                {{ $configuration->is_active
                                                    ? 'bg-emerald-50 text-emerald-700'
                                                    : 'bg-amber-50 text-amber-700' }}">
                                            {{ $configuration->is_active ? 'Active' : 'Inactive' }}
                                        </span>

                                    </td>

                                    <td class="px-5 py-3 text-sm">

                                        <div class="flex flex-wrap items-center gap-2">

                                            <button type="button"
                                                data-id="{{ $configuration->support_configuration_id }}"
                                                data-project-id="{{ $configuration->project_id }}"
                                                data-code="{{ $configuration->configuration_code }}"
                                                data-name="{{ $configuration->configuration_name }}"
                                                data-level="{{ $configuration->default_support_level }}"
                                                data-team="{{ $configuration->default_team_type }}"
                                                data-sla="{{ $configuration->sla_hours }}"
                                                data-description="{{ $configuration->description }}"
                                                onclick="editSupport(this.dataset)"
                                                class="rounded-lg bg-slate-900 px-2.5 py-1.5 text-xs font-semibold text-white hover:bg-slate-800">
                                                Edit
                                            </button>


                                            <form method="POST"
                                                action="{{ route('project-support-configurations.toggle', $configuration->support_configuration_id) }}"
                                                class="inline">

                                                @csrf

                                                <button type="submit"
                                                    class="rounded-lg px-2.5 py-1.5 text-xs font-semibold
                                                        {{ $configuration->is_active
                                                            ? 'bg-rose-100 text-rose-700 hover:bg-rose-200'
                                                            : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' }}">
                                                    {{ $configuration->is_active ? 'Disable' : 'Activate' }}
                                                </button>

                                            </form>

                                        </div>

                                    </td>

                                </tr>

                                @empty

                                <tr class="empty-row">

                                    <td colspan="8" class="px-5 py-6 text-center text-sm text-slate-500">

                                        No project support configurations found.

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

    <div id="support-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 px-4 py-8">

        <div class="mx-auto flex max-w-2xl flex-col rounded-3xl bg-white shadow-2xl">

            <form id="support-form" method="POST" action="">

                @csrf

                <input type="hidden" id="support_form_method" name="_method" value="POST" />

                <input type="hidden" id="support_configuration_id" name="support_configuration_id" />


                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">

                    <div>

                        <h3 id="support-modal-title" class="text-lg font-semibold text-slate-900">
                            Add Project Support Configuration
                        </h3>

                        <p class="text-sm text-slate-600">
                            Configure project support routing defaults.
                        </p>

                    </div>


                    <button type="button" onclick="closeSupportModal()"
                        class="rounded-full bg-slate-100 p-2 text-slate-700 hover:bg-slate-200">
                        ✕
                    </button>

                </div>


                <div class="space-y-4 px-5 py-5">

                    <div class="grid gap-4 md:grid-cols-2">

                        <div>

                            <label class="mb-1 block text-sm font-medium text-slate-700">
                                Project ID
                            </label>

                            <input id="project_id" name="project_id" type="number" required
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400" />

                        </div>


                        <div>

                            <label class="mb-1 block text-sm font-medium text-slate-700">
                                Configuration Code
                            </label>

                            <input id="configuration_code" name="configuration_code" type="text" required
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm uppercase outline-none focus:border-slate-400" />

                        </div>

                    </div>


                    <div>

                        <label class="mb-1 block text-sm font-medium text-slate-700">
                            Configuration Name
                        </label>

                        <input id="configuration_name" name="configuration_name" type="text" required
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400" />

                    </div>


                    <div class="grid gap-4 md:grid-cols-3">

                        <div>

                            <label class="mb-1 block text-sm font-medium text-slate-700">
                                Support Level
                            </label>

                            <select id="default_support_level" name="default_support_level"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm">
                                <option value="1">Level 1</option>
                                <option value="2">Level 2</option>
                                <option value="3">Level 3</option>
                            </select>

                        </div>


                        <div>

                            <label class="mb-1 block text-sm font-medium text-slate-700">
                                Team Type
                            </label>

                            <select id="default_team_type" name="default_team_type"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm">

                                <option value="HO_IT">
                                    HO IT
                                </option>

                                <option value="VENDOR">
                                    Vendor
                                </option>

                            </select>

                        </div>


                        <div>

                            <label class="mb-1 block text-sm font-medium text-slate-700">
                                SLA Hours
                            </label>

                            <input id="sla_hours" name="sla_hours" type="number" step="0.01" min="0"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm" />

                        </div>

                    </div>


                    <div>

                        <label class="mb-1 block text-sm font-medium text-slate-700">
                            Description
                        </label>

                        <textarea id="description" name="description" rows="3"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm"></textarea>

                    </div>


                    <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-4">

                        <button type="button" onclick="closeSupportModal()"
                            class="rounded-xl border border-slate-300 bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700">
                            Cancel
                        </button>

                        <button type="submit" id="support-modal-submit"
                            class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700">
                            Save
                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>


    <script>
    function openSupportModal() {

        document.getElementById('support-modal-title').textContent = 'Add Project Support Configuration';

        document.getElementById('support-form').action = "{{ route('project-support-configurations.store') }}";

        document.getElementById('support_form_method').value = 'POST';

        document.getElementById('support_configuration_id').value = '';

        document.getElementById('project_id').value = '';
        document.getElementById('configuration_code').value = '';
        document.getElementById('configuration_name').value = '';

        document.getElementById('default_support_level').value = '1';

        document.getElementById('default_team_type').value = 'HO_IT';

        document.getElementById('sla_hours').value = '';

        document.getElementById('description').value = '';

        document.getElementById('support-modal-submit').textContent = 'Save';

        document.getElementById('support-modal').classList.remove('hidden');
    }


    function editSupport(data) {

        document.getElementById('support-modal-title').textContent = 'Edit Project Support Configuration';

        document.getElementById('support-form').action = "{{ route('project-support-configurations.update', ':id') }}"
            .replace(':id', data.id);

        document.getElementById('support_form_method').value = 'PUT';

        document.getElementById('support_configuration_id').value = data.id;

        document.getElementById('project_id').value = data.projectId;

        document.getElementById('configuration_code').value = data.code;
        document.getElementById('configuration_name').value = data.name;
        document.getElementById('default_support_level').value = data.level;

        document.getElementById('default_team_type').value = data.team;

        document.getElementById('sla_hours').value = data.sla || '';

        document.getElementById('description').value = data.description || '';

        document.getElementById('support-modal-submit').textContent = 'Update';

        document.getElementById('support-modal').classList.remove('hidden');
    }


    function closeSupportModal() {

        document.getElementById('support-modal').classList.add('hidden');

    }


    function closeSupportMessage() {

        const container = document.getElementById('support-message-container');

        if (container) {

            container.style.opacity = '0';

            setTimeout(() => container.remove(), 400);

        }

    }


    function filterSupport() {

        const query =
            document.getElementById('support-search')
            .value
            .trim()
            .toLowerCase();

        const rows =
            document.querySelectorAll('tbody tr');

        let visibleCount = 0;

        rows.forEach(row => {

            if (row.classList.contains('empty-row')) {
                return;
            }

            const match =
                query === '' ||
                row.textContent.toLowerCase().includes(query);

            row.classList.toggle('hidden', !match);

            if (match) {
                visibleCount++;
            }

        });

    }


    function exportSupportTable(format) {

        const params =
            new URLSearchParams({
                format
            });

        window.location.href = "{{ route('project-support-configurations.index') }}?" + params.toString();

    }


    document.addEventListener('DOMContentLoaded', function() {

        const search =
            document.getElementById('support-search');

        if (search) {
            search.addEventListener('input', filterSupport);
        }

        const message =
            document.getElementById('support-message');

        if (message) {
            setTimeout(closeSupportMessage, 10000);
        }

    });
    </script>

</x-app-layout>