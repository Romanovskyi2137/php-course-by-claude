<?php

declare(strict_types=1);

// Тема 06: керуючі конструкції та match — референсний розв'язок.

function grade_letter(int $score): string
{
    return match (true) {
        $score >= 90 => 'A',
        $score >= 75 => 'B',
        $score >= 60 => 'C',
        default => 'F',
    };
}

function describe_weekday(int $isoWeekday): string
{
    return match (true) {
        $isoWeekday >= 1 && $isoWeekday <= 5 => 'будній день',
        $isoWeekday === 6 || $isoWeekday === 7 => 'вихідний',
        default => throw new \InvalidArgumentException(
            "Некоректний ISO-день тижня: {$isoWeekday}, очікую 1..7"
        ),
    };
}

function sum_positive(array $numbers): int
{
    $sum = 0;

    foreach ($numbers as $number) {
        if ($number <= 0) {
            continue;
        }

        $sum += $number;
    }

    return $sum;
}

function doubled(array $numbers): array
{
    $result = $numbers;

    foreach ($result as &$number) {
        $number *= 2;
    }
    unset($number);

    return $result;
}
