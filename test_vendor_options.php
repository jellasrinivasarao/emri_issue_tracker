<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Check vendorOptions
$vendorQuery = DB::table('mst_vendor as v')->select('v.vendor_id', 'v.vendor_name');
$vendorOptions = $vendorQuery->orderBy('v.vendor_name')->get();

echo "Total vendorOptions: " . $vendorOptions->count() . "\n";

$requiredVendorIds = [2, 3, 5, 6];
echo "\nChecking required vendor IDs [2, 3, 5, 6]:\n";
foreach ($requiredVendorIds as $vendorId) {
    $found = $vendorOptions->firstWhere('vendor_id', $vendorId);
    if ($found) {
        echo "  vendor_id=" . $vendorId . ", vendor_name=" . $found->vendor_name . " - FOUND\n";
    } else {
        echo "  vendor_id=" . $vendorId . " - NOT FOUND\n";
    }
}

// Now check vendorStateMappings
$vendorStateMappings = DB::table('map_vendor_state as m')
    ->select('m.state_id', 'm.project_id', 'm.vendor_id')
    ->where('m.is_active', 1)
    ->distinct()
    ->get();

echo "\n\nvendorStateMappings for state_id=3, project_id=1:\n";
$filtered = $vendorStateMappings->where('state_id', 3)->where('project_id', 1);
foreach ($filtered as $row) {
    $vendor = $vendorOptions->firstWhere('vendor_id', $row->vendor_id);
    echo "  vendor_id=" . $row->vendor_id . " (" . ($vendor ? $vendor->vendor_name : "NOT FOUND") . ")\n";
}
