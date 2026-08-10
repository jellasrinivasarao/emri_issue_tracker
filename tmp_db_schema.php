<?php
try {
    $env = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $config = [];
    foreach ($env as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (!str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $config[trim($key)] = trim($value);
    }
    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $config['DB_HOST'] ?? '127.0.0.1', $config['DB_PORT'] ?? '3306', $config['DB_DATABASE'] ?? '');
    $pdo = new PDO($dsn, $config['DB_USERNAME'] ?? '', $config['DB_PASSWORD'] ?? '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    $tables = [];
    foreach ($pdo->query("SHOW TABLES") as $row) {
        $tables[] = array_values($row)[0];
    }
    file_put_contents('db_tables_all.json', json_encode($tables, JSON_PRETTY_PRINT));

    $roleTables = [];
    foreach ($tables as $table) {
        if (stripos($table, 'role') !== false) {
            $roleTables[] = $table;
        }
    }
    file_put_contents('db_tables_role.json', json_encode($roleTables, JSON_PRETTY_PRINT));

    $schema = [];
    foreach ($roleTables as $table) {
        $stmt = $pdo->query("SHOW COLUMNS FROM `" . $table . "`");
        $schema[$table] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    file_put_contents('db_role_schema.json', json_encode($schema, JSON_PRETTY_PRINT));

    $usertable = 'mst_user';
    $stmt = $pdo->query("SHOW COLUMNS FROM `{$usertable}`");
    file_put_contents('db_mst_user_schema.json', json_encode($stmt->fetchAll(PDO::FETCH_ASSOC), JSON_PRETTY_PRINT));

    file_put_contents('db_schema_debug.txt', "OK\n");
} catch (Throwable $e) {
    file_put_contents('db_schema_debug.txt', "ERROR: " . $e->getMessage() . "\n");
}
