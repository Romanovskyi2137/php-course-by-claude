<?php

declare(strict_types=1);

// Скопіюй цей файл у config.local.php (він у .gitignore) і за потреби зміни значення.
// docker-compose.yml у цій же папці піднімає MySQL з такими самими дефолтами.

return [
    'db' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'database' => 'phpcourse',
        'username' => 'phpcourse',
        'password' => 'phpcourse',
        'charset' => 'utf8mb4',
    ],
];
