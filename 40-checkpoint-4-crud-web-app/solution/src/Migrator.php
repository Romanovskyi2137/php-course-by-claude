<?php

declare(strict_types=1);

namespace App;

// Checkpoint 4: CRUD-каталог — референсний розв'язок.

final class Migrator
{
    public static function pending(array $allMigrationFiles, array $appliedMigrationNames): array
    {
        $pending = array_values(array_diff($allMigrationFiles, $appliedMigrationNames));
        sort($pending);

        return $pending;
    }
}
