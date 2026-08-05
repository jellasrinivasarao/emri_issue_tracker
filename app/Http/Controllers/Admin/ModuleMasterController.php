<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ModuleMasterRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Illuminate\Http\Response;

class ModuleMasterController extends Controller
{
    public function index(Request $request): View|Response
    {
        $modules = DB::table('mst_module')
            ->select('module_id', 'module_name',  'description', 'is_active')
            ->orderBy('module_name')
            ->get();

        $format = $request->query('format');
        if ($format) {
            $fileName = 'module-master-' . now()->format('YmdHis') . '.' . $format;
            $rows = $modules->map(function ($module) {
                return [
                    'Module ID' => $module->module_id,
                    'Module Name' => $module->module_name,
                    'Description' => $module->description ?? '-',
                    'Status' => (int) $module->is_active === 1 ? 'Active' : 'Inactive',
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
                $html .= '<thead><tr><th>Module Name</th><th>Application</th><th>Owner</th><th>Status</th></tr></thead><tbody>';
                foreach ($rows as $row) {
                    $html .= '<tr>' . implode('', array_map(fn ($value) => '<td>' . e($value) . '</td>', $row)) . '</tr>';
                }
                $html .= '</tbody></table>';

                return response($html, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                ]);
            }

            return redirect()->route('module.master')->with('error', 'Unsupported export format.');
        }

        return view('pages.module-master', [
            'title' => 'Module Master',
            'description' => 'Manage application modules and module assignments.',
            'modules' => $modules,
        ]);
    }

    public function store(ModuleMasterRequest $request): RedirectResponse
    {
        $insert = [
            'module_name' => $request->module_name,
            'module_code' => $this->generateModuleCode(),
            'description' => $request->module_description,
            'is_active' => 1,
        ];

        if (Schema::hasColumn('mst_module', 'created_at')) {
            $insert['created_at'] = now();
        }

        if (Schema::hasColumn('mst_module', 'created_by')) {
            $insert['created_by'] = auth()->id();
        }

        DB::table('mst_module')->insert($insert);

        return redirect()->route('module.master')->with('success', 'Module created successfully.');
    }

    public function update(ModuleMasterRequest $request, int $module_id): RedirectResponse
    {
        $update = [
            'module_name' => $request->module_name,
            'description' => $request->module_description,
        ];

        if (Schema::hasColumn('mst_module', 'update_at')) {
            $update['update_at'] = now();
        }
        if (Schema::hasColumn('mst_module', 'updated_at')) {
            $update['updated_at'] = now();
        }

        if (Schema::hasColumn('mst_module', 'updated_by')) {
            $update['updated_by'] = auth()->id();
        }
        if (Schema::hasColumn('mst_module', 'update_by')) {
            $update['update_by'] = auth()->id();
        }

        $updated = DB::table('mst_module')
            ->where('module_id', $module_id)
            ->update($update);

        if (! $updated) {
            return redirect()->route('module.master')->with('error', 'Module not found or no changes made.');
        }

        return redirect()->route('module.master')->with('success', 'Module updated successfully.');
    }

    public function toggle(Request $request, int $module_id): RedirectResponse
    {
        $module = DB::table('mst_module')->where('module_id', $module_id)->first();
        if (! $module) {
            return redirect()->route('module.master')->with('error', 'Module not found.');
        }

        $newStatus = ((int) $module->is_active === 1) ? 0 : 1;
        $update = ['is_active' => $newStatus];


        if (Schema::hasColumn('mst_module', 'update_at')) {
            $update['update_at'] = now();
        }
        if (Schema::hasColumn('mst_module', 'updated_at')) {
            $update['updated_at'] = now();
        }

        if (Schema::hasColumn('mst_module', 'updated_by')) {
            $update['updated_by'] = auth()->id();
        }
        if (Schema::hasColumn('mst_module', 'update_by')) {
            $update['update_by'] = auth()->id();
        }

        DB::table('mst_module')
            ->where('module_id', $module_id)
            ->update($update);

        return redirect()->route('module.master')->with('success', $newStatus === 1 ? 'Module reactivated successfully.' : 'Module disabled successfully.');
    }

    private function generateModuleCode(): string
    {
        $nextId = (int) DB::table('mst_module')->max('module_id') + 1;
        return 'MOD' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
    }

}
