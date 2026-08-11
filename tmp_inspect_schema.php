<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$tables = ['map_role_issue_status', 'mst_issue_status', 'txn_issue'];
foreach ($tables as $table) {
    echo "TABLE $table\n";
    $cols = Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM `$table`");
    foreach ($cols as $col) {
        echo $col->Field . "\n";
    }
    echo "---\n";
}
