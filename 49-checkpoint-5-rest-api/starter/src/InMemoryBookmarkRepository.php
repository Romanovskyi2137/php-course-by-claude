<?php

declare(strict_types=1);

namespace App;

// Checkpoint 5: REST API закладок. Опис вимог — у task.md.

// TODO (вимога 2): сховище закладок у пам'яті.
final class InMemoryBookmarkRepository implements BookmarkRepository
{
    public function all(): array
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function find(int $id): ?Bookmark
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function add(string $title, string $url): Bookmark
    {
        throw new \RuntimeException('Не реалізовано');
    }

    public function delete(int $id): bool
    {
        throw new \RuntimeException('Не реалізовано');
    }
}
