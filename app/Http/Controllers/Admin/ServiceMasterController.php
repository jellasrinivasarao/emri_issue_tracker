<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ServiceMasterRequest;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ServiceMasterController extends Controller
{
    public function index(Request $request): View
    {
        $services = Service::query()
            ->select('service_id', 'short_code', 'service_name', 'description', 'is_active')
            ->orderBy('display_order')
            ->orderBy('service_name')
            ->get();

        $format = $request->query('format');
        if ($format) {
            $fileName = 'service-master-' . now()->format('YmdHis') . '.' . $format;
            $rows = $services->map(function (Service $service) {
                return [
                    'Service ID' => $service->service_id,
                    'Short Code' => $service->short_code ?? '-',
                    'Service Name' => $service->service_name,
                    'Description' => $service->description ?? '-',
                    'Status' => $service->is_active ? 'Active' : 'Inactive',
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
                $html .= '<thead><tr><th>Service ID</th><th>Short Code</th><th>Service Name</th><th>Description</th><th>Status</th></tr></thead><tbody>';
                foreach ($rows as $row) {
                    $html .= '<tr>' . implode('', array_map(fn ($value) => '<td>' . e($value) . '</td>', $row)) . '</tr>';
                }
                $html .= '</tbody></table>';

                return response($html, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                ]);
            }

            return redirect()->route('service.master')->with('error', 'Unsupported export format.');
        }

        return view('pages.service-master', [
            'title' => 'Service Master',
            'description' => 'Manage service master records and service catalog entries.',
            'services' => $services,
        ]);
    }

    public function store(ServiceMasterRequest $request): RedirectResponse
    {
        $nextServiceId = (int) Service::query()->max('service_id') + 1;
        $service = new Service();
        $service->service_code = 'SRV' . str_pad($nextServiceId, 3, '0', STR_PAD_LEFT);
        $service->short_code = $request->short_code;
        $service->service_name = $request->service_name;
        $service->description = $request->service_description;
        $service->is_active = 1;

        if (Schema::hasColumn('mst_service', 'created_at')) {
            $service->created_at = now();
        }

        if (Schema::hasColumn('mst_service', 'created_by')) {
            $service->created_by = auth()->id();
        }

        $service->save();

        return redirect()->route('service.master')->with('success', 'Service created successfully.');
    }

    public function update(ServiceMasterRequest $request, int $service_id): RedirectResponse
    {
        $service = Service::findOrFail($service_id);
        $service->short_code = $request->short_code;
        $service->service_name = $request->service_name;
        $service->description = $request->service_description;

        if (Schema::hasColumn('mst_service', 'update_at')) {
            $service->update_at = now();
        }

        if (Schema::hasColumn('mst_service', 'updated_by')) {
            $service->updated_by = auth()->id();
        }

        $service->save();

        return redirect()->route('service.master')->with('success', 'Service updated successfully.');
    }

    public function toggle(Request $request, int $service_id): RedirectResponse
    {
        $service = Service::findOrFail($service_id);
        $service->is_active = ! $service->is_active;

        if (Schema::hasColumn('mst_service', 'update_at')) {
            $service->update_at = now();
        }

        if (Schema::hasColumn('mst_service', 'updated_by')) {
            $service->updated_by = auth()->id();
        }

        $service->save();

        return redirect()->route('service.master')->with('success', $service->is_active ? 'Service reactivated successfully.' : 'Service disabled successfully.');
    }
}
