<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SupportGroupMasterRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Illuminate\Http\Response;

class SupportGroupMasterController extends Controller
{
    public function index(Request $request): View|Response
    {
        $supportGroups = DB::table('mst_support_group')
            ->select('support_group_id', 'support_group_name', 'description', 'is_active')
            ->orderBy('support_group_name')
            ->get();

        $format = $request->query('format');
        if ($format) {
            $fileName = 'support-group-master-' . now()->format('YmdHis') . '.' . $format;
            $rows = $supportGroups->map(function ($group) {
                return [
                    'Support Group ID' => $group->support_group_id,
                    'Support Group' => $group->support_group_name,
                    'Description' => $group->description ?? '-',
                    'Status' => (int) $group->is_active === 1 ? 'Active' : 'Inactive',
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
                $html .= '<thead><tr><th>Support Group</th><th>Lead</th><th>Escalation Level</th><th>Status</th></tr></thead><tbody>';
                foreach ($rows as $row) {
                    $html .= '<tr>' . implode('', array_map(fn ($value) => '<td>' . e($value) . '</td>', $row)) . '</tr>';
                }
                $html .= '</tbody></table>';

                return response($html, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                ]);
            }

            return redirect()->route('support-group.master')->with('error', 'Unsupported export format.');
        }

        return view('pages.support-group-master', [
            'title' => 'Support Group Master',
            'description' => 'Manage support groups and group ownership details.',
            'supportGroups' => $supportGroups,
        ]);
    }

    public function store(SupportGroupMasterRequest $request): RedirectResponse
    {
        $insert = [
            'support_group_name' => $request->support_group_name,
            'support_group_code' => $this->generateSupportGroupCode(),
            'description' => $request->support_group_description,
            'is_active' => 1,
        ];

        if (Schema::hasColumn('mst_support_group', 'created_at')) {
            $insert['created_at'] = now();
        }

        if (Schema::hasColumn('mst_support_group', 'created_by')) {
            $insert['created_by'] = auth()->id();
        }

        DB::table('mst_support_group')->insert($insert);

        return redirect()->route('support-group.master')->with('success', 'Support group created successfully.');
    }

    public function update(SupportGroupMasterRequest $request, int $support_group_id): RedirectResponse
    {
        $update = [
            'support_group_name' => $request->support_group_name,
            'description' => $request->support_group_description,
        ];

        if (Schema::hasColumn('mst_support_group', 'update_at')) {
            $update['update_at'] = now();
        }
        if (Schema::hasColumn('mst_support_group', 'updated_at')) {
            $update['updated_at'] = now();
        }

        if (Schema::hasColumn('mst_support_group', 'updated_by')) {
            $update['updated_by'] = auth()->id();
        }
        if (Schema::hasColumn('mst_support_group', 'update_by')) {
            $update['update_by'] = auth()->id();
        }

        $updated = DB::table('mst_support_group')
            ->where('support_group_id', $support_group_id)
            ->update($update);

        if (! $updated) {
            return redirect()->route('support-group.master')->with('error', 'Support group not found or no changes made.');
        }

        return redirect()->route('support-group.master')->with('success', 'Support group updated successfully.');
    }

    public function toggle(Request $request, int $support_group_id): RedirectResponse
    {
        $group = DB::table('mst_support_group')->where('support_group_id', $support_group_id)->first();
        if (! $group) {
            return redirect()->route('support-group.master')->with('error', 'Support group not found.');
        }

        $newStatus = ((int) $group->is_active === 1) ? 0 : 1;
        $update = ['is_active' => $newStatus];

        if (Schema::hasColumn('mst_support_group', 'update_at')) {
            $update['update_at'] = now();
        }
        if (Schema::hasColumn('mst_support_group', 'updated_at')) {
            $update['updated_at'] = now();
        }

        if (Schema::hasColumn('mst_support_group', 'updated_by')) {
            $update['updated_by'] = auth()->id();
        }
        if (Schema::hasColumn('mst_support_group', 'update_by')) {
            $update['update_by'] = auth()->id();
        }
        if (Schema::hasColumn('mst_support_group', 'update_by')) {
            $update['update_by'] = auth()->id();
        }

        DB::table('mst_support_group')
            ->where('support_group_id', $support_group_id)
            ->update($update);

        return redirect()->route('support-group.master')->with('success', $newStatus === 1 ? 'Support group reactivated successfully.' : 'Support group disabled successfully.');
    }

    private function generateSupportGroupCode(): string
    {
        $last = DB::table('mst_support_group')->orderByDesc('support_group_id')->limit(1)->first();
        $next = $last ? ((int) $last->support_group_id + 1) : 1;
        return 'SGP' . str_pad((string) $next, 3, '0', STR_PAD_LEFT);
    }
}
