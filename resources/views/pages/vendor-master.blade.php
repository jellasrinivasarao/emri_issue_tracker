<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ $title ?? __('Vendor Master') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 bg-slate-50 px-5 py-5">
                    <div class="grid gap-4 md:grid-cols-[1fr_auto_auto] md:items-center">
                        <div>
                            <p class="text-sm text-slate-600">{{ $description ?? __('Manage vendor master records and vendor information.') }}</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-end rounded-2xl border border-slate-200 bg-white px-3 py-2 shadow-sm">
                                <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"></path></svg>
                                <input id="vendor-search" type="text" placeholder="Search" class="ml-2 w-36 bg-transparent text-sm text-slate-900 placeholder:text-slate-400 outline-none" />
                            </div>
                            @if(data_get($permissions, 'export'))
                                <button type="button" onclick="exportVendorTable('csv')" class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-100">Export CSV</button>
                                <button type="button" onclick="exportVendorTable('xlsx')" class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm font-semibold text-emerald-700 hover:bg-emerald-100">Export XLSX</button>
                                <button type="button" onclick="exportVendorTable('pdf')" class="rounded-lg border border-purple-200 bg-purple-50 px-3 py-2 text-sm font-semibold text-purple-700 hover:bg-purple-100">Export PDF</button>
                            @endif
                        </div>
                        <div class="flex justify-end">
                            @if(data_get($permissions, 'create'))
                                <button type="button" onclick="openVendorMasterModal()" class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">Add New</button>
                            @endif
                        </div>
                    </div>
                </div>
                @if(session('success') || session('error'))
                    <div class="px-5 py-4" id="vendor-message-container">
                        <div id="vendor-message" class="relative rounded-2xl px-4 py-3 text-sm font-semibold shadow-sm {{ session('success') ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                            <span>{{ session('success') ?? session('error') }}</span>
                            <button type="button" onclick="closeVendorMessage()" class="absolute right-3 top-3 rounded-full bg-white/80 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-white focus:outline-none">Close</button>
                        </div>
                    </div>
                @endif
                <div class="overflow-x-auto">
                    <div class="max-h-[420px] overflow-auto">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-purple-100 sticky top-0 z-10">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Vendor Name</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Category</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Contact Person</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Primary Contact</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Primary Email</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Support Mobile</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Status</th>
                                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-purple-900">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                @forelse($vendors as $vendor)
                                    <tr data-vendor-id="{{ $vendor->vendor_id }}">
                                        <td class="px-5 py-3 text-sm font-semibold text-slate-900">{{ $vendor->vendor_name }}</td>
                                        <td class="px-5 py-3 text-sm text-slate-600">{{ $vendor->category ?? '-' }}</td>
                                        <td class="px-5 py-3 text-sm text-slate-600">{{ data_get($vendor, 'contact_person', data_get($vendor, 'primary_contact_name', data_get($vendor, 'contactPerson', '-'))) }}</td>
                                        <td class="px-5 py-3 text-sm text-slate-600">{{ $vendor->primary_contact_mobile ?? '-' }}</td>
                                        <td class="px-5 py-3 text-sm text-slate-600">{{ $vendor->primary_contact_email ?? '-' }}</td>
                                        <td class="px-5 py-3 text-sm text-slate-600">{{ $vendor->support_mobile ?? '-' }}</td>
                                        <td class="px-5 py-3 text-sm">
                                            <span class="rounded-full {{ (int) $vendor->is_active === 1 ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }} px-2.5 py-1 text-xs font-semibold">
                                                {{ (int) $vendor->is_active === 1 ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3 text-sm">
                                            <div class="flex items-center gap-2 whitespace-nowrap">
                                                @if(data_get($permissions, 'edit'))
                                                    <button type="button"
                                                        data-vendor-id="{{ $vendor->vendor_id }}"
                                                        data-vendor-name="{{ $vendor->vendor_name }}"
                                                        data-category="{{ $vendor->category ?? '' }}"
                                                        data-contact-person="{{ data_get($vendor, 'contact_person', data_get($vendor, 'primary_contact_name', data_get($vendor, 'contactPerson', '')) ) }}"
                                                        data-primary-contact-mobile="{{ $vendor->primary_contact_mobile ?? '' }}"
                                                        data-primary-contact-email="{{ $vendor->primary_contact_email ?? '' }}"
                                                        data-support-mobile="{{ $vendor->support_mobile ?? '' }}"
                                                        data-description="{{ $vendor->description ?? '' }}"
                                                        data-status="{{ (int) $vendor->is_active === 1 ? 'active' : 'inactive' }}"
                                                        onclick="editVendor(this.dataset)"
                                                        class="rounded-lg bg-slate-900 px-2.5 py-1.5 text-xs font-semibold text-white hover:bg-slate-800">
                                                        Edit
                                                    </button>
                                                @endif
                                                @if((int) $vendor->is_active === 1 && data_get($permissions, 'deactivate'))
                                                    <form method="POST" action="{{ route('vendor.master.toggle', ['vendor' => $vendor->vendor_id]) }}" class="inline-flex vendor-toggle-form" data-vendor-id="{{ $vendor->vendor_id }}">
                                                        @csrf
                                                        <button type="submit" class="rounded-lg px-2.5 py-1.5 text-xs font-semibold bg-rose-100 text-rose-700 hover:bg-rose-200">
                                                            Disable
                                                        </button>
                                                    </form>
                                                @elseif((int) $vendor->is_active !== 1 && data_get($permissions, 'activate'))
                                                    <form method="POST" action="{{ route('vendor.master.toggle', ['vendor' => $vendor->vendor_id]) }}" class="inline-flex vendor-toggle-form" data-vendor-id="{{ $vendor->vendor_id }}">
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
                                        <td colspan="8" class="px-5 py-6 text-center text-sm text-slate-500">No vendors found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="vendor-master-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 px-4 py-8">
            <div class="mx-auto flex max-w-2xl flex-col rounded-3xl bg-white shadow-2xl">
            <form id="vendor-master-form" method="POST" action="">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                <div>
                    <h3 id="vendor-modal-title" class="text-lg font-semibold text-slate-900">Add Vendor</h3>
                    <p class="text-sm text-slate-600">Create or update a vendor entry.</p>
                </div>
                <button type="button" onclick="closeVendorMasterModal()" class="rounded-full p-2 bg-slate-100 text-slate-700 hover:bg-slate-200 hover:text-slate-900">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
                <div class="space-y-4 px-5 py-5">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Vendor Name <span class="text-rose-600 ml-1">*</span></label>
                        <input id="vendor_name" name="vendor_name" required aria-required="true" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400" placeholder="Enter vendor name" />
                    </div>
                <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Category <span class="text-rose-600 ml-1">*</span></label>
                        <input id="vendor_category" name="vendor_category" required aria-required="true" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400" placeholder="Enter category" />
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Contact Person <span class="text-rose-600 ml-1">*</span></label>
                        <input id="vendor_contact_person" name="vendor_contact_person" required aria-required="true" type="text" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400" placeholder="Enter contact person" />
                    </div>
                </div>
                    <div class="grid gap-4 md:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Primary Contact Mobile</label>
                        <input id="primary_contact_mobile" name="primary_contact_mobile" type="tel" pattern="^(?:[0-9]{10}|\\+91[0-9]{10})$" maxlength="13" title="Format: 10 digits or +91 followed by 10 digits" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400" placeholder="+911234567890" />
                        <p id="primary_contact_mobile_error" class="text-rose-600 text-sm mt-1 hidden">Invalid mobile. Use 10 digits or +91 followed by 10 digits.</p>
                    </div>
                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Primary Contact Email</label>
                        <input id="primary_contact_email" name="primary_contact_email" type="email" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400" placeholder="Enter email" />
                    </div>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Support Mobile</label>
                    <input id="support_mobile" name="support_mobile" type="tel" pattern="^(?:[0-9]{10}|\\+91[0-9]{10})$" maxlength="13" title="Format: 10 digits or +91 followed by 10 digits" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400" placeholder="1234567890 or +911234567890" />
                    <p id="support_mobile_error" class="text-rose-600 text-sm mt-1 hidden">Invalid mobile. Use 10 digits or +91 followed by 10 digits.</p>
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-700">Description</label>
                    <textarea id="vendor_description" name="vendor_description" rows="4" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400" placeholder="Add description"></textarea>
                </div>
                @csrf
                <input type="hidden" id="vendor_id" name="vendor_id" value="" />
                <input type="hidden" id="vendor_form_method" name="_method" value="POST" />
                <input type="hidden" id="vendor_created_at" name="created_at" value="" />
                <input type="hidden" id="vendor_updated_at" name="updated_at" value="" />
                <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-4">
                    <button type="button" onclick="closeVendorMasterModal()" class="rounded-xl border border-slate-300 bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">Cancel</button>
                    <button type="submit" class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700" id="vendor-modal-submit">Save</button>
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

        function openVendorMasterModal() {
            document.getElementById('vendor-modal-title').textContent = 'Add Vendor';
            document.getElementById('vendor-master-form').action = '{{ route('vendor.master.store') }}';
            document.getElementById('vendor_form_method').value = 'POST';
            document.getElementById('vendor_id').value = '';
            document.getElementById('vendor_name').value = '';
            document.getElementById('vendor_category').value = '';
            document.getElementById('vendor_contact_person').value = '';
            document.getElementById('primary_contact_mobile').value = '';
            document.getElementById('primary_contact_email').value = '';
            document.getElementById('support_mobile').value = '';
            document.getElementById('vendor_description').value = '';
            const now = currentTimestamp();
            const ca = document.getElementById('vendor_created_at');
            const ua = document.getElementById('vendor_updated_at');
            if (ca) ca.value = now;
            if (ua) ua.value = now;
            document.getElementById('vendor-modal-submit').textContent = 'Save';
            document.getElementById('vendor-master-modal').classList.remove('hidden');
        }

        function closeVendorMasterModal() {
            document.getElementById('vendor-master-modal').classList.add('hidden');
        }

        function editVendor(data) {
            document.getElementById('vendor-modal-title').textContent = 'Edit Vendor';
            document.getElementById('vendor-master-form').action = '{{ url('/vendor-master') }}' + '/' + (data.vendorId || '');
            document.getElementById('vendor_form_method').value = 'PUT';
            document.getElementById('vendor_id').value = data.vendorId || '';
            document.getElementById('vendor_name').value = data.vendorName || '';
            document.getElementById('vendor_category').value = data.category || '';
            document.getElementById('vendor_contact_person').value = data.contactPerson || '';
            document.getElementById('primary_contact_mobile').value = data.primaryContactMobile || '';
            document.getElementById('primary_contact_email').value = data.primaryContactEmail || '';
            document.getElementById('support_mobile').value = data.supportMobile || '';
            document.getElementById('vendor_description').value = data.description || '';
            const now = currentTimestamp();
            const ua = document.getElementById('vendor_updated_at');
            if (ua) ua.value = now;
            document.getElementById('vendor-modal-submit').textContent = 'Update';
            document.getElementById('vendor-master-modal').classList.remove('hidden');
        }

        function exportVendorTable(format) {
            const params = new URLSearchParams({ format });
            window.location.href = window.location.pathname + '?' + params.toString();
        }

        function closeVendorMessage() {
            const container = document.getElementById('vendor-message-container');
            if (container) {
                container.style.transition = 'opacity 0.4s ease';
                container.style.opacity = '0';
                setTimeout(() => container.remove(), 400);
            }
        }

        function filterVendors() {
            const query = document.getElementById('vendor-search').value.trim().toLowerCase();
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

        function showVendorMessage(message, type = 'success') {
            const existing = document.getElementById('vendor-message-container');
            if (existing) existing.remove();

            const container = document.createElement('div');
            container.id = 'vendor-message-container';
            container.className = 'px-5 py-4';

            const messageBox = document.createElement('div');
            messageBox.id = 'vendor-message';
            messageBox.className = `relative rounded-2xl px-4 py-3 text-sm font-semibold shadow-sm ${type === 'success' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700'}`;
            messageBox.innerHTML = `<span>${message}</span><button type="button" onclick="closeVendorMessage()" class="absolute right-3 top-3 rounded-full bg-white/80 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-white focus:outline-none">Close</button>`;
            container.appendChild(messageBox);

            const pageWrapper = document.querySelector('.overflow-hidden.rounded-3xl.border');
            if (pageWrapper && pageWrapper.parentNode) {
                pageWrapper.parentNode.insertBefore(container, pageWrapper);
            }
        }

        function createVendorRow(vendor) {
            return `
                                    <tr data-vendor-id="${vendor.vendor_id}">
                                        <td class="px-5 py-3 text-sm font-semibold text-slate-900">${vendor.vendor_name}</td>
                                        <td class="px-5 py-3 text-sm text-slate-600">${vendor.vendor_category || '-'}</td>
                                        <td class="px-5 py-3 text-sm text-slate-600">${vendor.contact_person || '-'}</td>
                                        <td class="px-5 py-3 text-sm text-slate-600">${vendor.primary_contact_mobile || '-'}</td>
                                        <td class="px-5 py-3 text-sm text-slate-600">${vendor.primary_contact_email || '-'}</td>
                                        <td class="px-5 py-3 text-sm text-slate-600">${vendor.support_mobile || '-'}</td>
                                        <td class="px-5 py-3 text-sm">
                                            <span class="rounded-full ${vendor.is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700'} px-2.5 py-1 text-xs font-semibold">${vendor.is_active ? 'Active' : 'Inactive'}</span>
                                        </td>
                                        <td class="px-5 py-3 text-sm">
                                            <div class="flex items-center gap-2 whitespace-nowrap">
                                                <button type="button"
                                                    data-vendor-id="${vendor.vendor_id}"
                                                    data-vendor-name="${vendor.vendor_name}"
                                                    data-category="${vendor.vendor_category || ''}"
                                                    data-contact-person="${vendor.contact_person || ''}"
                                                    data-primary-contact-mobile="${vendor.primary_contact_mobile || ''}"
                                                    data-primary-contact-email="${vendor.primary_contact_email || ''}"
                                                    data-support-mobile="${vendor.support_mobile || ''}"
                                                    data-description="${vendor.vendor_description || ''}"
                                                    data-status="${vendor.is_active ? 'active' : 'inactive'}"
                                                    onclick="editVendor(this.dataset)"
                                                    class="rounded-lg bg-slate-900 px-2.5 py-1.5 text-xs font-semibold text-white hover:bg-slate-800">
                                                    Edit
                                                </button>
                                                <form method="POST" action="/vendor-master/${vendor.vendor_id}/toggle" class="inline-flex vendor-toggle-form" data-vendor-id="${vendor.vendor_id}">
                                                    <input type="hidden" name="_token" value="${document.querySelector('input[name=_token]')?.value || ''}" />
                                                    <button type="submit" class="rounded-lg px-2.5 py-1.5 text-xs font-semibold ${vendor.is_active ? 'bg-rose-100 text-rose-700 hover:bg-rose-200' : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200'}">${vendor.is_active ? 'Disable' : 'Activate'}</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                `;
        }

        function updateVendorRow(vendor) {
            const row = document.querySelector(`tbody tr[data-vendor-id="${vendor.vendor_id}"]`);
            if (!row) {
                insertVendorRow(vendor);
                return;
            }
            const wrapper = document.createElement('tbody');
            wrapper.innerHTML = createVendorRow(vendor);
            row.replaceWith(wrapper.querySelector('tr'));
            attachVendorToggleHandlers();
        }

        function insertVendorRow(vendor) {
            const tbody = document.querySelector('tbody');
            if (!tbody) return;
            const emptyRow = tbody.querySelector('tr.empty-row');
            if (emptyRow) {
                emptyRow.remove();
            }
            const wrapper = document.createElement('tbody');
            wrapper.innerHTML = createVendorRow(vendor);
            tbody.appendChild(wrapper.querySelector('tr'));
            attachVendorToggleHandlers();
        }

        async function submitVendorFormAjax(form) {
            const action = form.action;
            const method = form.querySelector('#vendor_form_method')?.value || form.method || 'POST';
            const formData = new FormData(form);
            const response = await fetch(action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData,
                credentials: 'same-origin',
            });
            if (!response.ok) {
                const error = await response.text();
                throw new Error(error || 'Request failed');
            }
            return response.json();
        }

        async function submitVendorToggleAjax(form) {
            const action = form.action;
            const formData = new FormData(form);
            const response = await fetch(action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData,
                credentials: 'same-origin',
            });
            if (!response.ok) {
                const error = await response.text();
                throw new Error(error || 'Request failed');
            }
            return response.json();
        }

        function attachVendorToggleHandlers() {
            document.querySelectorAll('.vendor-toggle-form').forEach((form) => {
                if (form.dataset.attached === 'true') {
                    return;
                }
                form.dataset.attached = 'true';
                form.addEventListener('submit', async function (e) {
                    e.preventDefault();
                    const submitButton = form.querySelector('button[type=submit]');
                    if (submitButton) submitButton.disabled = true;
                    try {
                        const data = await submitVendorToggleAjax(form);
                        if (data.vendor) {
                            updateVendorRow(data.vendor);
                        }
                        if (data.message) {
                            showVendorMessage(data.message, 'success');
                        }
                    } catch (error) {
                        showVendorMessage('Unable to update vendor status. Please try again.', 'error');
                    } finally {
                        if (submitButton) submitButton.disabled = false;
                    }
                });
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('vendor-search');
            if (searchInput) {
                searchInput.addEventListener('input', filterVendors);
            }
            const vendorForm = document.getElementById('vendor-master-form');
            if (vendorForm) {
                vendorForm.addEventListener('submit', async function (e) {
                    const primary = document.getElementById('primary_contact_mobile');
                    const support = document.getElementById('support_mobile');
                    const primaryErr = document.getElementById('primary_contact_mobile_error');
                    const supportErr = document.getElementById('support_mobile_error');
                    const re = /^(?:[0-9]{10}|\+91[0-9]{10})$/;
                    let valid = true;

                    if (primary && primary.value.trim() !== '') {
                        if (!re.test(primary.value.trim())) {
                            primaryErr.classList.remove('hidden');
                            valid = false;
                        } else {
                            primaryErr.classList.add('hidden');
                        }
                    } else if (primaryErr) {
                        primaryErr.classList.add('hidden');
                    }

                    if (support && support.value.trim() !== '') {
                        if (!re.test(support.value.trim())) {
                            supportErr.classList.remove('hidden');
                            valid = false;
                        } else {
                            supportErr.classList.add('hidden');
                        }
                    } else if (supportErr) {
                        supportErr.classList.add('hidden');
                    }

                    if (!valid) {
                        e.preventDefault();
                        const firstInvalid = document.querySelector('.text-rose-600:not(.hidden)');
                        if (firstInvalid) firstInvalid.previousElementSibling.focus();
                        return false;
                    }

                    e.preventDefault();
                    const submitButton = document.getElementById('vendor-modal-submit');
                    if (submitButton) submitButton.disabled = true;

                    try {
                        const data = await submitVendorFormAjax(vendorForm);
                        if (data.vendor) {
                            updateVendorRow(data.vendor);
                        }
                        if (data.message) {
                            showVendorMessage(data.message, 'success');
                        }
                        closeVendorMasterModal();
                    } catch (error) {
                        showVendorMessage('Unable to save vendor. Please check the form and try again.', 'error');
                    } finally {
                        if (submitButton) submitButton.disabled = false;
                    }
                });
            }

            attachVendorToggleHandlers();

            // Real-time phone input restriction: allow either 10 digits (no prefix) or +91 followed by 10 digits
            function attachPhoneRestriction(inputId, errorId) {
                const el = document.getElementById(inputId);
                const err = document.getElementById(errorId);
                if (!el) return;
                el.addEventListener('input', function () {
                    let v = el.value || '';
                    v = v.replace(/\s+/g, '');
                    // allow leading + and digits only
                    if (v.startsWith('+')) {
                        v = '+' + v.slice(1).replace(/[^0-9]/g, '');
                        if (v.startsWith('+91')) {
                            v = '+91' + v.slice(3).slice(0, 10);
                        } else if (v.length > 1) {
                            // keep partial + input while typing the prefix, but don't allow full invalid codes
                            v = '+' + v.slice(1, 3);
                        }
                    } else {
                        // digits only, limit to 10
                        v = v.replace(/[^0-9]/g, '').slice(0, 10);
                    }
                    el.value = v;

                    // validate current value (if non-empty)
                    if (el.value.trim() !== '') {
                        const okPlain = /^[0-9]{10}$/.test(el.value);
                        const okWithPrefix = /^\+91[0-9]{10}$/.test(el.value);
                        if (!okPlain && !okWithPrefix) {
                            if (err) err.classList.remove('hidden');
                        } else {
                            if (err) err.classList.add('hidden');
                        }
                    } else {
                        if (err) err.classList.add('hidden');
                    }
                });
            }

            attachPhoneRestriction('primary_contact_mobile', 'primary_contact_mobile_error');
            attachPhoneRestriction('support_mobile', 'support_mobile_error');
        });
    </script>
</x-app-layout>
