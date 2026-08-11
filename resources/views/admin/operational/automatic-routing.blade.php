<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Automatic Routing</h2>
    </x-slot>

    @php $rules = $rules ?? collect(); $permissions = $permissions ?? []; @endphp

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 bg-slate-50 px-5 py-5">
                    <div class="grid gap-4 md:grid-cols-[1fr_auto_auto] md:items-center">
                        <div>
                            <p class="text-sm text-slate-600">Manage automatic issue routing rules.</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <div
                                class="flex items-center justify-end rounded-2xl border border-slate-200 bg-white px-3 py-2 shadow-sm">
                                <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"></path>
                                </svg>
                                <input id="routing-search" type="text" placeholder="Search"
                                    class="ml-2 w-36 bg-transparent text-sm text-slate-900 placeholder:text-slate-400 outline-none" />
                            </div>
                        </div>
                        <div class="flex justify-end">
                            @if(data_get($permissions, 'create'))
                            <button type="button" onclick="openRoutingModal()"
                                class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">Add
                                Rule</button>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <div class="max-h-[520px] overflow-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-purple-100 sticky top-0 z-10">
                                <tr>
                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">
                                        #</th>
                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">
                                        Code</th>
                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">
                                        Name</th>
                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">
                                        Priority</th>
                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">
                                        Level</th>
                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">
                                        Status</th>
                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                @php $start = is_numeric(data_get($rules, 'firstItem')) ? $rules->firstItem() : 1;
                                @endphp
                                @forelse($rules as $i => $rule)
                                <tr>
                                    <td class="px-5 py-3 text-sm font-semibold text-slate-900">{{ $start + $i }}</td>
                                    <td class="px-5 py-3 text-sm text-slate-600">{{ $rule->rule_code }}</td>
                                    <td class="px-5 py-3 text-sm text-slate-900">{{ $rule->rule_name }}</td>
                                    <td class="px-5 py-3 text-sm text-slate-600">{{ $rule->priority ?? '-' }}</td>
                                    <td class="px-5 py-3 text-sm text-slate-600">{{ $rule->routing_level ?? '-' }}</td>
                                    <td class="px-5 py-3 text-sm">
                                        <span
                                            class="rounded-full {{ (int)$rule->is_active === 1 ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }} px-2.5 py-1 text-xs font-semibold">{{ (int)$rule->is_active === 1 ? 'Active' : 'Inactive' }}</span>
                                    </td>
                                    <td class="px-5 py-3 text-sm">
                                        <div class="flex items-center gap-2">
                                            <button type="button" onclick="editRouting({{ json_encode($rule) }})"
                                                class="rounded-lg bg-slate-900 px-2.5 py-1.5 text-xs font-semibold text-white hover:bg-slate-800">Edit</button>
                                            <form method="POST"
                                                action="{{ url('/automatic-routing') }}/{{ $rule->routing_rule_id }}/toggle"
                                                class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="rounded-lg px-2.5 py-1.5 text-xs font-semibold bg-rose-50 text-rose-700">{{ (int)$rule->is_active === 1 ? 'Disable' : 'Activate' }}</button>
                                            </form>
                                            <form method="POST"
                                                action="{{ url('/automatic-routing') }}/{{ $rule->routing_rule_id }}"
                                                class="inline" onsubmit="return confirm('Delete rule?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="rounded-lg px-2.5 py-1.5 text-xs font-semibold bg-rose-50 text-rose-700">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr class="empty-row">
                                    <td colspan="7" class="px-5 py-6 text-center text-sm text-slate-500">No routing
                                        rules configured.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="px-5 py-4">
                    {{ $rules->links() }}
                </div>
            </div>
        </div>
    </div>

    <div id="routing-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 px-4 py-8">
        <div class="mx-auto flex max-w-2xl flex-col rounded-3xl bg-white shadow-2xl">
            <form id="routing-form" method="POST" action="{{ route('automatic.routing.store') }}">
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                    <div>
                        <h3 id="routing-modal-title" class="text-lg font-semibold text-slate-900">Add Routing Rule</h3>
                        <p class="text-sm text-slate-600">Create or update an automatic routing rule.</p>
                    </div>
                    <button type="button" onclick="closeRoutingModal()"
                        class="rounded-full p-2 bg-slate-100 text-slate-700 hover:bg-slate-200 hover:text-slate-900">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                @csrf
                <input type="hidden" id="routing_id" name="routing_rule_id" value="" />
                <input type="hidden" id="routing_form_method" name="_method" value="POST" />

                <div class="space-y-4 px-5 py-5">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Rule Code</label>
                            <input id="rule_code" name="rule_code" type="text" required
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400"
                                placeholder="e.g. RR-01" />
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Rule Name</label>
                            <input id="rule_name" name="rule_name" type="text" required
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400"
                                placeholder="Short description" />
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Priority</label>
                            <input id="priority" name="priority" type="text"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400"
                                placeholder="e.g. High" />
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Routing Level</label>
                            <input id="routing_level" name="routing_level" type="number" min="0"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-4">
                        <button type="button" onclick="closeRoutingModal()"
                            class="rounded-xl border border-slate-300 bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">Cancel</button>
                        <button type="submit"
                            class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700"
                            id="routing-modal-submit">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
    function openRoutingModal() {
        document.getElementById('routing-modal-title').textContent = 'Add Routing Rule';

        document.getElementById('routing-form').action =
            "{{ route('automatic.routing.store') }}";

        document.getElementById('routing_form_method').value = 'POST';
        document.getElementById('routing_id').value = '';
        document.getElementById('rule_code').value = '';
        document.getElementById('rule_name').value = '';
        document.getElementById('priority').value = '';
        document.getElementById('routing_level').value = '';

        document.getElementById('routing-modal-submit').textContent = 'Save';
        document.getElementById('routing-modal').classList.remove('hidden');
    }

    function closeRoutingModal() {
        document.getElementById('routing-modal').classList.add('hidden');
    }

    function editRouting(data) {
        const parsed = typeof data === 'string' ? JSON.parse(data) : data;

        document.getElementById('routing-modal-title').textContent = 'Edit Routing Rule';

        document.getElementById('routing-form').action =
            "{{ url('/automatic-routing') }}/" + parsed.routing_rule_id;

        document.getElementById('routing_form_method').value = 'PUT';
        document.getElementById('routing_id').value = parsed.routing_rule_id || '';
        document.getElementById('rule_code').value = parsed.rule_code || '';
        document.getElementById('rule_name').value = parsed.rule_name || '';
        document.getElementById('priority').value = parsed.priority || '';
        document.getElementById('routing_level').value = parsed.routing_level || '';

        document.getElementById('routing-modal-submit').textContent = 'Update';
        document.getElementById('routing-modal').classList.remove('hidden');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('routing-search');

        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value.trim().toLowerCase();

                document.querySelectorAll('tbody tr').forEach(function(row) {
                    if (row.classList.contains('empty-row')) {
                        return;
                    }

                    const rowText = row.textContent.toLowerCase();
                    row.classList.toggle('hidden', !rowText.includes(query));
                });
            });
        }
    });
    </script>

</x-app-layout>