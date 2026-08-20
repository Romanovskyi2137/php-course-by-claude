<?php

declare(strict_types=1);

namespace App;

// Тема 44: тестування з PHPUnit. Цей файл уже робочий — не редагуй його.

interface PriceFormatter
{
    public function format(float $amount): string;
}
