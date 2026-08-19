<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== txn_issue_status_history ===\n";
$columns = \Illuminate\Support\Facades\Schema::getColumns('txn_issue_status_history');
foreach ($columns as $col) {
    echo "  - " . $col['name'] . " (" . $col['type'] . ")\n";
}

echo "\n=== mst_issue_status ===\n";
$columns = \Illuminate\Support\Facades\Schema::getColumns('mst_issue_status');
foreach ($columns as $col) {
    echo "  - " . $col['name'] . " (" . $col['type'] . ")\n";
}
