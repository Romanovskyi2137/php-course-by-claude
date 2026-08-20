<?php

declare(strict_types=1);

namespace App;

// Checkpoint 5: REST API закладок — референсний розв'язок.

final class Bookmark
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $url,
    ) {
    }

    public function toArray(): array
    {
        return ['id' => $this->id, 'title' => $this->title, 'url' => $this->url];
    }
}
