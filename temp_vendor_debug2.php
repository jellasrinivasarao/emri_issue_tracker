<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
use Illuminate\Support\Facades\DB;

$vendorId = 2;
$stateId = 3;
$projectId = 3;

$rows = DB::select('SELECT * FROM map_vendor_state WHERE vendor_id = ? ORDER BY state_id, project_id, application_id', [$vendorId]);
foreach ($rows as $row) {
    echo sprintf("vendor_id=%s state_id=%s project_id=%s application_id=%s is_active=%s\n", $row->vendor_id, $row->state_id, $row->project_id, $row->application_id, $row->is_active);
}

echo "\nproject options for vendor+state:\n";
$projectRows = DB::select('SELECT DISTINCT pr.project_id, pr.project_name FROM mst_project pr JOIN map_vendor_state m ON pr.project_id = m.project_id WHERE m.vendor_id = ? AND m.state_id = ? ORDER BY pr.project_name', [$vendorId, $stateId]);
var_export($projectRows);

echo "\napplication ids for vendor+state+project:\n";
$appRows = DB::select('SELECT DISTINCT application_id FROM map_vendor_state WHERE vendor_id = ? AND state_id = ? AND project_id = ? ORDER BY application_id', [$vendorId, $stateId, $projectId]);
var_export($appRows);
