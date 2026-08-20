<?php

declare(strict_types=1);

namespace App;

// Checkpoint 5: REST API закладок. Опис вимог — у task.md.

// TODO (вимога 1): readonly DTO закладки.
final class Bookmark
{
    public function __construct()
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function toArray(): array
    {
        throw new \RuntimeException('Не реалізовано');
    }
}
