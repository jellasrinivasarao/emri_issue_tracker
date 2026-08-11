<?php
$pdo = new PDO("mysql:host=172.16.10.95;port=3306;dbname=EMRI_ISSUE_TRACKER;charset=utf8mb4", "emri", "Emri123@", [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$stmt = $pdo->query("SHOW FULL COLUMNS FROM mst_user LIKE 'password_reset_otp'");
$row = $stmt->fetch(PDO::FETCH_ASSOC);
var_export($row);
?>
