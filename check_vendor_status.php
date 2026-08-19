<?php
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'emri_issue_tracker';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

echo "\n=== Issue Status List ===\n";
$result = $conn->query('SELECT status_id, status_name, display_order FROM mst_issue_status ORDER BY display_order');
while ($row = $result->fetch_assoc()) {
    echo "[{$row['status_id']}] {$row['status_name']} (order: {$row['display_order']})\n";
}

echo "\n=== Vendor Role Status Mapping ===\n";
$result = $conn->query('
    SELECT m.status_id, s.status_name, m.display_order, m.is_allowed, r.role_name
    FROM map_role_issue_status m
    JOIN mst_issue_status s ON m.status_id = s.status_id
    JOIN mst_role r ON m.role_id = r.role_id
    WHERE r.role_name IN ("Vendor", "Vendor IT", "Vendor Admin")
    ORDER BY m.display_order
');
while ($row = $result->fetch_assoc()) {
    echo "[{$row['status_id']}] {$row['status_name']} (Role: {$row['role_name']}, Allowed: {$row['is_allowed']}, Order: {$row['display_order']})\n";
}

$conn->close();
?>
