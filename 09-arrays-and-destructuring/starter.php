<?php

declare(strict_types=1);

// Тема 09: масиви та деструктуризація. Опис завдання — у task.md.

// TODO (вимога 1): [$a, $b] -> [$b, $a] через деструктуризацію.
function swap(array $pair): array
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 2): злиття через spread [...$defaults, ...$overrides].
function merge_configs(array $defaults, array $overrides): array
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 3): новий масив з доданим значенням, без мутації оригіналу.
function append(array $items, mixed $value): array
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 4): значення за ключем $key з кожного запису, через foreach.
function pluck(array $records, string $key): array
{
    throw new \RuntimeException('Не реалізовано');
}
