<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$result = DB::table('map_vendor_state as m')
    ->select('m.state_id', 'm.project_id', 'm.vendor_id')
    ->where('m.is_active', 1)
    ->distinct()
    ->get();

echo "Total rows (distinct): " . $result->count() . "\n";

$filtered = $result->where('state_id', 3)->where('project_id', 1);
echo "Rows for state_id=3, project_id=1: " . $filtered->count() . "\n";

if ($filtered->count() > 0) {
    echo "Vendors:\n";
    foreach ($filtered as $row) {
        echo "  state_id=" . $row->state_id . ", project_id=" . $row->project_id . ", vendor_id=" . $row->vendor_id . "\n";
    }
} else {
    echo "No rows found!\n";
}
