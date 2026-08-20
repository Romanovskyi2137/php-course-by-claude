<?php

declare(strict_types=1);

// Checkpoint 4: застосовує файли міграцій із ../migrations/, які ще не
// застосовані. Готовий код — не редагуй. Логіка обчислення "що ще не
// застосовано" живе в App\Migrator (../src/Migrator.php).

require __DIR__ . '/../vendor/autoload.php';

use App\Migrator;

$config = (require __DIR__ . '/../../../_env/config.local.php')['db'];
$dsn = "mysql:host={$config['host']};port={$config['port']};"
    . "dbname={$config['database']};charset={$config['charset']}";
$pdo = new \PDO($dsn, $config['username'], $config['password'], [
    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
]);

$pdo->exec('CREATE TABLE IF NOT EXISTS migrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    applied_at DATETIME NOT NULL
)');

$migrationsDir = __DIR__ . '/../migrations';
$allFiles = array_map('basename', glob($migrationsDir . '/*.sql'));
$applied = $pdo->query('SELECT name FROM migrations')->fetchAll(\PDO::FETCH_COLUMN);

$pending = Migrator::pending($allFiles, $applied);

if ($pending === []) {
    echo "Нема нових міграцій.\n";
    exit(0);
}

$insert = $pdo->prepare('INSERT INTO migrations (name, applied_at) VALUES (?, NOW())');

foreach ($pending as $file) {
    $sql = file_get_contents($migrationsDir . '/' . $file);
    $pdo->exec($sql);
    $insert->execute([$file]);
    echo "Застосовано: {$file}\n";
}
