<?php

declare(strict_types=1);

namespace App;

// Checkpoint 5: інтерфейс репозиторію закладок. Готовий код — не редагуй.

interface BookmarkRepository
{
    public function all(): array;

    public function find(int $id): ?Bookmark;

    public function add(string $title, string $url): Bookmark;

    public function delete(int $id): bool;
}
