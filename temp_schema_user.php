<?php
$pdo = new PDO("mysql:host=172.16.10.95;port=3306;dbname=EMRI_ISSUE_TRACKER;charset=utf8mb4","emri","Emri123@", [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$stmt = $pdo->query("SHOW COLUMNS FROM mst_user");
foreach($stmt as $row) { echo $row['Field'] . ' | ' . $row['Type'] . PHP_EOL; }
?>
