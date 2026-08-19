<?php

declare(strict_types=1);

// Тема 20: простори імен та автозавантаження. Опис завдання — у task.md.
// Класи App\Domain\Product і App\Support\StringHelper лежать у ./classes/
// і НЕ підвантажені тут через require — це і є суть завдання.

// TODO (вимога 1): зареєструвати автозавантажувач для простору імен App\.
function register_autoloader(string $baseDir): void
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 2): створити \App\Domain\Product без явного require.
function make_product(string $name, float $price): \App\Domain\Product
{
    throw new \RuntimeException('Не реалізовано');
}

// TODO (вимога 3): викликати \App\Support\StringHelper::shout() без явного require.
function shout_via_autoload(string $text): string
{
    throw new \RuntimeException('Не реалізовано');
}
