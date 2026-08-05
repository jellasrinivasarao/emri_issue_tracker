<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PrivilegeMasterRequest;
use App\Models\Privilege;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class PrivilegeMasterController extends Controller
{
    public function index(): View
    {
        $privileges = Privilege::query()
            ->select('privilege_id', 'privilege_code', 'privilege_name', 'module_name', 'description', 'is_active')
            ->orderBy('privilege_name')
            ->get();

        return view('pages.privilege-master', [
            'title' => 'Privilege Master',
            'description' => 'Manage privileges, privilege codes, and access permissions.',
            'privileges' => $privileges,
        ]);
    }

    public function store(PrivilegeMasterRequest $request): RedirectResponse
    {
        $privilege = new Privilege($request->validated());
        $privilege->is_active = 1;

        if (Schema::hasColumn('mst_privilege', 'created_at')) {
            $privilege->created_at = now();
        }
        if (Schema::hasColumn('mst_privilege', 'created_by')) {
            $privilege->created_by = auth()->id();
        }

        $privilege->save();

        return redirect()->route('privilege.master')->with('success', 'Privilege created successfully.');
    }

    public function update(PrivilegeMasterRequest $request, int $privilege_id): RedirectResponse
    {
        $privilege = Privilege::findOrFail($privilege_id);
        $privilege->fill($request->validated());

        if (Schema::hasColumn('mst_privilege', 'updated_at')) {
            $privilege->updated_at = now();
        }
        if (Schema::hasColumn('mst_privilege', 'updated_by')) {
            $privilege->updated_by = auth()->id();
        }

        $privilege->save();

        return redirect()->route('privilege.master')->with('success', 'Privilege updated successfully.');
    }

    public function toggle(int $privilege_id): RedirectResponse
    {
        $privilege = Privilege::findOrFail($privilege_id);
        $privilege->is_active = ! $privilege->is_active;

        if (Schema::hasColumn('mst_privilege', 'updated_at')) {
            $privilege->updated_at = now();
        }
        if (Schema::hasColumn('mst_privilege', 'updated_by')) {
            $privilege->updated_by = auth()->id();
        }

        $privilege->save();

        return redirect()->route('privilege.master')->with('success', $privilege->is_active ? 'Privilege activated successfully.' : 'Privilege deactivated successfully.');
    }
}
