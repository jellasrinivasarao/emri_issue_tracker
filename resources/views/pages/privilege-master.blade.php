<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ $title ?? 'Privilege Master' }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 bg-slate-50 px-5 py-5">
                    <div class="grid gap-4 md:grid-cols-[1fr_auto] md:items-center">
                        <p class="text-sm text-slate-600">{{ $description ?? 'Manage privileges, privilege codes, and access permissions.' }}</p>
                        <button type="button" onclick="openPrivilegeModal()" class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">Add New</button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-100">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-900">Code</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-900">Name</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-900">Module</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-900">Description</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-900">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse($privileges as $privilege)
                                <tr>
                                    <td class="px-5 py-3 text-sm font-semibold text-slate-900">{{ $privilege->privilege_code }}</td>
                                    <td class="px-5 py-3 text-sm text-slate-600">{{ $privilege->privilege_name }}</td>
                                    <td class="px-5 py-3 text-sm text-slate-600">{{ $privilege->module_name }}</td>
                                    <td class="px-5 py-3 text-sm text-slate-600">{{ $privilege->description ?? '-' }}</td>
                                    <td class="px-5 py-3 text-sm">{{ $privilege->is_active ? 'Active' : 'Inactive' }}</td>
                                    <td class="px-5 py-3 text-sm">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <button type="button"
                                                data-privilege-id="{{ $privilege->privilege_id }}"
                                                data-privilege-code="{{ $privilege->privilege_code }}"
                                                data-privilege-name="{{ $privilege->privilege_name }}"
                                                data-module-name="{{ $privilege->module_name }}"
                                                data-description="{{ $privilege->description }}"
                                                onclick="editPrivilege(this.dataset)"
                                                class="rounded-lg bg-slate-900 px-2.5 py-1.5 text-xs font-semibold text-white hover:bg-slate-800">Edit</button>
                                            <form method="POST" action="{{ route('privilege.master.toggle', ['privilege_id' => $privilege->privilege_id]) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="rounded-lg px-2.5 py-1.5 text-xs font-semibold {{ $privilege->is_active ? 'bg-rose-100 text-rose-700 hover:bg-rose-200' : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' }}">
                                                    {{ $privilege->is_active ? 'Deactivate' : 'Activate' }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-6 text-center text-sm text-slate-500">No privileges found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="privilege-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 px-4 py-8">
        <div class="mx-auto max-w-2xl rounded-3xl bg-white shadow-2xl">
            <form id="privilege-form" method="POST" action="">
                @csrf
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                    <div>
                        <h3 id="privilege-modal-title" class="text-lg font-semibold text-slate-900">Add Privilege</h3>
                        <p class="text-sm text-slate-600">Create or update a privilege in the system.</p>
                    </div>
                    <button type="button" onclick="closePrivilegeModal()" class="rounded-full p-2 bg-slate-100 text-slate-700 hover:bg-slate-200 hover:text-slate-900">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="space-y-4 px-5 py-5">
                    <input type="hidden" id="privilege_id" name="privilege_id" value="" />
                    <input type="hidden" id="privilege_form_method" name="_method" value="POST" />

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Privilege Code</label>
                            <input id="privilege_code" name="privilege_code" type="text" required class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400" placeholder="PRIV001" />
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Privilege Name</label>
                            <input id="privilege_name" name="privilege_name" type="text" required class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400" placeholder="View Issues" />
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Module Name</label>
                        <input id="module_name" name="module_name" type="text" required class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400" placeholder="Issue Module" />
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700">Description</label>
                        <textarea id="description" name="description" rows="4" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400" placeholder="Privilege description"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-4">
                        <button type="button" onclick="closePrivilegeModal()" class="rounded-xl border border-slate-300 bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">Cancel</button>
                        <button type="submit" class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700" id="privilege-modal-submit">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openPrivilegeModal() {
            document.getElementById('privilege-modal-title').textContent = 'Add Privilege';
            document.getElementById('privilege-form').action = '{{ route('privilege.master.store') }}';
            document.getElementById('privilege_form_method').value = 'POST';
            document.getElementById('privilege_id').value = '';
            document.getElementById('privilege_code').value = '';
            document.getElementById('privilege_name').value = '';
            document.getElementById('module_name').value = '';
            document.getElementById('description').value = '';
            document.getElementById('privilege-modal-submit').textContent = 'Save';
            document.getElementById('privilege-modal').classList.remove('hidden');
        }

        function closePrivilegeModal() {
            document.getElementById('privilege-modal').classList.add('hidden');
        }

        function editPrivilege(data) {
            document.getElementById('privilege-modal-title').textContent = 'Edit Privilege';
            document.getElementById('privilege-form').action = '{{ url('/privilege-master') }}' + '/' + (data.privilegeId || '');
            document.getElementById('privilege_form_method').value = 'PUT';
            document.getElementById('privilege_id').value = data.privilegeId || '';
            document.getElementById('privilege_code').value = data.privilegeCode || '';
            document.getElementById('privilege_name').value = data.privilegeName || '';
            document.getElementById('module_name').value = data.moduleName || '';
            document.getElementById('description').value = data.description || '';
            document.getElementById('privilege-modal-submit').textContent = 'Update';
            document.getElementById('privilege-modal').classList.remove('hidden');
        }
    </script>
</x-app-layout>
