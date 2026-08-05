<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ $title ?? __('State Master') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 bg-slate-50 px-5 py-5">
                    <div class="grid gap-4 md:grid-cols-[1fr_auto_auto] md:items-center">
                        <div>
                            <p class="text-sm text-slate-600">{{ $description ?? __('Manage state master records and state-level organization details.') }}</p>
                        </div>
                        <div class="flex items-center justify-end rounded-2xl border border-slate-200 bg-white px-3 py-2 shadow-sm">
                            <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"></path></svg>
                            <input id="state-search" type="text" placeholder="Search" class="ml-2 w-36 bg-transparent text-sm text-slate-900 placeholder:text-slate-400 outline-none" />
                        </div>
                        @if(data_get($permissions, 'create'))
                            <button type="button" onclick="openStateMasterModal()" class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">
                                Add New
                            </button>
                        @endif
                    </div>
                </div>

                <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                    <div class="flex flex-wrap items-center gap-3">
                    @if(data_get($permissions, 'export'))
                        <button type="button" onclick="exportStateTable('csv')" class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-100">Export CSV</button>
                        <button type="button" onclick="exportStateTable('xlsx')" class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-semibold text-emerald-700 hover:bg-emerald-100">Export XLSX</button>
                        <button type="button" onclick="exportStateTable('pdf')" class="rounded-lg border border-purple-200 bg-purple-50 px-3 py-2 text-sm font-semibold text-purple-700 hover:bg-purple-100">Export PDF</button>
                    @endif
                </div>
                </div>

                @if(session('success') || session('error'))
                    <div class="px-5 py-4" id="state-message-container">
                        <div id="state-message" class="relative rounded-2xl px-4 py-3 text-sm font-semibold shadow-sm {{ session('success') ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                            <span>{{ session('success') ?? session('error') }}</span>
                            <button type="button" onclick="closeStateMessage()" class="absolute right-3 top-3 rounded-full bg-white/80 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-white focus:outline-none">
                                Close
                            </button>
                        </div>
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <div class="max-h-[420px] overflow-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-purple-100 sticky top-0 z-10">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">State Name</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">State Code</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Short Name</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Status</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                @forelse($states as $index => $state)
                                    <tr>
                                        <td class="px-5 py-3 text-sm font-semibold text-slate-900">{{ $state->state_name }}</td>
                                        <td class="px-5 py-3 text-sm text-slate-600">{{ $state->state_code }}</td>
                                        <td class="px-5 py-3 text-sm text-slate-600">{{ $state->state_short_name ?? '-' }}</td>
                                        <td class="px-5 py-3 text-sm">
                                            <span class="rounded-full {{ (int) $state->is_active === 1 ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }} px-2.5 py-1 text-xs font-semibold">
                                                {{ (int) $state->is_active === 1 ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3 text-sm">
                                            <div class="flex flex-wrap items-center gap-2">
                                                @if(data_get($permissions, 'edit'))
                                                    <button type="button"
                                                        data-state-id="{{ $state->state_id }}"
                                                        data-state-name="{{ $state->state_name }}"
                                                        data-state-code="{{ $state->state_code }}"
                                                        data-state-short-name="{{ $state->state_short_name ?? '' }}"
                                                        onclick="editState(this.dataset)"
                                                        class="rounded-lg bg-slate-900 px-2.5 py-1.5 text-xs font-semibold text-white hover:bg-slate-800">
                                                        Edit
                                                    </button>
                                                @endif

                                                @if((int) $state->is_active === 1 && data_get($permissions, 'deactivate'))
                                                    <form method="POST" action="{{ route('state.master.toggle', ['state_id' => $state->state_id]) }}" class="inline">
                                                        @csrf
                                                        <button type="submit" class="rounded-lg px-2.5 py-1.5 text-xs font-semibold bg-rose-100 text-rose-700 hover:bg-rose-200">
                                                            Disable
                                                        </button>
                                                    </form>
                                                @elseif((int) $state->is_active !== 1 && data_get($permissions, 'activate'))
                                                    <form method="POST" action="{{ route('state.master.toggle', ['state_id' => $state->state_id]) }}" class="inline">
                                                        @csrf
                                                        <button type="submit" class="rounded-lg px-2.5 py-1.5 text-xs font-semibold bg-emerald-100 text-emerald-700 hover:bg-emerald-200">
                                                            Activate
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="empty-row">
                                        <td colspan="5" class="px-5 py-6 text-center text-sm text-slate-500">No states found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="state-master-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 px-4 py-8">
        <div class="mx-auto flex max-w-2xl flex-col rounded-3xl bg-white shadow-2xl">
            <form id="state-master-form" method="POST" action="">
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                    <div>
                        <h3 id="state-modal-title" class="text-lg font-semibold text-slate-900">Add State</h3>
                        <p class="text-sm text-slate-600">Create or update a state entry in the master list.</p>
                    </div>
                    <button type="button" onclick="closeStateMasterModal()" class="rounded-full p-2 bg-slate-100 text-slate-700 hover:bg-slate-200 hover:text-slate-900">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                @csrf
                <input type="hidden" id="state_id" name="state_id" value="" />
                <input type="hidden" id="state_form_method" name="_method" value="POST" />

                <div class="space-y-4 px-5 py-5">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">State Name</label>
                            <input id="state_name" name="state_name" type="text" required class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400" placeholder="Enter state name" />
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">State Code</label>
                            <input id="state_code" name="state_code" type="text" required class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400" placeholder="e.g. AP" />
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Short Name</label>
                        <input id="state_short_name" name="state_short_name" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400" placeholder="e.g. AP" />
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-4">
                        <button type="button" onclick="closeStateMasterModal()" class="rounded-xl border border-slate-300 bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">Cancel</button>
                        <button type="submit" class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700" id="state-modal-submit">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openStateMasterModal() {
            document.getElementById('state-modal-title').textContent = 'Add State';
            document.getElementById('state-master-form').action = '{{ route('state.master.store') }}';
            document.getElementById('state_form_method').value = 'POST';
            document.getElementById('state_id').value = '';
            document.getElementById('state_name').value = '';
            document.getElementById('state_code').value = '';
            document.getElementById('state_short_name').value = '';
            document.getElementById('state-modal-submit').textContent = 'Save';
            document.getElementById('state-master-modal').classList.remove('hidden');
        }

        function closeStateMasterModal() {
            document.getElementById('state-master-modal').classList.add('hidden');
        }

        function editState(state) {
            document.getElementById('state-modal-title').textContent = 'Edit State';
            document.getElementById('state-master-form').action = '{{ url('/state-master') }}/' + state.stateId;
            document.getElementById('state_form_method').value = 'PUT';
            document.getElementById('state_id').value = state.stateId;
            document.getElementById('state_name').value = state.stateName;
            document.getElementById('state_code').value = state.stateCode;
            document.getElementById('state_short_name').value = state.stateShortName || '';
            document.getElementById('state-modal-submit').textContent = 'Update';
            document.getElementById('state-master-modal').classList.remove('hidden');
        }

        function exportStateTable(format) {
            const params = new URLSearchParams({ format });
            window.location.href = '{{ route('state.master') }}?' + params.toString();
        }

        function closeStateMessage() {
            const container = document.getElementById('state-message-container');
            if (container) {
                container.style.transition = 'opacity 0.4s ease';
                container.style.opacity = '0';
                setTimeout(() => container.remove(), 400);
            }
        }

        function filterStates() {
            const query = document.getElementById('state-search').value.trim().toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            let visibleCount = 0;

            rows.forEach((row) => {
                if (row.classList.contains('empty-row')) {
                    return;
                }

                const text = row.textContent.toLowerCase();
                const match = query === '' || text.includes(query);

                if (match) {
                    row.classList.remove('hidden');
                    visibleCount += 1;
                } else {
                    row.classList.add('hidden');
                }
            });

            const emptyRow = document.querySelector('tbody tr.empty-row');
            if (emptyRow) {
                emptyRow.classList.toggle('hidden', visibleCount !== 0);
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const message = document.getElementById('state-message');
            if (message) {
                setTimeout(closeStateMessage, 10000);
            }

            const searchInput = document.getElementById('state-search');
            if (searchInput) {
                searchInput.addEventListener('input', filterStates);
            }
        });
    </script>
</x-app-layout>
