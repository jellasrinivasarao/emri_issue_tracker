<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Simulate a ticket array like the controller creates
$ticket = [
    'id' => 'IS-20260817129',
    'issue_id' => 129,
    'title' => 'Test Issue',
    'state' => 'Assam',
    'state_id' => 3,
    'project' => '108',
    'project_id' => 1,
    'status' => 'Vendor Assignment',
    'status_id' => 12,
];

echo "Ticket array:\n";
print_r($ticket);

echo "\n\nJSON via @json():\n";
echo json_encode($ticket) . "\n";

echo "\n\nSimulating @js() in Blade:\n";
// In Blade, @js() is similar to @json() but specifically for JS context
echo "var ticket = " . json_encode($ticket) . ";\n";
echo "ticket.state_id = " . (isset($ticket['state_id']) ? $ticket['state_id'] : 'UNDEFINED') . "\n";
echo "ticket.project_id = " . (isset($ticket['project_id']) ? $ticket['project_id'] : 'UNDEFINED') . "\n";
