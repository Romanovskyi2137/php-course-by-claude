<?php

declare(strict_types=1);

require __DIR__ . '/../_lib/check.php';

use function Course\Check\{assertSame, assertThrows, section, summary, test};

require __DIR__ . '/' . (getenv('CHECK_TARGET') ?: 'starter') . '.php';

section('Вимога 1: parse_date()');
test('парсить коректну дату', function (): void {
    assertSame('2026-08-20', parse_date('2026-08-20')->format('Y-m-d'));
});
test('кидає InvalidArgumentException на неіснуючу дату', function (): void {
    assertThrows(\InvalidArgumentException::class, fn () => parse_date('2026-13-40'));
});
test('кидає InvalidArgumentException на довільний текст', function (): void {
    assertThrows(\InvalidArgumentException::class, fn () => parse_date('not-a-date'));
});

section('Вимога 2: add_business_days()');
test('п\'ятниця + 1 робочий день = понеділок', function (): void {
    $friday = new \DateTimeImmutable('2026-08-21');
    assertSame('2026-08-24', add_business_days($friday, 1)->format('Y-m-d'));
});
test('понеділок + 5 робочих днів = наступний понеділок', function (): void {
    $monday = new \DateTimeImmutable('2026-08-24');
    assertSame('2026-08-31', add_business_days($monday, 5)->format('Y-m-d'));
});
test('не змінює оригінальний обʼєкт', function (): void {
    $date = new \DateTimeImmutable('2026-08-20');
    add_business_days($date, 3);
    assertSame('2026-08-20', $date->format('Y-m-d'));
});

section('Вимога 3: age_in_years()');
test('день народження ще не настав цього року', function (): void {
    assertSame(25, age_in_years(new \DateTimeImmutable('2000-08-21'), new \DateTimeImmutable('2026-08-20')));
});
test('день народження вже настав цього року', function (): void {
    assertSame(26, age_in_years(new \DateTimeImmutable('2000-08-20'), new \DateTimeImmutable('2026-08-20')));
});

section('Вимога 4: format_in_timezone()');
test('конвертує UTC у America/New_York', function (): void {
    $utc = new \DateTimeImmutable('2026-01-15 12:00:00', new \DateTimeZone('UTC'));
    assertSame('2026-01-15 07:00:00 EST', format_in_timezone($utc, 'America/New_York'));
});
test('не змінює момент часу, лише представлення', function (): void {
    $utc = new \DateTimeImmutable('2026-01-15 12:00:00', new \DateTimeZone('UTC'));
    $formatted = format_in_timezone($utc, 'Europe/Kyiv');
    assertSame('2026-01-15 14:00:00 EET', $formatted);
});

exit(summary());
