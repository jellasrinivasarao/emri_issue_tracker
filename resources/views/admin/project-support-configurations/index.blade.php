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
                                {{ $description ?? 'Manage project support configuration and issue routing.' }}
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

                    <div class="max-h-[480px] overflow-auto">

                        <table class="min-w-full divide-y divide-slate-200">

                            <thead class="sticky top-0 z-10 bg-purple-100">

                                <tr>

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
                                        Priority
                                    </th>

                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">
                                        Auto Routing
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
                                        {{ $configuration->config_code }}
                                    </td>

                                    <td class="px-5 py-3 text-sm text-slate-700">
                                        {{ $configuration->config_name }}
                                    </td>

                                    <td class="px-5 py-3 text-sm">

                                        <span
                                            class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-700">
                                            {{ $configuration->default_priority }}
                                        </span>

                                    </td>

                                    <td class="px-5 py-3 text-sm">

                                        @if($configuration->auto_routing_enabled)

                                        <span
                                            class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">
                                            Enabled
                                        </span>

                                        @else

                                        <span
                                            class="rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-600">
                                            Disabled
                                        </span>

                                        @endif

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

                                            <button type="button" data-id="{{ $configuration->support_config_id }}"
                                                data-code="{{ $configuration->config_code }}"
                                                data-name="{{ $configuration->config_name }}"
                                                data-description="{{ $configuration->description ?? '' }}"
                                                data-priority="{{ $configuration->default_priority }}"
                                                data-routing="{{ $configuration->auto_routing_enabled }}"
                                                onclick="editSupport(this.dataset)"
                                                class="rounded-lg bg-slate-900 px-2.5 py-1.5 text-xs font-semibold text-white hover:bg-slate-800">
                                                Edit
                                            </button>

                                            <a href="{{ route('project.support.show', $configuration) }}"
                                                class="rounded-lg bg-blue-100 px-2.5 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-200">
                                                View
                                            </a>

                                            <form method="POST"
                                                action="{{ route('project.support.toggle', $configuration) }}"
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

                                    <td colspan="6" class="px-5 py-6 text-center text-sm text-slate-500">

                                        No support configurations found.

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

                <input type="hidden" id="support_form_method" name="_method" value="POST">

                <input type="hidden" id="support_config_id" name="support_config_id">


                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">

                    <div>

                        <h3 id="support-modal-title" class="text-lg font-semibold text-slate-900">
                            Add Support Configuration
                        </h3>

                        <p class="text-sm text-slate-600">
                            Configure project-level issue support and routing.
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
                                Configuration Code
                            </label>

                            <input id="config_code" name="config_code" type="text" required maxlength="50"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm uppercase outline-none focus:border-slate-400"
                                placeholder="e.g. PROJECT_SUPPORT">

                        </div>

                        <div>

                            <label class="mb-1 block text-sm font-medium text-slate-700">
                                Configuration Name
                            </label>

                            <input id="config_name" name="config_name" type="text" required maxlength="150"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400"
                                placeholder="Enter configuration name">

                        </div>

                    </div>


                    <div>

                        <label class="mb-1 block text-sm font-medium text-slate-700">
                            Project ID
                        </label>

                        <input id="project_id" name="project_id" type="number"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm"
                            placeholder="Optional project ID">

                    </div>


                    <div>

                        <label class="mb-1 block text-sm font-medium text-slate-700">
                            Description
                        </label>

                        <textarea id="description" name="description" rows="3"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm"></textarea>

                    </div>


                    <div class="grid gap-4 md:grid-cols-2">

                        <div>

                            <label class="mb-1 block text-sm font-medium text-slate-700">
                                Default Priority
                            </label>

                            <select id="default_priority" name="default_priority"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm">

                                <option value="LOW">Low</option>
                                <option value="MEDIUM" selected>Medium</option>
                                <option value="HIGH">High</option>
                                <option value="CRITICAL">Critical</option>

                            </select>

                        </div>


                        <div class="flex items-center pt-7">

                            <label class="inline-flex items-center gap-2">

                                <input type="checkbox" id="auto_routing_enabled" name="auto_routing_enabled" value="1"
                                    checked class="rounded border-slate-300">

                                <span class="text-sm font-medium text-slate-700">
                                    Enable Automatic Routing
                                </span>

                            </label>

                        </div>

                    </div>


                    <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-4">

                        <button type="button" onclick="closeSupportModal()"
                            class="rounded-xl border border-slate-300 bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700">
                            Cancel
                        </button>

                        <button type="submit" id="support-modal-submit"
                            class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white">
                            Save
                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>


    <script>
    function openSupportModal() {

        document.getElementById('support-modal-title').textContent =
            'Add Support Configuration';

        document.getElementById('support-form').action = "{{ route('project.support.store') }}";

        document.getElementById('support_form_method').value = 'POST';

        document.getElementById('support_config_id').value = '';

        document.getElementById('config_code').value = '';

        document.getElementById('config_name').value = '';

        document.getElementById('project_id').value = '';

        document.getElementById('description').value = '';

        document.getElementById('default_priority').value = 'MEDIUM';

        document.getElementById('auto_routing_enabled').checked = true;

        document.getElementById('support-modal-submit').textContent = 'Save';

        document.getElementById('support-modal').classList.remove('hidden');
    }


    function editSupport(data) {

        document.getElementById('support-modal-title').textContent =
            'Edit Support Configuration';

        document.getElementById('support-form').action = "{{ url('/project-support') }}/" + data.id;

        document.getElementById('support_form_method').value = 'PUT';

        document.getElementById('support_config_id').value = data.id;

        document.getElementById('config_code').value = data.code;

        document.getElementById('config_name').value = data.name;

        document.getElementById('description').value = data.description || '';

        document.getElementById('default_priority').value =
            data.priority || 'MEDIUM';

        document.getElementById('auto_routing_enabled').checked =
            data.routing === '1' || data.routing === 'true';

        document.getElementById('support-modal-submit').textContent = 'Update';

        document.getElementById('support-modal').classList.remove('hidden');
    }


    function closeSupportModal() {

        document
            .getElementById('support-modal')
            .classList.add('hidden');

    }


    function exportSupportTable(format) {

        const params = new URLSearchParams({
            format: format
        });

        window.location.href = "{{ route('project.support') }}?" + params.toString();
    }


    function closeSupportMessage() {

        const container =
            document.getElementById('support-message-container');

        if (container) {

            container.style.transition =
                'opacity 0.4s ease';

            container.style.opacity = '0';

            setTimeout(() => container.remove(), 400);
        }
    }


    document.addEventListener('DOMContentLoaded', function() {

        const search =
            document.getElementById('support-search');

        if (search) {

            search.addEventListener('input', function() {

                const query =
                    this.value.toLowerCase().trim();

                document
                    .querySelectorAll('tbody tr')
                    .forEach(row => {

                        if (row.classList.contains('empty-row')) {
                            return;
                        }

                        row.classList.toggle(
                            'hidden',
                            !row.textContent.toLowerCase().includes(query)
                        );

                    });

            });

        }

    });
    </script>

</x-app-layout>