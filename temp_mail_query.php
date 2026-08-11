<?php
$pdo = new PDO("mysql:host=172.16.10.95;port=3306;dbname=EMRI_ISSUE_TRACKER;charset=utf8mb4", "emri", "Emri123@", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$stmt = $pdo->query("SELECT mail_setting_id, name, host, port, encryption, username, from_address, from_name, is_active, created_at FROM mst_mail_setting ORDER BY mail_setting_id DESC LIMIT 10");
foreach ($stmt as $row) {
    echo implode(' | ', $row) . PHP_EOL;
}
?>
