<?php
$env = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$config = [];
foreach ($env as $line) {
    if ($line === '' || $line[0] === '#') continue;
    [$key, $value] = explode('=', $line, 2);
    $config[trim($key)] = trim($value);
}
$dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $config['DB_HOST'] ?? '127.0.0.1', $config['DB_PORT'] ?? '3306', $config['DB_DATABASE'] ?? '');
$pdo = new PDO($dsn, $config['DB_USERNAME'] ?? '', $config['DB_PASSWORD'] ?? '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$tables = ['mst_user', 'map_user_role', 'map_role_hierarchy', 'mst_role'];
foreach ($tables as $t) {
    try {
        $stmt = $pdo->query("SHOW COLUMNS FROM `$t`");
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "TABLE:$t\n";
        foreach ($rows as $r) {
            echo $r['Field'] . '|' . $r['Type'] . '|' . $r['Null'] . '|' . $r['Key'] . '|' . $r['Extra'] . "\n";
        }
    } catch (Throwable $e) {
        echo "TABLE:$t|ERROR|" . str_replace(["\n", "\r"], ' ', $e->getMessage()) . "\n";
    }
}
