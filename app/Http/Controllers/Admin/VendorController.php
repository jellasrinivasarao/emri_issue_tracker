<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\VendorRequest;
use App\Models\Vendor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class VendorController extends Controller
{
    public function index(Request $request): View
    {
        $vendors = Vendor::query()
            ->orderBy('vendor_name')
            ->get();

        $format = $request->query('format');
        if ($format) {
            $fileName = 'vendor-master-' . now()->format('YmdHis') . '.' . $format;
            $rows = $vendors->map(function (Vendor $vendor) {
                return [
                    'Vendor Name' => $vendor->vendor_name,
                    'Vendor Code' => $vendor->vendor_code,
                    'Category' => $vendor->vendor_category,
                    'Primary Contact' => $vendor->vendor_contact_name ?? $vendor->contact_person,
                    'Primary Contact Mobile' => $vendor->primary_contact_mobile ?? '-',
                    'Primary Contact Email' => $vendor->primary_contact_email ?? '-',
                    'Support Mobile' => $vendor->support_mobile ?? '-',
                    'Status' => $vendor->is_active ? 'Active' : 'Inactive',
                ];
            })->toArray();

            if (in_array($format, ['csv', 'xlsx'], true)) {
                $output = '';
                foreach (array_keys($rows[0] ?? []) as $header) {
                    $output .= $header . ',';
                }
                $output = rtrim($output, ',') . "\n";

                foreach ($rows as $row) {
                    $output .= implode(',', array_map(fn ($value) => '"' . str_replace('"', '""', $value) . '"', $row)) . "\n";
                }

                return response($output, 200, [
                    'Content-Type' => 'text/csv',
                    'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                ]);
            }

            return redirect()->route('vendor.master')->with('error', 'Unsupported export format.');
        }

        return view('pages.vendor-master', [
            'title' => 'Vendor Master',
            'description' => 'Manage vendor master records and vendor information.',
            'vendors' => $vendors,
        ]);
    }

    public function store(VendorRequest $request): RedirectResponse
    {
        $vendor = new Vendor();
        $vendor->vendor_name = trim($request->vendor_name);
        $vendor->vendor_category = trim($request->vendor_category);
        $vendor->vendor_code = $this->generateVendorCode();
        $vendor->contact_person = trim($request->vendor_contact_person);
        $vendor->primary_contact_mobile = $request->primary_contact_mobile;
        $vendor->primary_contact_email = $request->primary_contact_email;
        $vendor->support_mobile = $request->support_mobile;
        $vendor->vendor_description = $request->vendor_description;
        $vendor->is_active = 1;
        $vendor->created_at = now();
        $vendor->created_by = auth()->id();
        $vendor->save();

        return redirect()->route('vendor.master')->with('success', 'Vendor created successfully.');
    }

    public function update(VendorRequest $request, Vendor $vendor): RedirectResponse
    {
        $vendor->vendor_name = trim($request->vendor_name);
        $vendor->vendor_category = trim($request->vendor_category);
        $vendor->contact_person = trim($request->vendor_contact_person);
        $vendor->primary_contact_mobile = $request->primary_contact_mobile;
        $vendor->primary_contact_email = $request->primary_contact_email;
        $vendor->support_mobile = $request->support_mobile;
        $vendor->vendor_description = $request->vendor_description;
        $vendor->Update_at = now();
        $vendor->Update_by = auth()->id();
        $vendor->save();

        return redirect()->route('vendor.master')->with('success', 'Vendor updated successfully.');
    }

    public function toggle(Request $request, Vendor $vendor): RedirectResponse
    {
        $vendor->is_active = $vendor->is_active ? 0 : 1;
        $vendor->Update_at = now();
        $vendor->Update_by = auth()->id();
        $vendor->save();

        return redirect()->route('vendor.master')->with('success', 'Vendor status updated successfully.');
    }

    protected function generateVendorCode(): string
    {
        $nextSequence = Vendor::query()
            ->where('vendor_code', 'like', 'VND%')
            ->get()
            ->map(fn ($vendor) => preg_match('/^VND(\d+)$/', $vendor->vendor_code, $matches) ? (int) $matches[1] : 0)
            ->max();

        return sprintf('VND%03d', $nextSequence ? $nextSequence + 1 : 1);
    }
}
