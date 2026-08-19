<?php

declare(strict_types=1);

namespace App\Domain;

// Клас для теми 20: підвантажується автозавантажувачем, який пишеш у starter.php.
// Не редагуй цей файл — завдання про сам автозавантажувач, не про цей клас.

class Product
{
    public function __construct(
        public string $name,
        public float $price,
    ) {
    }
}
