<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Illuminate\Http\Response;

class ItSupportMasterController extends Controller
{
    private const MASTER_CONFIG = [
        'category' => [
            'table' => 'mst_it_support_category',
            'key' => 'category_id',
            'name' => 'category_name',
            'code' => 'category_code',
            'description' => 'description',
            'status' => 'is_active',
            'label' => 'IT Support Category',
            'title' => 'IT Support Category Master',
            'descriptionText' => 'Manage support categories used in the internal issue workflow.',
            'route' => 'it.support.category.master',
        ],
        'device' => [
            'table' => 'mst_it_support_device',
            'key' => 'device_type_id',
            'name' => 'device_name',
            'code' => 'device_code',
            'description' => null,
            'status' => 'is_active',
            'label' => 'IT Support Device',
            'title' => 'IT Support Device Master',
            'descriptionText' => 'Manage device types and related support references.',
            'route' => 'it.support.device.master',
        ],
        'issue_type' => [
            'table' => 'mst_it_support_issue_type',
            'key' => 'issue_type_id',
            'name' => 'issue_type_name',
            'code' => 'issue_type_code',
            'description' => 'description',
            'status' => 'is_active',
            'label' => 'IT Support Issue Type',
            'title' => 'IT Support Issue Type Master',
            'descriptionText' => 'Manage issue types used in support and escalation workflows.',
            'route' => 'it.support.issue.type.master',
            'category' => true,
        ],
        'impact' => [
            'table' => 'mst_it_support_impact',
            'key' => 'impact_id',
            'name' => 'impact_name',
            'code' => 'impact_code',
            'description' => 'description',
            'status' => 'is_active',
            'label' => 'IT Support Impact',
            'title' => 'IT Support Impact Master',
            'descriptionText' => 'Manage impact levels used for issue severity and priority.',
            'route' => 'it.support.impact.master',
        ],
    ];

    public function index(Request $request, ?string $type = null): View|Response
    {
        $type = $type ?? $request->route()?->defaults['type'] ?? 'category';
        $config = $this->getConfig($type);

        $columns = array_filter([
            $config['key'],
            $config['name'],
            $config['code'],
            $config['description'],
            $config['status'],
        ]);

        $records = DB::table($config['table'])
            ->select($columns)
            ->orderBy($config['name'])
            ->get();

        $categories = collect();
        if (! empty($config['category'])) {
            $categories = DB::table('mst_it_support_category')
                ->select('category_id', 'category_code', 'category_name')
                ->where('is_active', 1)
                ->orderBy('category_name')
                ->get();

            $records = DB::table($config['table'] . ' as support')
                ->leftJoin('mst_it_support_category as category', 'support.category_id', '=', 'category.category_id')
                ->select('support.*', 'category.category_name as support_category_name')
                ->orderBy('category.category_name')
                ->orderBy('support.' . $config['name'])
                ->get();
        }

        $format = $request->query('format');
        if ($format) {
            $fileName = strtolower(str_replace(' ', '-', $config['title'])) . '-' . now()->format('YmdHis') . '.' . $format;
            $rows = $records->map(function ($record) use ($config) {
                return [
                    $config['label'] . ' Name' => $record->{$config['name']} ?? '-',
                    $config['label'] . ' Code' => $record->{$config['code']} ?? '-',
                    'Description' => $config['description'] ? ($record->{$config['description']} ?? '-') : '-',
                    'Status' => (int) ($record->{$config['status']} ?? 0) === 1 ? 'Active' : 'Inactive',
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
                $html .= '<thead><tr><th>' . e($config['label']) . ' Name</th><th>' . e($config['label']) . ' Code</th><th>Description</th><th>Status</th></tr></thead><tbody>';
                foreach ($rows as $row) {
                    $html .= '<tr>' . implode('', array_map(fn ($value) => '<td>' . e($value) . '</td>', $row)) . '</tr>';
                }
                $html .= '</tbody></table>';

                return response($html, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                ]);
            }

            return redirect()->route($config['route'])->with('error', 'Unsupported export format.');
        }

        $user = $request->user();
        $routeName = $config['route'];

        $permissions = [
            'view' => $user?->hasPrivilegeOnRoute($routeName, 'view') ?? true,
            'create' => $user?->hasPrivilegeOnRoute($routeName, 'create') ?? true,
            'edit' => $user?->hasPrivilegeOnRoute($routeName, 'edit') ?? true,
            'delete' => $user?->hasPrivilegeOnRoute($routeName, 'delete') ?? true,
            'export' => $user?->hasPrivilegeOnRoute($routeName, 'export') ?? true,
            'activate' => $user?->hasPrivilegeOnRoute($routeName, 'activate') ?? true,
            'deactivate' => $user?->hasPrivilegeOnRoute($routeName, 'deactivate') ?? true,
        ];

        return view('pages.it-support-master', [
            'title' => $config['title'],
            'description' => $config['descriptionText'],
            'masterType' => $type,
            'entityLabel' => $config['label'],
            'records' => $records,
            'categories' => $categories,
            'permissions' => $permissions,
        ]);
    }

    public function store(Request $request, ?string $type = null): RedirectResponse
    {
        $type = $type ?? $request->route()?->defaults['type'] ?? 'category';
        $config = $this->getConfig($type);
        $request->validate([
            $config['name'] => ['required', 'string', 'max:255'],
            $config['code'] => ['nullable', 'string', 'max:100'],
            ...($config['description'] ? [$config['description'] => ['nullable', 'string']] : []),
            ...(! empty($config['category']) ? ['category_id' => ['required', 'integer', 'exists:mst_it_support_category,category_id']] : []),
        ]);

        $payload = [
            $config['name'] => $request->input($config['name']),
            $config['status'] => 1,
        ];

        if ($config['description']) {
            $payload[$config['description']] = $request->input($config['description']);
        }

        if ($request->filled($config['code'])) {
            $payload[$config['code']] = $request->input($config['code']);
        }

        if (! empty($config['category'])) {
            $payload['category_id'] = $request->integer('category_id');
        }

        if (Schema::hasColumn($config['table'], 'created_at')) {
            $payload['created_at'] = now();
        }

        if (Schema::hasColumn($config['table'], 'created_by')) {
            $payload['created_by'] = auth()->id();
        }

        DB::table($config['table'])->insert($payload);

        return redirect()->route($config['route'])->with('success', $config['label'] . ' created successfully.');
    }

    public function update(Request $request, ?string $type = null, int $id = 0): RedirectResponse
    {
        $type = $type ?? $request->route()?->defaults['type'] ?? 'category';
        $config = $this->getConfig($type);
        $request->validate([
            $config['name'] => ['required', 'string', 'max:255'],
            $config['code'] => ['nullable', 'string', 'max:100'],
            ...($config['description'] ? [$config['description'] => ['nullable', 'string']] : []),
            ...(! empty($config['category']) ? ['category_id' => ['required', 'integer', 'exists:mst_it_support_category,category_id']] : []),
        ]);

        $payload = [
            $config['name'] => $request->input($config['name']),
        ];

        if ($config['description']) {
            $payload[$config['description']] = $request->input($config['description']);
        }

        if ($request->filled($config['code'])) {
            $payload[$config['code']] = $request->input($config['code']);
        }

        if (! empty($config['category'])) {
            $payload['category_id'] = $request->integer('category_id');
        }

        if (Schema::hasColumn($config['table'], 'updated_at')) {
            $payload['updated_at'] = now();
        }

        if (Schema::hasColumn($config['table'], 'updated_by')) {
            $payload['updated_by'] = auth()->id();
        }

        $updated = DB::table($config['table'])
            ->where($config['key'], $id)
            ->update($payload);

        if (! $updated) {
            return redirect()->route($config['route'])->with('error', $config['label'] . ' not found or no changes were made.');
        }

        return redirect()->route($config['route'])->with('success', $config['label'] . ' updated successfully.');
    }

    public function toggle(Request $request, ?string $type = null, int $id = 0): RedirectResponse
    {
        $type = $type ?? $request->route()?->defaults['type'] ?? 'category';
        $config = $this->getConfig($type);
        $row = DB::table($config['table'])->where($config['key'], $id)->first();

        if (! $row) {
            return redirect()->route($config['route'])->with('error', $config['label'] . ' not found.');
        }

        $newStatus = ((int) ($row->{$config['status']} ?? 0) === 1) ? 0 : 1;
        $payload = [$config['status'] => $newStatus];

        if (Schema::hasColumn($config['table'], 'updated_at')) {
            $payload['updated_at'] = now();
        }

        if (Schema::hasColumn($config['table'], 'updated_by')) {
            $payload['updated_by'] = auth()->id();
        }

        DB::table($config['table'])
            ->where($config['key'], $id)
            ->update($payload);

        return redirect()->route($config['route'])->with('success', $newStatus === 1 ? $config['label'] . ' reactivated successfully.' : $config['label'] . ' disabled successfully.');
    }

    private function getConfig(string $type): array
    {
        $type = strtolower(trim($type));

        if (! isset(self::MASTER_CONFIG[$type])) {
            abort(404, 'Invalid master type.');
        }

        return self::MASTER_CONFIG[$type];
    }
}
