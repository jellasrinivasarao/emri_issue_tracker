<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\DB;

$users = User::with('roles')->whereHas('roles', function ($q) {
    $q->whereRaw('LOWER(role_name) LIKE ?', ['%vendor%']);
})->take(10)->get();

foreach ($users as $user) {
    echo "user_id=" . $user->user_id . " login_id=" . $user->login_id . " official_email=" . $user->official_email . " vendor_id=" . ($user->vendor_id ?? 'NULL') . " roles=" . $user->roles->pluck('role_name')->join(', ') . "\n";
}

$rows = DB::select('SELECT vendor_id, state_id, project_id, application_id, is_active FROM map_vendor_state LIMIT 20');
foreach ($rows as $row) {
    echo "map_vendor_state: vendor_id={$row->vendor_id} state_id={$row->state_id} project_id={$row->project_id} application_id={$row->application_id} is_active={$row->is_active}\n";
}
