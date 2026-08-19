<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Simulate what the controller does
$vendorStateMappings = DB::table('map_vendor_state as m')
    ->select('m.state_id', 'm.project_id', 'm.vendor_id')
    ->where('m.is_active', 1)
    ->distinct()
    ->get();

echo "RAW vendorStateMappings COUNT: " . $vendorStateMappings->count() . "\n";
echo "RAW vendorStateMappings TYPE: " . get_class($vendorStateMappings) . "\n\n";

// Simulate what the Blade template does
$result = collect($vendorStateMappings ?? [])->map(function ($mapping) {
    return ['state_id' => $mapping->state_id ?? $mapping['state_id'], 'project_id' => $mapping->project_id ?? $mapping['project_id'], 'vendor_id' => $mapping->vendor_id ?? $mapping['vendor_id']];
})->all();

echo "MAPPED COUNT: " . count($result) . "\n";
echo "MAPPED data:\n";
print_r(array_slice($result, 0, 5));

// Simulate @json
$json = json_encode($result);
echo "\nJSON OUTPUT (first 200 chars):\n";
echo substr($json, 0, 200) . "...\n";
echo "\nJSON length: " . strlen($json) . " chars\n";

// Check for state 3, project 1
$filtered = array_filter($result, function ($row) {
    return $row['state_id'] == 3 && $row['project_id'] == 1;
});
echo "\nFiltered for state_id=3, project_id=1: " . count($filtered) . " rows\n";
