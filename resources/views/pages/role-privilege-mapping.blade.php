<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ $title ?? 'Role–Privilege Mapping' }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                @if(session('success') || session('error'))
                    <div class="px-5 py-4" id="permission-message-container">
                        <div id="permission-message" class="relative rounded-2xl px-4 py-3 text-sm font-semibold shadow-sm {{ session('success') ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                            <span>{{ session('success') ?? session('error') }}</span>
                            <button type="button" onclick="document.getElementById('permission-message-container').remove()" class="absolute right-3 top-3 rounded-full bg-white/80 px-2 py-1 text-xs font-semibold text-slate-700 hover:bg-white focus:outline-none">Close</button>
                        </div>
                    </div>
                @endif
                <div class="px-5 py-4">
                    <div class="grid gap-3 lg:grid-cols-[1fr_auto] lg:items-center">
                        <div>
                            <h1 class="text-lg font-semibold text-slate-900">Role Menu & Action Mapping</h1>
                            <p class="mt-1 text-sm text-slate-500">Map menus and actions/privileges for the selected role.</p>
                        </div>
                        <form method="GET" action="{{ route('role.privilege.mapping') }}" class="grid gap-3 sm:grid-cols-[320px_auto] items-end">
                            <div>
                                <label for="role_id" class="text-sm font-medium text-slate-700">Role</label>
                                <select id="role_id" name="role_id" class="mt-2 block w-full min-w-[320px] rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm focus:border-sky-500 focus:ring-2 focus:ring-sky-100">
                                    <option value="">-- Select role --</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->role_id }}" @selected((string)$selectedRoleId === (string)$role->role_id)>{{ $role->role_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="inline-flex h-[42px] items-center justify-center rounded-xl bg-sky-600 px-4 text-sm font-semibold text-white shadow-sm hover:bg-sky-700">Load Permissions</button>
                        </form>
                    </div>
                </div>
            </div>

            <form id="permForm" method="POST" action="{{ route('role.privilege.mapping.store') }}">
                @csrf
                <input type="hidden" name="role_id" value="{{ $selectedRoleId }}">

                <div id="permission-matrix" class="mt-4 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                    <div class="flex flex-col gap-3 border-b border-slate-200 bg-slate-50 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center gap-2 rounded-full bg-sky-600 px-3 py-2 text-sm font-semibold text-white shadow-sm"><i class="fas fa-shield-alt"></i> ROLE : {{ optional($roles->firstWhere('role_id', $selectedRoleId))->role_name ?? '—' }}</span>
                            <span class="rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.3em] text-slate-500">Menus: {{ $totalMenus }}</span>
                            <span class="rounded-full border border-slate-200 bg-white px-3 py-1 text-[11px] font-semibold uppercase tracking-[0.3em] text-slate-500">Actions: {{ $totalPrivileges }}</span>
                        </div>
                        <div class="text-sm text-slate-500">Choose which menu actions are allowed for this role.</div>
                    </div>

                    <div class="overflow-x-auto w-full">
                        <div class="table-scroll w-full">
                            <table class="min-w-full table-fixed border-separate border-spacing-0" style="min-width:1080px; border-collapse: separate; border-spacing: 0;">
                                <thead class="bg-slate-200 text-slate-700">
                                    <tr class="border-b border-slate-300">
                                        <th class="sticky-col left-0 z-60 bg-slate-200 px-4 py-3 text-left text-xs font-semibold uppercase tracking-[0.24em] text-slate-900" style="min-width:320px;">Menu</th>
                                        @foreach($privileges as $priv)
                                            @php
                                                $code = strtolower($priv->privilege_code ?? $priv->privilege_name);
                                                if (str_contains($code, 'view')) { $icon = 'fas fa-eye text-emerald-600'; }
                                                elseif (str_contains($code, 'create') || str_contains($code,'add')) { $icon = 'fas fa-plus-circle text-sky-600'; }
                                                elseif (str_contains($code, 'edit') || str_contains($code,'modify')) { $icon = 'fas fa-pen text-orange-500'; }
                                                elseif (str_contains($code, 'delete') || str_contains($code,'remove')) { $icon = 'fas fa-trash text-rose-500'; }
                                                elseif (str_contains($code, 'assign')) { $icon = 'fas fa-user-plus text-cyan-600'; }
                                                elseif (str_contains($code, 'resolve') || str_contains($code,'confirm')) { $icon = 'fas fa-check-circle text-lime-600'; }
                                                elseif (str_contains($code, 'close')) { $icon = 'fas fa-lock text-slate-500'; }
                                                elseif (str_contains($code, 'export') || str_contains($code,'download')) { $icon = 'fas fa-file-export text-sky-600'; }
                                                else { $icon = 'far fa-circle text-slate-400'; }
                                            @endphp
                                            <th class="px-2 py-3 text-center text-[10px] font-semibold uppercase tracking-[0.24em] text-slate-600" style="width:96px; min-width:96px;"> 
                                                <div class="flex flex-col items-center gap-1">
                                                    <i class="{{ $icon }}" style="font-size:14px;"></i>
                                                    <span>{{ $priv->privilege_name }}</span>
                                                </div>
                                            </th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody class="bg-white text-sm text-slate-700">
                                    @php
                                        function _renderRow($menu, $privileges, $existing, $level = 0) {
                                            $isParent = ! empty($menu['children']);
                                            $indent = $level * 22;
                                            $rowClass = $isParent ? 'parent-row' : '';
                                            $row = '<tr class="'.$rowClass.'">';
                                            $row .= '<td class="sticky-col menu-cell px-4 py-2" style="left:0; vertical-align:middle; min-width:280px;">';
                                            $row .= '<div style="display:flex;align-items:center;gap:0.75rem;padding-left:'. ($indent + 6) .'px;">';
                                            $row .= $isParent ? '<i class="fas fa-folder text-sky-600"></i>' : '<i class="far fa-file-alt text-slate-400"></i>';
                                            $row .= '<span class="'.($isParent ? 'font-semibold text-slate-900' : 'text-slate-700').'">'.e($menu['menu_name']).'</span>';
                                            $row .= '</div></td>';

                                            foreach ($privileges as $priv) {
                                                $checked = isset($existing[$menu['menu_id']][$priv->privilege_id]) && $existing[$menu['menu_id']][$priv->privilege_id];
                                                $name = "permissions[{$menu['menu_id']}][{$priv->privilege_id}]";
                                                $row .= '<td class="text-center align-middle px-2 py-1.5 action-cell">';
                                                $row .= '<div class="form-check mb-0 flex h-9 items-center justify-center">';
                                                $row .= '<input class="form-check-input" type="checkbox" name="'. $name .'" value="1" '. ($checked ? 'checked' : '') .' />';
                                                $row .= '</div></td>';
                                            }

                                            $row .= '</tr>';

                                            if (! empty($menu['children'])) {
                                                foreach ($menu['children'] as $child) {
                                                    $row .= _renderRow($child, $privileges, $existing, $level + 1);
                                                }
                                            }

                                            return $row;
                                        }
                                    @endphp

                                    @foreach($menuTree as $menu)
                                        @if(! empty($menu['children']))
                                            <tr class="section-heading">
                                                <td colspan="{{ 1 + $privileges->count() }}" class="bg-slate-50 px-4 py-2">
                                                    <div class="flex items-center gap-2 text-sm font-semibold text-slate-800">
                                                        <i class="fas fa-folder text-sky-600"></i>
                                                        <span>{{ e($menu['menu_name']) }}</span>
                                                    </div>
                                                </td>
                                            </tr>
                                            @foreach($menu['children'] as $child)
                                                {!! _renderRow($child, $privileges, $existing, 1) !!}
                                            @endforeach
                                        @else
                                            {!! _renderRow($menu, $privileges, $existing, 0) !!}
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="flex flex-col gap-3 border-t border-slate-200 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-3 text-sm text-slate-500">
                            <span class="inline-flex h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                            Allowed
                            <span class="inline-flex h-2.5 w-2.5 rounded-full bg-slate-300"></span>
                            Not Allowed
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            <button type="button" id="selectAll" class="inline-flex h-10 items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"><i class="fas fa-check"></i> Select All</button>
                            <button type="button" id="clearAll" class="inline-flex h-10 items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"><i class="fas fa-eraser"></i> Clear</button>
                            <button type="submit" class="inline-flex h-10 items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-700"><i class="fas fa-save"></i> Save Permissions</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('styles')
    <style>
        /* Card and table look */
        .card { border-radius: 0.8rem; }
        .table-responsive { background: #fff; border-radius: .6rem; }

        /* Sticky first column */
        .sticky-col {
            position: sticky;
            left: 0;
            z-index: 50;
            background: #eaf3ff;
            border-right: 1px solid #d1d5db;
        }
        thead .sticky-col {
            z-index: 60;
            background: #dceafe;
        }

        th.sticky-col,
        td.sticky-col {
            position: sticky;
            left: 0;
            background-clip: padding-box;
        }

        /* Section header row */
        .section-heading td { background: #dcebff !important; }

        /* Parent menu highlight (if rows appear as parent menu items) */
        .parent-row td { background: #f8fbff !important; }

        /* Alternate rows */
        tbody tr:not(.section-heading):nth-child(odd) td { background: #ffffff; }
        tbody tr:not(.section-heading):nth-child(even) td { background: #fbfdff; }

        /* Hover */
        tbody tr:hover td { background: #f1f7fb !important; }

        /* Custom checkbox look (blue filled square with white check) */
        #permForm input[type="checkbox"] {
            -webkit-appearance: none; appearance: none;
            width: 18px; height: 18px; margin: 0; display:inline-block; vertical-align:middle;
            border: 2px solid #d9e4ef; border-radius:4px; background: #fff; position: relative; cursor: pointer;
        }
        #permForm input[type="checkbox"]:focus { outline: none; box-shadow: 0 0 0 3px rgba(13,110,253,0.06); }
        #permForm input[type="checkbox"]:checked { background: #0b69e6; border-color: #0b69e6; }
        #permForm input[type="checkbox"]:checked::after { content: '\2713'; color: #fff; font-size: 12px; position: absolute; left: 50%; top: 50%; transform: translate(-50%,-55%); }
        /* center checkbox cell */
        td .form-check { display:flex; align-items:center; justify-content:center; height:48px; }

        /* header styles */
        thead th { font-weight: 700; padding: 10px 8px; color: #0f172a; background: #dceafe; }
        thead th:first-child { text-align: left; }
        tbody td { padding: 8px 6px; }
        .menu-cell { min-width: 320px; }
        .menu-label { display: flex; align-items: center; gap: 0.75rem; }
        .action-col { width: 96px; min-width: 96px; max-width: 96px; }
        .action-cell { padding: 8px 4px; }
        .table-fixed td, .table-fixed th { border-color: #d1d5db; }
        #permForm input[type="checkbox"] { width: 16px; height: 16px; }
        td .form-check { height: 36px; }
*** End Patch
        /* Sticky header */
        thead th { position: sticky; top: 0; z-index: 20; background: #dceafe; }

        .table thead th { border-bottom: 1px solid rgba(13,110,253,0.06); }
        .table tbody td { vertical-align: middle; }

        .table-scroll {
            max-height: 660px;
            overflow: auto;
            position: relative;
        }
        .table-scroll table {
            border-collapse: separate;
        }
        .table-scroll thead th {
            position: sticky;
            top: 0;
            z-index: 50;
            background: #dceafe;
        }
        .table-scroll thead th.sticky-col {
            left: 0;
            z-index: 70;
            background: #dceafe;
        }
        th.sticky-col,
        td.sticky-col {
            position: sticky;
            left: 0;
            background-clip: padding-box;
        }

        /* Thin borders and softer look */
        .table-hover tbody tr:hover { box-shadow: none; }
    </style>
    @endpush

    @push('scripts')
    <script>
        // Select All / Clear handlers
        document.getElementById('selectAll')?.addEventListener('click', function(){
            document.querySelectorAll('#permForm input[type="checkbox"]').forEach(cb => cb.checked = true);
        });
        document.getElementById('clearAll')?.addEventListener('click', function(){
            document.querySelectorAll('#permForm input[type="checkbox"]').forEach(cb => cb.checked = false);
        });

        // Optional: per-row select all when header click (not required)
        document.querySelectorAll('.table thead th').forEach((th, idx) => {
            // skip first column
            if (idx === 0) return;
            // add pointer for header
            th.style.cursor = 'default';
        });

        // Scroll viewport to permission matrix after loading permissions
        window.addEventListener('load', function() {
            var roleId = '{{ $selectedRoleId }}';
            if (roleId) {
                var matrix = document.getElementById('permission-matrix');
                if (matrix) {
                    matrix.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }

            var roleSelect = document.getElementById('role_id');
            var roleHidden = document.querySelector('#permForm input[name="role_id"]');
            if (roleSelect && roleHidden) {
                roleHidden.value = roleSelect.value;
                roleSelect.addEventListener('change', function () {
                    roleHidden.value = this.value;
                });
            }
        });
    </script>
    @endpush

</x-app-layout>
