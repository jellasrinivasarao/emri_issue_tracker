<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ $title ?? 'Issue Category Configuration' }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 bg-slate-50 px-5 py-5">
                    <div class="grid gap-4 md:grid-cols-[1fr_auto_auto] md:items-center">
                        <div>
                            <p class="text-sm text-slate-600">Manage issue categories used for classifying issues.</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <div
                                class="flex items-center justify-end rounded-2xl border border-slate-200 bg-white px-3 py-2 shadow-sm">
                                <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"></path>
                                </svg>
                                <input id="category-search" type="text" placeholder="Search"
                                    class="ml-2 w-36 bg-transparent text-sm text-slate-900 placeholder:text-slate-400 outline-none" />
                            </div>
                        </div>
                        <div class="flex justify-end">
                            @if(data_get($permissions, 'create'))
                            <button type="button" onclick="openCategoryModal()"
                                class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">Add
                                Category</button>
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
                                        #</th>
                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">
                                        Code</th>
                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">
                                        Name</th>
                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">
                                        Description</th>
                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">
                                        Status</th>
                                    <th
                                        class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                @php $start = is_numeric(data_get($categories, 'firstItem')) ? $categories->firstItem()
                                : 1; @endphp
                                @forelse($categories as $i => $cat)
                                <tr>
                                    <td class="px-5 py-3 text-sm font-semibold text-slate-900">{{ $start + $i }}</td>
                                    <td class="px-5 py-3 text-sm text-slate-600">{{ $cat->category_code }}</td>
                                    <td class="px-5 py-3 text-sm text-slate-900">{{ $cat->category_name }}</td>
                                    <td class="px-5 py-3 text-sm text-slate-600">{{ $cat->description ?? '-' }}</td>
                                    <td class="px-5 py-3 text-sm">
                                        <span
                                            class="rounded-full {{ (int)$cat->is_active === 1 ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }} px-2.5 py-1 text-xs font-semibold">{{ (int)$cat->is_active === 1 ? 'Active' : 'Inactive' }}</span>
                                    </td>
                                    <td class="px-5 py-3 text-sm">
                                        <div class="flex items-center gap-2">
                                            @if(data_get($permissions, 'edit'))
                                            <button type="button" onclick="editCategory({{ json_encode($cat) }})"
                                                class="rounded-lg bg-slate-900 px-2.5 py-1.5 text-xs font-semibold text-white hover:bg-slate-800">Edit</button>
                                            @endif
                                            <form method="POST"
                                                action="{{ url('/issue-category-configuration') }}/{{ $cat->issue_category_id }}/toggle"
                                                class="inline">
                                                @csrf
                                                <button type="submit"
                                                    class="rounded-lg px-2.5 py-1.5 text-xs font-semibold bg-rose-50 text-rose-700">{{ (int)$cat->is_active === 1 ? 'Disable' : 'Activate' }}</button>
                                            </form>
                                            <form method="POST"
                                                action="{{ url('/issue-category-configuration') }}/{{ $cat->issue_category_id }}"
                                                class="inline" onsubmit="return confirm('Delete category?');">
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
                                    <td colspan="6" class="px-5 py-6 text-center text-sm text-slate-500">No issue
                                        categories found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="px-5 py-4">
                    {{ $categories->links() }}
                </div>
            </div>
        </div>
    </div>

    <div id="category-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 px-4 py-8">
        <div class="mx-auto flex max-w-2xl flex-col rounded-3xl bg-white shadow-2xl">
            <form id="category-form" method="POST" action="{{ route('issue.category.configuration.store') }}">
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                    <div>
                        <h3 id="category-modal-title" class="text-lg font-semibold text-slate-900">Add Category</h3>
                        <p class="text-sm text-slate-600">Create or update an issue category.</p>
                    </div>
                    <button type="button" onclick="closeCategoryModal()"
                        class="rounded-full p-2 bg-slate-100 text-slate-700 hover:bg-slate-200 hover:text-slate-900">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                @csrf
                <input type="hidden" id="category_id" name="issue_category_id" value="" />
                <input type="hidden" id="category_form_method" name="_method" value="POST" />

                <div class="space-y-4 px-5 py-5">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Category Code</label>
                            <input id="category_code" name="category_code" type="text" required
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400"
                                placeholder="e.g. CAT-01" />
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Category Name</label>
                            <input id="category_name" name="category_name" type="text" required
                                class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400"
                                placeholder="Short name" />
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Description</label>
                        <textarea id="category_description" name="description" rows="3"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400"
                            placeholder="Optional description"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-4">
                        <button type="button" onclick="closeCategoryModal()"
                            class="rounded-xl border border-slate-300 bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">Cancel</button>
                        <button type="submit"
                            class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700"
                            id="category-modal-submit">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
    function openCategoryModal() {
        document.getElementById('category-modal-title').textContent = 'Add Category';

        document.getElementById('category-form').action =
            "{{ route('issue.category.configuration.store') }}";

        document.getElementById('category_form_method').value = 'POST';
        document.getElementById('category_id').value = '';
        document.getElementById('category_code').value = '';
        document.getElementById('category_name').value = '';
        document.getElementById('category_description').value = '';
        document.getElementById('category-modal-submit').textContent = 'Save';
        document.getElementById('category-modal').classList.remove('hidden');
    }

    function closeCategoryModal() {
        document.getElementById('category-modal').classList.add('hidden');
    }

    function editCategory(data) {
        const parsed = typeof data === 'string' ? JSON.parse(data) : data;
        document.getElementById('category-modal-title').textContent = 'Edit Category';

        const editUrl = "{{ url('/issue-category-configuration') }}/" + parsed.issue_category_id;

        document.getElementById('category-form').action = editUrl;

        document.getElementById('category_form_method').value = 'PUT';
        document.getElementById('category_id').value = parsed.issue_category_id || '';
        document.getElementById('category_code').value = parsed.category_code || '';
        document.getElementById('category_name').value = parsed.category_name || '';
        document.getElementById('category_description').value = parsed.description || '';
        document.getElementById('category-modal-submit').textContent = 'Update';
        document.getElementById('category-modal').classList.remove('hidden');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('category-search');
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


    <script>
    document.addEventListener('DOMContentLoaded', function() {

        // Elements
        const modal = document.getElementById('category-modal');
        const form = document.getElementById('category-form');
        const methodInput = document.getElementById('category_form_method');
        const modalTitle = document.getElementById('category-modal-title');
        const modalSubmit = document.getElementById('category-modal-submit');

        const categoryId = document.getElementById('category_id');
        const categoryCode = document.getElementById('category_code');
        const categoryName = document.getElementById('category_name');
        const categoryDescription = document.getElementById('category_description');

        const searchInput = document.getElementById('category-search');

        // Laravel-generated URLs
        const storeUrl = "{{ route('issue.category.configuration.store') }}";
        const baseUrl = "{{ url('/issue-category-configuration') }}";


        // Open Add Category Modal
        window.openCategoryModal = function() {

            if (!modal || !form) {
                return;
            }

            modalTitle.textContent = 'Add Category';

            form.action = storeUrl;

            methodInput.value = 'POST';

            categoryId.value = '';
            categoryCode.value = '';
            categoryName.value = '';
            categoryDescription.value = '';

            modalSubmit.textContent = 'Save';

            modal.classList.remove('hidden');
        };


        // Close Modal
        window.closeCategoryModal = function() {

            if (!modal) {
                return;
            }

            modal.classList.add('hidden');
        };


        // Edit Category
        window.editCategory = function(data) {

            if (!modal || !form) {
                return;
            }

            try {

                const parsed = typeof data === 'string' ?
                    JSON.parse(data) :
                    data;

                if (!parsed || !parsed.issue_category_id) {
                    console.error('Invalid category data:', data);
                    return;
                }

                modalTitle.textContent = 'Edit Category';

                form.action = baseUrl + '/' + encodeURIComponent(
                    parsed.issue_category_id
                );

                methodInput.value = 'PUT';

                categoryId.value = parsed.issue_category_id || '';
                categoryCode.value = parsed.category_code || '';
                categoryName.value = parsed.category_name || '';
                categoryDescription.value = parsed.description || '';

                modalSubmit.textContent = 'Update';

                modal.classList.remove('hidden');

            } catch (error) {
                console.error('Unable to parse category data:', error);
            }
        };


        // Category Search
        if (searchInput) {

            const rows = document.querySelectorAll('tbody tr');

            searchInput.addEventListener('input', function() {

                const query = this.value.trim().toLowerCase();

                rows.forEach(function(row) {

                    if (row.classList.contains('empty-row')) {
                        return;
                    }

                    const text = row.textContent.toLowerCase();

                    row.classList.toggle(
                        'hidden',
                        query !== '' && !text.includes(query)
                    );
                });
            });
        }

    });
    </script>
</x-app-layout>