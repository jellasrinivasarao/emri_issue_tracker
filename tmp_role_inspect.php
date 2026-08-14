<?php
$env = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$config = [];
foreach ($env as $line) {
    $line = trim($line);
    if ($line === '' || str_starts_with($line, '#')) continue;
    if (!str_contains($line, '=')) continue;
    [$key, $value] = explode('=', $line, 2);
    $config[trim($key)] = trim($value);
}
$dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $config['DB_HOST'] ?? '127.0.0.1', $config['DB_PORT'] ?? '3306', $config['DB_DATABASE'] ?? '');
$pdo = new PDO($dsn, $config['DB_USERNAME'] ?? '', $config['DB_PASSWORD'] ?? '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$result = [];
$email = 'srinivasarao_jella@emri.in';
$login = 'srinivasarao_jella@emri.in';
$result['user'] = [];
$stmt = $pdo->prepare('SELECT * FROM mst_user WHERE official_email = ? OR login_id = ? LIMIT 1');
$stmt->execute([$email, $login]);
$result['user'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("SELECT TABLE_NAME FROM information_schema.columns WHERE COLUMN_NAME = 'role_id' AND TABLE_SCHEMA = DATABASE()");
$result['tables_with_role_id'] = $stmt->fetchAll(PDO::FETCH_COLUMN);

$stmt = $pdo->query("SELECT TABLE_NAME FROM information_schema.columns WHERE COLUMN_NAME LIKE '%page%' AND TABLE_SCHEMA = DATABASE()");
$result['tables_with_page'] = $stmt->fetchAll(PDO::FETCH_COLUMN);

$stmt = $pdo->query("SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema = DATABASE() AND TABLE_NAME LIKE '%role%'");
$roleTables = $stmt->fetchAll(PDO::FETCH_COLUMN);
$result['role_tables'] = $roleTables;
foreach ($roleTables as $table) {
    $stmt = $pdo->query("SHOW COLUMNS FROM `{$table}`");
    $result['schema'][$table] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt2 = $pdo->query("SELECT COUNT(*) AS count FROM `{$table}`");
    $result['counts'][$table] = $stmt2->fetch(PDO::FETCH_ASSOC)['count'];
}

if (in_array('map_role_privilege', $roleTables, true) || in_array('t_map_role_privilege', $roleTables, true)) {
    $table = in_array('map_role_privilege', $roleTables, true) ? 'map_role_privilege' : 't_map_role_privilege';
    $stmt = $pdo->query("SELECT * FROM {$table} LIMIT 50");
    $result['map_role_privilege'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

if ($pdo->query("SELECT 1 FROM information_schema.tables WHERE table_schema=DATABASE() AND table_name='mst_privilege'")->fetchColumn()) {
    $result['mst_privilege_schema'] = $pdo->query('SHOW COLUMNS FROM mst_privilege')->fetchAll(PDO::FETCH_ASSOC);
    $result['mst_privilege_rows'] = $pdo->query('SELECT * FROM mst_privilege LIMIT 50')->fetchAll(PDO::FETCH_ASSOC);
}

$stmt = $pdo->query("SELECT TABLE_NAME FROM information_schema.columns WHERE COLUMN_NAME = 'user_id' AND TABLE_SCHEMA = DATABASE() AND TABLE_NAME LIKE '%role%'");
$result['user_role_tables'] = $stmt->fetchAll(PDO::FETCH_COLUMN);

$stmt = $pdo->query("SELECT TABLE_NAME FROM information_schema.tables WHERE table_schema = DATABASE() AND TABLE_NAME LIKE '%menu%'");
$result['menu_tables'] = $stmt->fetchAll(PDO::FETCH_COLUMN);

file_put_contents('tmp_role_inspect.json', json_encode($result, JSON_PRETTY_PRINT));
