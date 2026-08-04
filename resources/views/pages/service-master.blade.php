<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ $title ?? __('Service Master') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 bg-slate-50 px-5 py-5">
                    <div class="grid gap-4 md:grid-cols-[1fr_auto_auto] md:items-center">
                        <div>
                            <p class="text-sm text-slate-600">{{ $description ?? __('Manage service master records and service catalog entries.') }}</p>
                        </div>
                        <div class="flex items-center justify-end rounded-2xl border border-slate-200 bg-white px-3 py-2 shadow-sm">
                            <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"></path></svg>
                            <input id="service-search" type="text" placeholder="Search" class="ml-2 w-36 bg-transparent text-sm text-slate-900 placeholder:text-slate-400 outline-none" />
                        </div>
                        <button type="button" onclick="openServiceMasterModal()" class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">Add New</button>
                    </div>
                </div>
                <div class="border-b border-slate-200 bg-slate-50 px-5 py-4">
                    <div class="flex flex-wrap items-center gap-3">
                        <button type="button" onclick="exportServiceTable('csv')" class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-100">Export CSV</button>
                        <button type="button" onclick="exportServiceTable('xlsx')" class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-semibold text-emerald-700 hover:bg-emerald-100">Export XLSX</button>
                        <button type="button" onclick="exportServiceTable('pdf')" class="rounded-lg border border-purple-200 bg-purple-50 px-3 py-2 text-sm font-semibold text-purple-700 hover:bg-purple-100">Export PDF</button>
                    </div>
                </div>
                @if(session('success') || session('error'))
                    <div class="px-5 py-4" id="service-message-container">
                        <div id="service-message" class="relative rounded-2xl px-4 py-3 text-sm font-semibold shadow-sm {{ session('success') ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                            <span>{{ session('success') ?? session('error') }}</span>
                            <button type="button" onclick="closeServiceMessage()" class="absolute right-3 top-3 rounded-full bg-white/80 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-white focus:outline-none">Close</button>
                        </div>
                    </div>
                @endif
                <div class="overflow-x-auto">
                    <div class="max-h-[420px] overflow-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-purple-100 sticky top-0 z-10">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Service ID</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Short Code</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Service Name</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Description</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Status</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                        @forelse($services as $index => $service)
                                            <tr>
                                                <td class="px-5 py-3 text-sm font-semibold text-slate-900">{{ $index + 1 }}</td>
                                                <td class="px-5 py-3 text-sm font-semibold text-slate-900">{{ $service->short_code ?? '-' }}</td>
                                                <td class="px-5 py-3 text-sm font-semibold text-slate-900">{{ $service->service_name }}</td>
                                                <td class="px-5 py-3 text-sm text-slate-600">{{ $service->description ?? '-' }}</td>
                                                <td class="px-5 py-3 text-sm">
                                                    <span class="rounded-full {{ (int)$service->is_active === 1 ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }} px-2.5 py-1 text-xs font-semibold">{{ (int)$service->is_active === 1 ? 'Active' : 'Inactive' }}</span>
                                                </td>
                                                <td class="px-5 py-3 text-sm">
                                                    <div class="flex flex-wrap items-center gap-2">
                                                        <button type="button" data-service-id="{{ $service->service_id }}" data-short-code="{{ $service->short_code ?? '' }}" data-service-name="{{ $service->service_name }}" data-description="{{ $service->description ?? '' }}" data-status="{{ (int)$service->is_active === 1 ? 'active' : 'inactive' }}" onclick="editService(this.dataset)" class="rounded-lg bg-slate-900 px-2.5 py-1.5 text-xs font-semibold text-white hover:bg-slate-800">Edit</button>
                                                        <form method="POST" action="{{ route('service.master.toggle', ['service_id' => $service->service_id]) }}" class="inline">
                                                            @csrf
                                                            <button type="submit" class="rounded-lg px-2.5 py-1.5 text-xs font-semibold {{ (int)$service->is_active === 1 ? 'bg-rose-100 text-rose-700 hover:bg-rose-200' : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' }}">{{ (int)$service->is_active === 1 ? 'Disable' : 'Activate' }}</button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr class="empty-row">
                                                <td colspan="6" class="px-5 py-6 text-center text-sm text-slate-500">No services found.</td>
                                            </tr>
                                        @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="service-master-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 px-4 py-8">
        <div class="mx-auto flex max-w-2xl flex-col rounded-3xl bg-white shadow-2xl">
            <form id="service-master-form" method="POST" action="">
                @csrf
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                <div>
                    <h3 id="service-modal-title" class="text-lg font-semibold text-slate-900">Add Service</h3>
                    <p class="text-sm text-slate-600">Create or update a service entry.</p>
                </div>
                <button type="button" onclick="closeServiceMasterModal()" class="rounded-full p-2 bg-slate-100 text-slate-700 hover:bg-slate-200 hover:text-slate-900">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
            <div class="space-y-4 px-5 py-5">
                <div id="service-id-field" class="hidden">
                    <label class="mb-1 block text-sm font-medium text-slate-700">Service ID</label>
                    <input id="service_id_display" type="text" readonly class="w-full rounded-xl border border-slate-200 bg-slate-100 px-3 py-2.5 text-sm text-slate-700 outline-none" placeholder="Service ID" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Service Name</label>
                    <input id="service_name" name="service_name" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400" placeholder="Enter service name" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Short Code</label>
                    <input id="service_code" name="short_code" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400" placeholder="Enter short code" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Description</label>
                    <textarea id="service_description" name="service_description" rows="4" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400" placeholder="Add description"></textarea>
                </div>
                <input type="hidden" id="service_created_at" name="created_at" value="" />
                <input type="hidden" id="service_updated_at" name="updated_at" value="" />
                <input type="hidden" id="service_id" name="service_id" value="" />
                <input type="hidden" id="service_form_method" name="_method" value="POST" />
                <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-4">
                    <button type="button" onclick="closeServiceMasterModal()" class="rounded-xl border border-slate-300 bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">Cancel</button>
                    <button type="submit" class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700" id="service-modal-submit">Save</button>
                </div>
            </div>
            </form>
        </div>
    </div>

    <script>
        function currentTimestamp() {
            const d = new Date();
            const pad = (n) => n.toString().padStart(2, '0');
            return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())} ${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`;
        }

        function openServiceMasterModal() {
            document.getElementById('service-modal-title').textContent = 'Add Service';
            document.getElementById('service-master-form').action = '{{ route('service.master.store') }}';
            document.getElementById('service_form_method').value = 'POST';
            document.getElementById('service_id').value = '';
            document.getElementById('service_id_display').value = '';
            document.getElementById('service-id-field').classList.add('hidden');
            document.getElementById('service_code').value = '';
            document.getElementById('service_name').value = '';
            document.getElementById('service_description').value = '';
            const now = currentTimestamp();
            const ca = document.getElementById('service_created_at');
            const ua = document.getElementById('service_updated_at');
            if (ca) ca.value = now;
            if (ua) ua.value = now;
            document.getElementById('service-modal-submit').textContent = 'Save';
            document.getElementById('service-master-modal').classList.remove('hidden');
        }

        function closeServiceMasterModal() {
            document.getElementById('service-master-modal').classList.add('hidden');
        }

        function editService(data) {
            document.getElementById('service-modal-title').textContent = 'Edit Service';
            document.getElementById('service-master-form').action = '{{ url('/service-master') }}' + '/' + (data.serviceId || '');
            document.getElementById('service_form_method').value = 'PUT';
            document.getElementById('service_id').value = data.serviceId || '';
            document.getElementById('service_id_display').value = data.serviceId || '';
            document.getElementById('service-id-field').classList.remove('hidden');
            document.getElementById('service_code').value = data.shortCode || '';
            document.getElementById('service_name').value = data.serviceName || '';
            document.getElementById('service_description').value = data.description || '';
            const now = currentTimestamp();
            const ua = document.getElementById('service_updated_at');
            if (ua) ua.value = now;
            document.getElementById('service-modal-submit').textContent = 'Update';
            document.getElementById('service-master-modal').classList.remove('hidden');
        }

        function exportServiceTable(format) {
            const params = new URLSearchParams({ format });
            window.location.href = window.location.pathname + '?' + params.toString();
        }

        function closeServiceMessage() {
            const container = document.getElementById('service-message-container');
            if (container) {
                container.style.transition = 'opacity 0.4s ease';
                container.style.opacity = '0';
                setTimeout(() => container.remove(), 400);
            }
        }

        function filterServices() {
            const query = document.getElementById('service-search').value.trim().toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            let visibleCount = 0;
            rows.forEach((row) => {
                const text = row.textContent.toLowerCase();
                const match = query === '' || text.includes(query);
                row.classList.toggle('hidden', !match);
                if (match) visibleCount += 1;
            });
            const emptyRow = document.querySelector('tbody tr.empty-row');
            if (emptyRow) emptyRow.classList.toggle('hidden', visibleCount !== 0);
        }

        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('service-search');
            if (searchInput) {
                searchInput.addEventListener('input', filterServices);
            }
        });
    </script>
</x-app-layout>
