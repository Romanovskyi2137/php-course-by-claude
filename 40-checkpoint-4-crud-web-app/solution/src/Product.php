<?php

declare(strict_types=1);

namespace App;

// Checkpoint 4: CRUD-каталог — референсний розв'язок.

final class Product
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly int $categoryId,
        public readonly float $price,
        public readonly bool $inStock,
    ) {
    }
}
