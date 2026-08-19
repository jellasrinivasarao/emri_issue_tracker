<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$columns = \Illuminate\Support\Facades\Schema::getColumns('map_issue_vendor_assignment');
echo "Columns in map_issue_vendor_assignment:\n";
foreach ($columns as $col) {
    echo "  - " . $col['name'] . " (" . $col['type'] . ")\n";
}
