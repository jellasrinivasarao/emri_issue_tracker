<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ $title ?? 'Project State Mapping' }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                @if(session('success') || session('error'))
                    <div class="px-5 py-4" id="mapping-message-container">
                        <div id="mapping-message" class="relative rounded-2xl px-4 py-3 text-sm font-semibold shadow-sm {{ session('success') ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                            <span>{{ session('success') ?? session('error') }}</span>
                            <button type="button" onclick="closeMappingMessage()" class="absolute right-3 top-3 rounded-full bg-white/80 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-white focus:outline-none">Close</button>
                        </div>
                    </div>
                @endif
                <div class="border-b border-slate-200 bg-slate-50 px-5 py-5">
                    <div class="grid gap-4 md:grid-cols-[1fr_auto_auto] md:items-center">
                        <div>
                            <p class="text-sm text-slate-600">{{ $description ?? 'Manage state-to-project mappings and control active mappings.' }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-end rounded-2xl border border-slate-200 bg-white px-3 py-2 shadow-sm">
                                <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"></path></svg>
                                <input id="mapping-search" type="text" placeholder="Search" class="ml-2 w-36 bg-transparent text-sm text-slate-900 placeholder:text-slate-400 outline-none" />
                            </div>
                            @if(data_get($permissions, 'export'))
                                <button type="button" class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-100">Export CSV</button>
                                <button type="button" class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-semibold text-emerald-700 hover:bg-emerald-100">Export XLSX</button>
                                <button type="button" class="rounded-lg border border-purple-200 bg-purple-50 px-3 py-2 text-sm font-semibold text-purple-700 hover:bg-purple-100">Export PDF</button>
                            @endif
                        </div>
                        <div class="flex justify-end">
                            <button type="button" onclick="openProjectStateModal()" class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">Add Mapping</button>
                        </div>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <div class="max-h-[420px] overflow-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-purple-100 sticky top-0 z-10">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">ID</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">State</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Project</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Status</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                @forelse($mappings as $mapping)
                                    <tr>
                                        <td class="px-5 py-3 text-sm font-semibold text-slate-900">{{ $mapping->mapping_id }}</td>
                                        <td class="px-5 py-3 text-sm font-semibold text-slate-900">{{ $mapping->state_name }}</td>
                                        <td class="px-5 py-3 text-sm text-slate-600">{{ $mapping->project_name }}</td>
                                        <td class="px-5 py-3 text-sm"><span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $mapping->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">{{ $mapping->is_active ? 'Active' : 'Inactive' }}</span></td>
                                        <td class="px-5 py-3 text-sm">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <button type="button"
                                                    data-mapping-id="{{ $mapping->mapping_id }}"
                                                    data-state-id="{{ $mapping->state_id }}"
                                                    data-project-id="{{ $mapping->project_id }}"
                                                    data-is-active="{{ $mapping->is_active ? '1' : '0' }}"
                                                    onclick="editProjectStateModal(this.dataset)"
                                                    class="rounded-lg bg-slate-900 px-2.5 py-1.5 text-xs font-semibold text-white hover:bg-slate-800">Edit</button>
                                                <form method="POST" action="{{ route('project.state.mapping.toggle', ['mapping_id' => $mapping->mapping_id]) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="rounded-lg px-2.5 py-1.5 text-xs font-semibold {{ $mapping->is_active ? 'bg-rose-100 text-rose-700 hover:bg-rose-200' : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' }}">
                                                        {{ $mapping->is_active ? 'Disable' : 'Activate' }}
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-5 py-6 text-center text-sm text-slate-500">No project state mappings found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="project-state-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 px-4 py-8">
        <div class="mx-auto max-w-2xl rounded-3xl bg-white shadow-2xl">
            <form id="project-state-form" method="POST" action="">
                @csrf
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                    <div>
                        <h3 id="project-state-modal-title" class="text-lg font-semibold text-slate-900">Add Mapping</h3>
                        <p class="text-sm text-slate-600">Create or update a state-to-project mapping.</p>
                    </div>
                    <button type="button" onclick="closeProjectStateModal()" class="rounded-full p-2 bg-slate-100 text-slate-700 hover:bg-slate-200 hover:text-slate-900">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="space-y-4 px-5 py-5">
                    <input type="hidden" id="mapping_id" name="mapping_id" value="" />
                    <input type="hidden" id="mapping_form_method" name="_method" value="POST" />

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">States</label>
                            <div id="state-select" class="relative">
                                <div class="flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2.5 shadow-sm transition focus-within:border-slate-400 focus-within:ring-2 focus-within:ring-emerald-100" data-multi-select="states">
                                    <div class="flex flex-wrap gap-2" data-multi-select-chips></div>
                                    <input type="text" class="min-w-[140px] flex-1 bg-transparent text-sm text-slate-900 placeholder:text-slate-400 outline-none" placeholder="Search states" data-multi-select-input autocomplete="off" />
                                </div>
                                <div class="absolute left-0 right-0 z-50 mt-1 hidden max-h-60 overflow-auto rounded-xl border border-slate-200 bg-white shadow-xl" data-multi-select-list></div>
                                <div data-multi-select-hidden class="hidden"></div>
                            </div>
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Projects</label>
                            <div id="project-select" class="relative">
                                <div class="flex flex-wrap items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2.5 shadow-sm transition focus-within:border-slate-400 focus-within:ring-2 focus-within:ring-emerald-100" data-multi-select="projects">
                                    <div class="flex flex-wrap gap-2" data-multi-select-chips></div>
                                    <input type="text" class="min-w-[140px] flex-1 bg-transparent text-sm text-slate-900 placeholder:text-slate-400 outline-none" placeholder="Search projects" data-multi-select-input autocomplete="off" />
                                </div>
                                <div class="absolute left-0 right-0 z-50 mt-1 hidden max-h-60 overflow-auto rounded-xl border border-slate-200 bg-white shadow-xl" data-multi-select-list></div>
                                <div data-multi-select-hidden class="hidden"></div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-4">
                        <button type="button" onclick="closeProjectStateModal()" class="rounded-xl border border-slate-300 bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">Close</button>
                        <button type="submit" class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700" id="project-state-modal-submit">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        const optionsEndpoint = '{{ route('project.state.mapping.options') }}';
        const formState = {
            states: [],
            projects: [],
        };

        function formatOptionLabel(item) {
            return item.state_name || item.project_name || '';
        }

        function getOptionById(list, id) {
            return list.find((item) => String(item.state_id) === String(id) || String(item.project_id) === String(id));
        }

        function initMultiSelect(name) {
            const container = document.querySelector(`[data-multi-select="${name}"]`).closest('[id$="-select"]');
            const input = container.querySelector('[data-multi-select-input]');
            const list = container.querySelector('[data-multi-select-list]');
            const chipsContainer = container.querySelector('[data-multi-select-chips]');
            const hiddenContainer = container.querySelector('[data-multi-select-hidden]');

            const selected = formState[name];
            let options = [];

            function buildOptionButton(option) {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'w-full px-3 py-2 text-left text-sm text-slate-700 hover:bg-slate-50';
                button.dataset.value = option.state_id || option.project_id;
                button.textContent = formatOptionLabel(option);
                return button;
            }

            function renderList(filter = '') {
                list.innerHTML = '';
                const query = filter.trim().toLowerCase();

                const filtered = options.filter((option) => {
                    const label = formatOptionLabel(option).toLowerCase();
                    const alreadySelected = selected.includes(String(option.state_id || option.project_id));
                    const matchesSearch = query === '' || label.includes(query);
                    return !alreadySelected && matchesSearch;
                });

                if (filtered.length === 0) {
                    const empty = document.createElement('div');
                    empty.className = 'px-3 py-2 text-sm text-slate-500';
                    empty.textContent = 'No results found.';
                    list.appendChild(empty);
                    return;
                }

                filtered.forEach((option) => list.appendChild(buildOptionButton(option)));
            }

            function renderChips() {
                chipsContainer.innerHTML = '';
                hiddenContainer.innerHTML = '';

                selected.forEach((value) => {
                    const option = getOptionById(options, value);
                    if (!option) {
                        return;
                    }

                    const chip = document.createElement('span');
                    chip.className = 'inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-sm text-slate-700';
                    chip.textContent = formatOptionLabel(option);

                    const removeButton = document.createElement('button');
                    removeButton.type = 'button';
                    removeButton.className = 'rounded-full bg-slate-200 px-1 text-slate-500 hover:bg-slate-300';
                    removeButton.textContent = '×';
                    removeButton.addEventListener('click', (event) => {
                        event.stopPropagation();
                        removeSelected(value);
                    });

                    chip.appendChild(removeButton);
                    chipsContainer.appendChild(chip);

                    const hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = name === 'states' ? 'state_ids[]' : 'project_ids[]';
                    hidden.value = value;
                    hiddenContainer.appendChild(hidden);
                });
            }

            function addSelected(value) {
                if (!selected.includes(String(value))) {
                    selected.push(String(value));
                    renderChips();
                    renderList(input.value);
                }
            }

            function removeSelected(value) {
                const index = selected.indexOf(String(value));
                if (index !== -1) {
                    selected.splice(index, 1);
                    renderChips();
                    renderList(input.value);
                }
            }

            input.addEventListener('input', () => {
                renderList(input.value);
                list.classList.remove('hidden');
            });

            input.addEventListener('focus', () => {
                renderList(input.value);
                list.classList.remove('hidden');
            });

            list.addEventListener('click', (event) => {
                const button = event.target.closest('button[data-value]');
                if (!button) {
                    return;
                }
                addSelected(button.dataset.value);
            });

            document.addEventListener('click', (event) => {
                if (!container.contains(event.target)) {
                    list.classList.add('hidden');
                }
            });

            function setItems(values = []) {
                selected.splice(0, selected.length, ...values.map(String));
                renderChips();
            }

            function setOptions(nextOptions) {
                options = nextOptions;
                renderList(input.value);
            }

            renderList();
            renderChips();

            return { setItems, setOptions };
        }

        const stateSelect = initMultiSelect('states');
        const projectSelect = initMultiSelect('projects');

        async function loadRealTimeOptions() {
            const response = await fetch(optionsEndpoint, { headers: { 'Accept': 'application/json' } });
            if (!response.ok) {
                console.error('Unable to load state/project options', response.statusText);
                return;
            }

            const data = await response.json();
            stateSelect.setOptions(data.states || []);
            projectSelect.setOptions(data.projects || []);
        }

        async function openProjectStateModal() {
            await loadRealTimeOptions();
            document.getElementById('project-state-modal-title').textContent = 'Add Mapping';
            document.getElementById('project-state-form').action = '{{ route('project.state.mapping.store') }}';
            document.getElementById('mapping_form_method').value = 'POST';
            document.getElementById('mapping_id').value = '';
            stateSelect.setItems([]);
            projectSelect.setItems([]);
            document.getElementById('project-state-modal-submit').textContent = 'Save';
            document.getElementById('project-state-modal').classList.remove('hidden');
        }

        function closeProjectStateModal() {
            document.getElementById('project-state-modal').classList.add('hidden');
        }

        function closeMappingMessage() {
            const container = document.getElementById('mapping-message-container');
            if (container) {
                container.style.transition = 'opacity 0.4s ease';
                container.style.opacity = '0';
                setTimeout(() => container.remove(), 400);
            }
        }

        async function editProjectStateModal(data) {
            await loadRealTimeOptions();
            document.getElementById('project-state-modal-title').textContent = 'Edit Mapping';
            document.getElementById('project-state-form').action = '{{ url('/project-state-mapping') }}' + '/' + (data.mappingId || '');
            document.getElementById('mapping_form_method').value = 'PUT';
            document.getElementById('mapping_id').value = data.mappingId || '';
            stateSelect.setItems([String(data.stateId || '')].filter(Boolean));
            projectSelect.setItems([String(data.projectId || '')].filter(Boolean));
            document.getElementById('project-state-modal-submit').textContent = 'Update';
            document.getElementById('project-state-modal').classList.remove('hidden');
        }

        function filterMappings() {
            const query = document.getElementById('mapping-search').value.trim().toLowerCase();
            const rows = document.querySelectorAll('tbody tr');

            rows.forEach((row) => {
                if (row.querySelector('td') === null) {
                    return;
                }

                const text = row.textContent.toLowerCase();
                const match = query === '' || text.includes(query);
                row.classList.toggle('hidden', !match);
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('mapping-search');
            if (searchInput) {
                searchInput.addEventListener('input', filterMappings);
            }
        });
    </script>
</x-app-layout>
