<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ $title ?? 'Role–Privilege Mapping' }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                <div class="border-b border-slate-200 bg-slate-50 px-5 py-5">
                    <p class="text-sm text-slate-600">{{ $description ?? 'Map roles to privileges and control role-based actions.' }}</p>
                    <div class="mt-4 flex justify-end">
                        <button type="button" onclick="openRolePrivilegeModal()" class="inline-flex items-center justify-center rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-emerald-700">Add Mapping</button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-100">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-900">Role</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-900">Privilege Code</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-900">Privilege Name</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-900">Module</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-900">Allowed</th>
                                <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-[0.2em] text-slate-900">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            @forelse($mappings as $mapping)
                                <tr>
                                    <td class="px-5 py-3 text-sm font-semibold text-slate-900">{{ $mapping->role_name }}</td>
                                    <td class="px-5 py-3 text-sm text-slate-600">{{ $mapping->privilege_code }}</td>
                                    <td class="px-5 py-3 text-sm text-slate-600">{{ $mapping->privilege_name }}</td>
                                    <td class="px-5 py-3 text-sm text-slate-600">{{ $mapping->module_name }}</td>
                                    <td class="px-5 py-3 text-sm">{{ $mapping->is_allowed ? 'Yes' : 'No' }}</td>
                                    <td class="px-5 py-3 text-sm">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <button type="button"
                                                data-role-privilege-id="{{ $mapping->role_privilege_id }}"
                                                data-role-id="{{ $mapping->role_id }}"
                                                data-privilege-id="{{ $mapping->privilege_id }}"
                                                data-role-name="{{ $mapping->role_name }}"
                                                data-privilege-code="{{ $mapping->privilege_code }}"
                                                data-privilege-name="{{ $mapping->privilege_name }}"
                                                data-module-name="{{ $mapping->module_name }}"
                                                data-is-allowed="{{ $mapping->is_allowed }}"
                                                onclick="editRolePrivilege(this.dataset)"
                                                class="rounded-lg bg-slate-900 px-2.5 py-1.5 text-xs font-semibold text-white hover:bg-slate-800">Edit</button>
                                            <form method="POST" action="{{ route('role.privilege.mapping.toggle', ['role_privilege_id' => $mapping->role_privilege_id]) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="rounded-lg px-2.5 py-1.5 text-xs font-semibold {{ $mapping->is_allowed ? 'bg-rose-100 text-rose-700 hover:bg-rose-200' : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200' }}">
                                                    {{ $mapping->is_allowed ? 'Disallow' : 'Allow' }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-5 py-6 text-center text-sm text-slate-500">No role-privilege mappings found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="role-privilege-modal" class="fixed inset-0 z-50 hidden bg-slate-900/60 px-4 py-8">
        <div class="mx-auto max-w-2xl rounded-3xl bg-white shadow-2xl">
            <form id="role-privilege-form" method="POST" action="">
                @csrf
                <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
                    <div>
                        <h3 id="role-privilege-modal-title" class="text-lg font-semibold text-slate-900">Add Mapping</h3>
                        <p class="text-sm text-slate-600">Create or update a role privilege mapping.</p>
                    </div>
                    <button type="button" onclick="closeRolePrivilegeModal()" class="rounded-full p-2 bg-slate-100 text-slate-700 hover:bg-slate-200 hover:text-slate-900">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="space-y-4 px-5 py-5">
                    <input type="hidden" id="role_privilege_id" name="role_privilege_id" value="" />
                    <input type="hidden" id="role_privilege_form_method" name="_method" value="POST" />

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Role</label>
                            <select id="role_id" name="role_id" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400">
                                @foreach($roles as $role)
                                    <option value="{{ $role->role_id }}">{{ $role->role_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Privilege</label>
                            <select id="privilege_id" name="privilege_id" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400">
                                @foreach($privileges as $priv)
                                    <option value="{{ $priv->privilege_id }}">{{ $priv->privilege_code }} - {{ $priv->privilege_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid gap-4 md:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700">Allowed</label>
                            <select id="is_allowed" name="is_allowed" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm outline-none focus:border-slate-400">
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                        </div>
                        <div class="text-sm text-slate-500"><!-- placeholder for future fields --></div>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-slate-200 pt-4">
                        <button type="button" onclick="closeRolePrivilegeModal()" class="rounded-xl border border-slate-300 bg-slate-100 px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-200">Cancel</button>
                        <button type="submit" class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700" id="role-privilege-modal-submit">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openRolePrivilegeModal() {
            document.getElementById('role-privilege-modal-title').textContent = 'Add Mapping';
            document.getElementById('role-privilege-form').action = '{{ route('role.privilege.mapping.store') }}';
            document.getElementById('role_privilege_form_method').value = 'POST';
            document.getElementById('role_privilege_id').value = '';
            document.getElementById('role_id').value = '{{ $roles->first()->role_id ?? '' }}';
            document.getElementById('privilege_id').value = '{{ $privileges->first()->privilege_id ?? '' }}';
            document.getElementById('is_allowed').value = '1';
            document.getElementById('role-privilege-modal-submit').textContent = 'Save';
            document.getElementById('role-privilege-modal').classList.remove('hidden');
        }

        function closeRolePrivilegeModal() {
            document.getElementById('role-privilege-modal').classList.add('hidden');
        }

        function editRolePrivilege(data) {
            document.getElementById('role-privilege-modal-title').textContent = 'Edit Mapping';
            document.getElementById('role-privilege-form').action = '{{ url('/role-privilege-mapping') }}' + '/' + (data.rolePrivilegeId || '');
            document.getElementById('role_privilege_form_method').value = 'PUT';
            document.getElementById('role_privilege_id').value = data.rolePrivilegeId || '';
            document.getElementById('role_id').value = data.roleId || '';
            document.getElementById('privilege_id').value = data.privilegeId || '';
            document.getElementById('is_allowed').value = data.isAllowed || '1';
            document.getElementById('role-privilege-modal-submit').textContent = 'Update';
            document.getElementById('role-privilege-modal').classList.remove('hidden');
        }
    </script>
</x-app-layout>
