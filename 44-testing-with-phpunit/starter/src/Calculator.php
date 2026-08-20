<?php

declare(strict_types=1);

namespace App;

// Тема 44: тестування з PHPUnit. Цей файл уже робочий — не редагуй його,
// твоя робота в tests/. Опис завдання — у task.md.

final class Calculator
{
    public function add(int $a, int $b): int
    {
        return $a + $b;
    }

    public function divide(int $a, int $b): float
    {
        if ($b === 0) {
            throw new \DivisionByZeroError('Ділення на нуль');
        }

        return $a / $b;
    }
}
