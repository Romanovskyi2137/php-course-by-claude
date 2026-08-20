<?php

declare(strict_types=1);

namespace App;

// Тема 47: фреймворки та екосистема — референсний розв'язок.

function classify_file(string $path): string
{
    if (str_contains($path, '/Controllers/') || str_ends_with($path, 'Controller.php')) {
        return 'controller';
    }
    if (str_contains($path, '/Models/') || str_ends_with($path, 'Model.php')) {
        return 'model';
    }
    if (str_contains($path, '/Views/') || str_ends_with($path, '.blade.php') || str_ends_with($path, '.twig')) {
        return 'view';
    }
    if (str_starts_with($path, 'config/') || str_contains($path, '/config/') || str_ends_with($path, '.env')) {
        return 'config';
    }
    if (str_starts_with($path, 'public/')) {
        return 'public';
    }

    return 'unknown';
}

function count_files_by_layer(array $paths): array
{
    $counts = [];
    foreach ($paths as $path) {
        $layer = classify_file($path);
        $counts[$layer] = ($counts[$layer] ?? 0) + 1;
    }

    return $counts;
}
