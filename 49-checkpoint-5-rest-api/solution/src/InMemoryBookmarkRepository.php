<?php

declare(strict_types=1);

namespace App;

// Checkpoint 5: REST API закладок — референсний розв'язок.

final class InMemoryBookmarkRepository implements BookmarkRepository
{
    private array $bookmarks = [];

    public function all(): array
    {
        $bookmarks = $this->bookmarks;
        usort($bookmarks, static fn (Bookmark $a, Bookmark $b): int => $a->id <=> $b->id);

        return $bookmarks;
    }

    public function find(int $id): ?Bookmark
    {
        foreach ($this->bookmarks as $bookmark) {
            if ($bookmark->id === $id) {
                return $bookmark;
            }
        }

        return null;
    }

    public function add(string $title, string $url): Bookmark
    {
        $id = $this->bookmarks === []
            ? 1
            : max(array_map(static fn (Bookmark $b): int => $b->id, $this->bookmarks)) + 1;

        $bookmark = new Bookmark($id, $title, $url);
        $this->bookmarks[] = $bookmark;

        return $bookmark;
    }

    public function delete(int $id): bool
    {
        foreach ($this->bookmarks as $index => $bookmark) {
            if ($bookmark->id === $id) {
                unset($this->bookmarks[$index]);

                return true;
            }
        }

        return false;
    }
}
