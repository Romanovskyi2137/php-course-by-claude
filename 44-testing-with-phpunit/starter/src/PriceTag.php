<?php

declare(strict_types=1);

namespace App;

// Тема 44: тестування з PHPUnit. Цей файл уже робочий — не редагуй його.

final class PriceTag
{
    public function __construct(private PriceFormatter $formatter)
    {
    }

    public function render(float $amount): string
    {
        return '[' . $this->formatter->format($amount) . ']';
    }
}
