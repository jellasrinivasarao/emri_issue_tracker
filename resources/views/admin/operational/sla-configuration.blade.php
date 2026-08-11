<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">SLA Configuration</h2>
    </x-slot>

    @php $slas = $slas ?? collect(); $permissions = $permissions ?? []; @endphp

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 bg-slate-50 px-5 py-5">
                    <div class="grid gap-4 md:grid-cols-[1fr_auto_auto] md:items-center">
                        <div>
                            <p class="text-sm text-slate-600">Configure Service Level Agreements for issue handling.</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <div
                                class="flex items-center justify-end rounded-2xl border border-slate-200 bg-white px-3 py-2 shadow-sm">
                                <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"></path>
                                </svg>
                                <input id="sla-search" type="text" placeholder="Search"
                                    class="ml-2 w-36 bg-transparent text-sm text-slate-900 placeholder:text-slate-400 outline-none" />
                            </div>
                        </div>
                        <div class="flex justify-end">
                            @if(data_get($permissions, 'create'))
                            <button type="button" onclick="openSLAModal()"
                                class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">Add
                                SLA</button>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <div class="max-h-[420px] overflow-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-purple-100 sticky top-0 z-10">
                                <tr>
                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">
                                        SLA Name</th>
                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">
                                        Response (hrs)</th>
                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">
                                        Resolution (hrs)</th>
                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">
                                        Status</th>
                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                @forelse($slas as $sla)
                                <tr>
                                    <td class="px-5 py-3 text-sm text-slate-900">{{ $sla->sla_name ?? '-' }}</td>
                                    <td class="px-5 py-3 text-sm text-slate-600">{{ $sla->response_time_hours ?? '-' }}
                                    </td>
                                    <td class="px-5 py-3 text-sm text-slate-600">
                                        {{ $sla->resolution_time_hours ?? '-' }}</td>
                                    <td class="px-5 py-3 text-sm">
                                        <span
                                            class="rounded-full {{ (int) ($sla->is_active ?? 1) === 1 ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }} px-2.5 py-1 text-xs font-semibold">{{ (int) ($sla->is_active ?? 1) === 1 ? 'Active' : 'Inactive' }}</span>
                                    </td>
                                    <td class="px-5 py-3 text-sm">
                                        <div class="flex items-center gap-2">
                                            @if(data_get($permissions, 'edit'))
                                            <button type="button" onclick="editSLA({{ json_encode($sla) }})"
                                                class="rounded-lg bg-slate-900 px-2.5 py-1.5 text-xs font-semibold text-white hover:bg-slate-800">Edit</button>
                                            @endif
                                            @if(data_get($permissions, 'delete'))
                                            <form method="POST" action="#" class="inline">
                                                @csrf
                                                <button type="button"
                                                    class="rounded-lg px-2.5 py-1.5 text-xs font-semibold bg-rose-50 text-rose-700">Delete</button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr class="empty-row">
                                    <td colspan="5" class="px-5 py-6 text-center text-sm text-slate-500">No SLA
                                        configurations found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="sla-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 px-4 py-8">
        <div class="mx-auto flex max-w-2xl flex-col rounded-3xl bg-white shadow-2xl">
            <form id="sla-form" method="POST" action="{{ route('sla.configuration.store') }}">
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                    <div>
                        <h3 id="sla-modal-title" class="text-lg font-semibold text-slate-900">Add SLA</h3>
                        <p class="text-sm text-slate-600">Create or update SLA entries.</p>
                    </div>
                    <button type="button" onclick="closeSLAModal()"
                        class="rounded-full p-2 bg-slate-100 text-slate-700 hover:bg-slate-200 hover:text-slate-900">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                @csrf
                <input type="hidden" id="sla_id" name="sla_id" value="" />
                <input type="hidden" id="sla_form_method" name="_method" value="POST" />

                <div class="space-y-4 px-5 py-5">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">SLA Name</label>
                        <input id="sla_name" name="sla_name" type="text" required
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400"
                            placeholder="Enter SLA name" />
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Response Time (hrs)</label>
                            <input id="response_time_hours" name="response_time_hours" type="number" min="0"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400" />
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Resolution Time (hrs)</label>
                            <input id="resolution_time_hours" name="resolution_time_hours" type="number" min="0"
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-4">
                        <button type="button" onclick="closeSLAModal()"
                            class="rounded-xl border border-slate-300 bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">Cancel</button>
                        <button type="submit"
                            class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700"
                            id="sla-modal-submit">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
    function openSLAModal() {
        document.getElementById('sla-modal-title').textContent = 'Add SLA';
        document.getElementById('sla-form').action = '{{ route('sla.configuration.store') }}';
        document.getElementById('sla_form_method').value = 'POST';
        document.getElementById('sla_id').value = '';
        document.getElementById('sla_name').value = '';
        document.getElementById('response_time_hours').value = '';
        document.getElementById('resolution_time_hours').value = '';
        document.getElementById('sla-modal-submit').textContent = 'Save';
        document.getElementById('sla-modal').classList.remove('hidden');
    }

    function closeSLAModal() {
        document.getElementById('sla-modal').classList.add('hidden');
    }

    function editSLA(data) {
        const parsed = typeof data === 'string' ? JSON.parse(data) : data;
        document.getElementById('sla-modal-title').textContent = 'Edit SLA';
        document.getElementById('sla-form').action = '{{ url('/sla-configuration') }}/' + parsed.sla_id;
        document.getElementById('sla_form_method').value = 'PUT';
        document.getElementById('sla_id').value = parsed.sla_id || '';
        document.getElementById('sla_name').value = parsed.sla_name || '';
        document.getElementById('response_time_hours').value = parsed.response_time_hours || '';
        document.getElementById('resolution_time_hours').value = parsed.resolution_time_hours || '';
        document.getElementById('sla-modal-submit').textContent = 'Update';
        document.getElementById('sla-modal').classList.remove('hidden');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('sla-search');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const query = this.value.trim().toLowerCase();
                document.querySelectorAll('tbody tr').forEach(row => {
                    if (row.classList.contains('empty-row')) return;
                    row.classList.toggle('hidden', !row.textContent.toLowerCase().includes(
                        query));
                });
            });
        }
    });
    </script>
</x-app-layout>