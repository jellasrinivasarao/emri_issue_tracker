<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ApplicationMasterRequest;
use App\Models\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Illuminate\Http\Response;

class ApplicationMasterController extends Controller
{
    public function index(Request $request): View|Response
    {
        $applications = Application::query()
            ->select('application_id', 'application_name', 'application_code', 'description', 'is_active')
            ->orderBy('application_name')
            ->get();

        $format = $request->query('format');
        if ($format) {
            $fileName = 'application-master-' . now()->format('YmdHis') . '.' . $format;
            $rows = $applications->map(function (Application $application) {
                return [
                    'Application ID' => $application->application_id,
                    'Application Name' => $application->application_name,
                    'Description' => $application->description ?? '-',
                    'Status' => $application->is_active ? 'Active' : 'Inactive',
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
                $html .= '<thead><tr><th>Application Name</th><th>Owner</th><th>Version</th><th>Status</th></tr></thead><tbody>';
                foreach ($rows as $row) {
                    $html .= '<tr>' . implode('', array_map(fn ($value) => '<td>' . e($value) . '</td>', $row)) . '</tr>';
                }
                $html .= '</tbody></table>';

                return response($html, 200, [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                ]);
            }

            return redirect()->route('application.master')->with('error', 'Unsupported export format.');
        }

        return view('pages.application-master', [
            'title' => 'Application Master',
            'description' => 'Manage application records and application-level settings.',
            'applications' => $applications,
        ]);
    }

    public function store(ApplicationMasterRequest $request): RedirectResponse
    {
        $application = new Application();
        $application->application_name = $request->application_name;
        $application->application_code = $this->generateApplicationCode();
        $application->description = $request->application_description;
        $application->is_active = 1;

        if (Schema::hasColumn('mst_application', 'created_at')) {
            $application->created_at = now();
        }

        if (Schema::hasColumn('mst_application', 'created_by')) {
            $application->created_by = auth()->id();
        }

        $application->save();

        return redirect()->route('application.master')->with('success', 'Application created successfully.');
    }

    public function update(ApplicationMasterRequest $request, int $application_id): RedirectResponse
    {
        $application = Application::findOrFail($application_id);
        $application->application_name = $request->application_name;
        $application->description = $request->application_description;

        if (Schema::hasColumn('mst_application', 'update_at')) {
            $application->update_at = now();
        } elseif (Schema::hasColumn('mst_application', 'updated_at')) {
            $application->updated_at = now();
        }

        if (Schema::hasColumn('mst_application', 'updated_by')) {
            $application->updated_by = auth()->id();
        }

        $application->save();

        return redirect()->route('application.master')->with('success', 'Application updated successfully.');
    }

    public function toggle(Request $request, int $application_id): RedirectResponse
    {
        $application = Application::findOrFail($application_id);
        $application->is_active = ! $application->is_active;

        if (Schema::hasColumn('mst_application', 'update_at')) {
            $application->update_at = now();
        } elseif (Schema::hasColumn('mst_application', 'updated_at')) {
            $application->updated_at = now();
        }

        if (Schema::hasColumn('mst_application', 'updated_by')) {
            $application->updated_by = auth()->id();
        }

        $application->save();

        return redirect()->route('application.master')->with('success', $application->is_active ? 'Application reactivated successfully.' : 'Application disabled successfully.');
    }

    private function generateApplicationCode(): string
    {
        $nextId = (int) Application::query()->max('application_id') + 1;
        return 'APP' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
    }

}
