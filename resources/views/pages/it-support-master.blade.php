<x-app-layout>
    @php
        $supportRoutePrefix = match($masterType) {
            'category' => 'it.support.category',
            'device' => 'it.support.device',
            'issue_type' => 'it.support.issue.type',
            'impact' => 'it.support.impact',
            default => abort(404),
        };
    @endphp

    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ $title ?? __('IT Support Master') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 bg-slate-50 px-5 py-5">
                    <div class="grid gap-4 md:grid-cols-[1fr_auto_auto] md:items-center">
                        <div>
                            <p class="text-sm text-slate-600">{{ $description ?? __('Manage master records for the internal IT support workflow.') }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-end rounded-2xl border border-slate-200 bg-white px-3 py-2 shadow-sm">
                                <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"></path></svg>
                                <input id="support-master-search" type="text" placeholder="Search" class="ml-2 w-36 bg-transparent text-sm text-slate-900 placeholder:text-slate-400 outline-none" />
                            </div>
                            @if(data_get($permissions, 'export'))
                                <button type="button" onclick="exportSupportTable('csv')" class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-100">Export CSV</button>
                                <button type="button" onclick="exportSupportTable('xlsx')" class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-semibold text-emerald-700 hover:bg-emerald-100">Export XLSX</button>
                                <button type="button" onclick="exportSupportTable('pdf')" class="rounded-lg border border-purple-200 bg-purple-50 px-3 py-2 text-sm font-semibold text-purple-700 hover:bg-purple-100">Export PDF</button>
                            @endif
                        </div>
                        <div class="flex justify-end">
                            @if(data_get($permissions, 'create'))
                                <button type="button" onclick="openSupportMasterModal()" class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">Add New</button>
                            @endif
                        </div>
                    </div>
                </div>

                @if(session('success') || session('error'))
                    <div class="px-5 py-4" id="support-message-container">
                        <div id="support-message" class="relative rounded-2xl px-4 py-3 text-sm font-semibold shadow-sm {{ session('success') ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                            <span>{{ session('success') ?? session('error') }}</span>
                            <button type="button" onclick="closeSupportMessage()" class="absolute right-3 top-3 rounded-full bg-white/80 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-white focus:outline-none">Close</button>
                        </div>
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <div class="max-h-[420px] overflow-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-purple-100 sticky top-0 z-10">
                                <tr>
                                    @if($masterType === 'issue_type')
                                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Category</th>
                                    @endif
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">{{ $entityLabel ?? 'Name' }}</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Code</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Description</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Status</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                @forelse($records as $record)
                                    @php
                                        $nameField = match($masterType) {
                                            'category' => 'category_name',
                                            'device' => 'device_name',
                                            'issue_type' => 'issue_type_name',
                                            'impact' => 'impact_name',
                                            default => 'name',
                                        };
                                        $codeField = match($masterType) {
                                            'category' => 'category_code',
                                            'device' => 'device_code',
                                            'issue_type' => 'issue_type_code',
                                            'impact' => 'impact_code',
                                            default => 'code',
                                        };
                                        $descField = 'description';
                                        $idField = match($masterType) {
                                            'category' => 'category_id',
                                            'device' => 'device_type_id',
                                            'issue_type' => 'issue_type_id',
                                            'impact' => 'impact_id',
                                            default => 'id',
                                        };
                                        $statusField = 'is_active';
                                        $categoryName = $record->support_category_name ?? '-';
                                    @endphp
                                    <tr>
                                        @if($masterType === 'issue_type')
                                            <td class="px-5 py-3 text-sm text-slate-600">{{ $categoryName }}</td>
                                        @endif
                                        <td class="px-5 py-3 text-sm font-semibold text-slate-900">{{ $record->$nameField ?? '-' }}</td>
                                        <td class="px-5 py-3 text-sm text-slate-600">{{ $record->$codeField ?? '-' }}</td>
                                        <td class="px-5 py-3 text-sm text-slate-600">{{ $record->$descField ?? '-' }}</td>
                                        <td class="px-5 py-3 text-sm">
                                            <span class="rounded-full {{ (int) ($record->$statusField ?? 0) === 1 ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }} px-2.5 py-1 text-xs font-semibold">
                                                {{ (int) ($record->$statusField ?? 0) === 1 ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3 text-sm">
                                            <div class="flex flex-wrap items-center gap-2">
                                                @if(data_get($permissions, 'edit'))
                                                    <button type="button"
                                                        data-id="{{ $record->$idField }}"
                                                        data-name="{{ $record->$nameField ?? '' }}"
                                                        data-code="{{ $record->$codeField ?? '' }}"
                                                        data-category-id="{{ $record->category_id ?? '' }}"
                                                        data-description="{{ $record->$descField ?? '' }}"
                                                        onclick="editSupport(this.dataset)"
                                                        class="rounded-lg bg-slate-900 px-2.5 py-1.5 text-xs font-semibold text-white hover:bg-slate-800">
                                                        Edit
                                                    </button>
                                                @endif

                                                @if((int) ($record->$statusField ?? 0) === 1 && data_get($permissions, 'deactivate'))
                                                    <form method="POST" action="{{ route($supportRoutePrefix . '.toggle', ['id' => $record->$idField]) }}" class="inline">
                                                        @csrf
                                                        <button type="submit" class="rounded-lg px-2.5 py-1.5 text-xs font-semibold bg-rose-100 text-rose-700 hover:bg-rose-200">Disable</button>
                                                    </form>
                                                @elseif((int) ($record->$statusField ?? 0) !== 1 && data_get($permissions, 'activate'))
                                                    <form method="POST" action="{{ route($supportRoutePrefix . '.toggle', ['id' => $record->$idField]) }}" class="inline">
                                                        @csrf
                                                        <button type="submit" class="rounded-lg px-2.5 py-1.5 text-xs font-semibold bg-emerald-100 text-emerald-700 hover:bg-emerald-200">Activate</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="empty-row">
                                        <td colspan="{{ $masterType === 'issue_type' ? 6 : 5 }}" class="px-5 py-6 text-center text-sm text-slate-500">No records found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="support-master-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 px-4 py-8">
        <div class="mx-auto flex max-w-2xl flex-col rounded-3xl bg-white shadow-2xl">
            <form id="support-master-form" method="POST" action="{{ route($supportRoutePrefix . '.store') }}">
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                    <div>
                        <h3 id="support-modal-title" class="text-lg font-semibold text-slate-900">Add {{ $entityLabel }}</h3>
                        <p class="text-sm text-slate-600">Create or update the {{ strtolower($entityLabel) }} entry.</p>
                    </div>
                    <button type="button" onclick="closeSupportMasterModal()" class="rounded-full p-2 bg-slate-100 text-slate-700 hover:bg-slate-200 hover:text-slate-900">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                @csrf
                <input type="hidden" id="support_id" name="id" value="" />
                <input type="hidden" id="support_form_method" name="_method" value="POST" />

                <div class="space-y-4 px-5 py-5">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">{{ $entityLabel }} Name</label>
                            <input id="support_name" name="{{ match($masterType) { 'category' => 'category_name', 'device' => 'device_name', 'issue_type' => 'issue_type_name', 'impact' => 'impact_name', default => 'name' } }}" type="text" required class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400" placeholder="Enter {{ strtolower($entityLabel) }} name" />
                        </div>
                        @if($masterType === 'issue_type')
                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700">Category</label>
                                <select id="support_category_id" name="category_id" required class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400">
                                    <option value="">Select category</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->category_id }}">{{ $category->category_name }} ({{ $category->category_code }})</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Code</label>
                            <input id="support_code" name="{{ match($masterType) { 'category' => 'category_code', 'device' => 'device_code', 'issue_type' => 'issue_type_code', 'impact' => 'impact_code', default => 'code' } }}" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400" placeholder="Enter code" />
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Description</label>
                        <textarea id="support_description" name="description" rows="4" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400" placeholder="Enter description"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-4">
                        <button type="button" onclick="closeSupportMasterModal()" class="rounded-xl border border-slate-300 bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">Cancel</button>
                        <button type="submit" class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700" id="support-modal-submit">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        const supportMasterType = '{{ $masterType }}';
        const supportStoreRoute = '{{ route($supportRoutePrefix . '.store') }}';
        const supportBaseUrl = '{{ url('/it-support-' . str_replace('_', '-', $masterType) . '-master') }}';

        function openSupportMasterModal() {
            document.getElementById('support-modal-title').textContent = 'Add {{ $entityLabel }}';
            document.getElementById('support-master-form').action = supportStoreRoute;
            document.getElementById('support_form_method').value = 'POST';
            document.getElementById('support_id').value = '';
            document.getElementById('support_name').value = '';
            document.getElementById('support_code').value = '';
            const categorySelect = document.getElementById('support_category_id');
            if (categorySelect) {
                categorySelect.value = '';
            }
            document.getElementById('support_description').value = '';
            document.getElementById('support-modal-submit').textContent = 'Save';
            document.getElementById('support-master-modal').classList.remove('hidden');
        }

        function closeSupportMasterModal() {
            document.getElementById('support-master-modal').classList.add('hidden');
        }

        function editSupport(data) {
            document.getElementById('support-modal-title').textContent = 'Edit {{ $entityLabel }}';
            document.getElementById('support-master-form').action = supportBaseUrl + '/' + data.id;
            document.getElementById('support_form_method').value = 'PUT';
            document.getElementById('support_id').value = data.id;
            document.getElementById('support_name').value = data.name || '';
            document.getElementById('support_code').value = data.code || '';
            const categorySelect = document.getElementById('support_category_id');
            if (categorySelect) {
                categorySelect.value = data.categoryId || '';
            }
            document.getElementById('support_description').value = data.description || '';
            document.getElementById('support-modal-submit').textContent = 'Update';
            document.getElementById('support-master-modal').classList.remove('hidden');
        }

        function exportSupportTable(format) {
            const params = new URLSearchParams({ format });
            window.location.href = '{{ route($supportRoutePrefix . '.master') }}?' + params.toString();
        }

        function closeSupportMessage() {
            const container = document.getElementById('support-message-container');
            if (container) {
                container.style.transition = 'opacity 0.4s ease';
                container.style.opacity = '0';
                setTimeout(() => container.remove(), 400);
            }
        }

        function filterSupportRows() {
            const query = document.getElementById('support-master-search').value.trim().toLowerCase();
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

        document.getElementById('support-master-search')?.addEventListener('input', filterSupportRows);
    </script>
</x-app-layout>
