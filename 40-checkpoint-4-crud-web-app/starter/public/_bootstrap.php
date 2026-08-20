<?php

declare(strict_types=1);

// Checkpoint 4: спільний бутстрап для CRUD-скриптів. Готовий код — не редагуй.

require __DIR__ . '/../vendor/autoload.php';

use App\ProductRepository;

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function productRepository(): ProductRepository
{
    $config = (require __DIR__ . '/../../../_env/config.local.php')['db'];
    $dsn = "mysql:host={$config['host']};port={$config['port']};"
        . "dbname={$config['database']};charset={$config['charset']}";
    $pdo = new \PDO($dsn, $config['username'], $config['password'], [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
    ]);

    return new ProductRepository($pdo);
}

function categories(): array
{
    $config = (require __DIR__ . '/../../../_env/config.local.php')['db'];
    $dsn = "mysql:host={$config['host']};port={$config['port']};"
        . "dbname={$config['database']};charset={$config['charset']}";
    $pdo = new \PDO($dsn, $config['username'], $config['password'], [
        \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
    ]);

    return $pdo->query('SELECT * FROM categories ORDER BY id')->fetchAll();
}
