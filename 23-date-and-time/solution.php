<?php

declare(strict_types=1);

// Тема 23: дата та час — референсний розв'язок.

function parse_date(string $date): \DateTimeImmutable
{
    $parsed = \DateTimeImmutable::createFromFormat('!Y-m-d', $date);
    if ($parsed === false || $parsed->format('Y-m-d') !== $date) {
        throw new \InvalidArgumentException("Некоректна дата: {$date}");
    }

    return $parsed;
}

function add_business_days(\DateTimeImmutable $date, int $days): \DateTimeImmutable
{
    $result = $date;
    $added = 0;
    while ($added < $days) {
        $result = $result->add(new \DateInterval('P1D'));
        if ((int) $result->format('N') < 6) {
            $added++;
        }
    }

    return $result;
}

function age_in_years(\DateTimeImmutable $birthDate, \DateTimeImmutable $today): int
{
    return $birthDate->diff($today)->y;
}

function format_in_timezone(\DateTimeImmutable $utc, string $timezone): string
{
    return $utc->setTimezone(new \DateTimeZone($timezone))->format('Y-m-d H:i:s T');
}
