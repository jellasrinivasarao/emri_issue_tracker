<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StateMasterRequest;
use App\Models\State;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Illuminate\Http\Response;

class StateMasterController extends Controller
{
    public function index(Request $request): View|Response
    {
        $states = State::query()
            ->select('state_id', 'state_code', 'state_name', 'state_short_name', 'is_active')
            ->orderBy('state_name')
            ->get();

        $format = $request->query('format');
        if ($format) {
            $fileName = 'state-master-' . now()->format('YmdHis') . '.' . $format;
            $rows = $states->map(function (State $state) {
                return [
                    'State Name' => $state->state_name,
                    'State Code' => $state->state_code,
                    'Short Name' => $state->state_short_name ?? '-',
                    'Status' => $state->is_active ? 'Active' : 'Inactive',
                ];
            })->toArray();

            if (in_array($format, ['csv', 'xlsx'], true)) {
                $output = '';
                $output .= implode(',', array_map(fn ($value) => '"' . str_replace('"', '""', $value) . '"', array_keys($rows[0] ?? []))) . "\r\n";
                foreach ($rows as $row) {
                    $output .= implode(',', array_map(fn ($value) => '"' . str_replace('"', '""', $value) . '"', $row)) . "\r\n";
                }

                return response($output, 200, [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                ]);
            }

            if ($format === 'pdf') {
                $html = '<table border="1" cellpadding="4" cellspacing="0" style="border-collapse:collapse;width:100%;">';
                $html .= '<thead><tr><th>State Name</th><th>State Code</th><th>Short Name</th><th>Status</th></tr></thead><tbody>';
                foreach ($rows as $row) {
                    $html .= '<tr>' . implode('', array_map(fn ($value) => '<td>' . e($value) . '</td>', $row)) . '</tr>';
                }
                $html .= '</tbody></table>';

                return response($html, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                ]);
            }

            return redirect()->route('state.master')->with('error', 'Unsupported export format.');
        }

        $user = $request->user();
        $routeName = 'state.master';

        $permissions = [
            'view' => $user->hasPrivilegeOnRoute($routeName, 'view'),
            'create' => $user->hasPrivilegeOnRoute($routeName, 'create'),
            'edit' => $user->hasPrivilegeOnRoute($routeName, 'edit'),
            'delete' => $user->hasPrivilegeOnRoute($routeName, 'delete'),
            'export' => $user->hasPrivilegeOnRoute($routeName, 'export'),
            'activate' => $user->hasPrivilegeOnRoute($routeName, 'activate'),
            'deactivate' => $user->hasPrivilegeOnRoute($routeName, 'deactivate'),
        ];

        return view('pages.state-master', [
            'title' => 'State Master',
            'description' => 'Manage state master records and state-level organization details.',
            'states' => $states,
            'permissions' => $permissions,
        ]);
    }

    public function store(StateMasterRequest $request): RedirectResponse
    {
        $state = new State();
        $state->state_name = $request->state_name;
        $state->state_code = $request->state_code;
        $state->state_short_name = $request->state_short_name;
        $state->is_active = 1;

        if (Schema::hasColumn('mst_state', 'created_at')) {
            $state->created_at = now();
        }

        if (Schema::hasColumn('mst_state', 'created_by')) {
            $state->created_by = auth()->id();
        }

        $state->save();

        return redirect()->route('state.master')->with('success', 'State created successfully.');
    }

    public function update(StateMasterRequest $request, int $state_id): RedirectResponse
    {
        $state = State::findOrFail($state_id);
        $state->state_name = $request->state_name;
        $state->state_code = $request->state_code;
        $state->state_short_name = $request->state_short_name;

        if (Schema::hasColumn('mst_state', 'update_at')) {
            $state->update_at = now();
        }

        if (Schema::hasColumn('mst_state', 'updated_by')) {
            $state->updated_by = auth()->id();
        }

        $state->save();

        return redirect()->route('state.master')->with('success', 'State updated successfully.');
    }

    public function toggle(Request $request, int $state_id): RedirectResponse
    {
        $state = State::findOrFail($state_id);
        $state->is_active = ! $state->is_active;

        if (Schema::hasColumn('mst_state', 'update_at')) {
            $state->update_at = now();
        }

        if (Schema::hasColumn('mst_state', 'updated_by')) {
            $state->updated_by = auth()->id();
        }

        $state->save();

        return redirect()->route('state.master')->with('success', $state->is_active ? 'State reactivated successfully.' : 'State disabled successfully.');
    }
}
