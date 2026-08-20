# Завдання 23: дата та час

## Мета

1. Суворо парсити дату з рядка й відхиляти некоректний ввід.
2. Робити арифметику з датами через `\DateInterval`.
3. Рахувати різницю між датами через `->diff()`.
4. Конвертувати момент часу між часовими поясами.

## Вимоги

1. `parse_date(string $date)` — парсить рядок у форматі `Y-m-d` через
   `\DateTimeImmutable::createFromFormat()`; якщо рядок не є коректною
   датою в цьому форматі (включно з неіснуючими датами типу `2026-13-40`)
   — кидає `\InvalidArgumentException`.
2. `add_business_days(\DateTimeImmutable $date, int $days)` — додає
   `$days` **робочих** днів (пропускаючи суботу й неділю) і повертає
   новий об'єкт.
3. `age_in_years(\DateTimeImmutable $birthDate, \DateTimeImmutable $today)`
   — повна кількість років між датами (з урахуванням того, чи вже був
   день народження в поточному році).
4. `format_in_timezone(\DateTimeImmutable $utc, string $timezone)` —
   конвертує момент часу (вважай, що `$utc` вже в UTC) у вказаний часовий
   пояс і форматує як `'Y-m-d H:i:s T'`.

## Контракт (сигнатури)

```php
function parse_date(string $date): \DateTimeImmutable
function add_business_days(\DateTimeImmutable $date, int $days): \DateTimeImmutable
function age_in_years(\DateTimeImmutable $birthDate, \DateTimeImmutable $today): int
function format_in_timezone(\DateTimeImmutable $utc, string $timezone): string
```

## Обмеження

- Файл починається з `declare(strict_types=1);`.
- `parse_date()` не повинна мовчки "нормалізувати" некоректну дату —
  перевір, що відформатований назад результат збігається з вхідним рядком.
- `add_business_days()` не змінює переданий `$date` (він `Immutable` —
  інакше й не вийде), рахує саме робочі дні, не календарні.

## Як перевірити

```bash
php check.php
```

## Definition of Done

- [ ] усі перевірки зелені
- [ ] можеш пояснити, чому просто `new \DateTimeImmutable($date)` не
      підійшов би для `parse_date()`

## Бонус (не перевіряється автоматично)

Додай `next_occurrence_of(\DateTimeImmutable $from, int $isoWeekday):
\DateTimeImmutable` — найближча наступна дата з вказаним днем тижня
(1 = понеділок, 7 = неділя) строго після `$from`.

<details>
<summary>Підказка 1</summary>

```php
function parse_date(string $date): \DateTimeImmutable
{
    $parsed = \DateTimeImmutable::createFromFormat('!Y-m-d', $date);
    if ($parsed === false || $parsed->format('Y-m-d') !== $date) {
        throw new \InvalidArgumentException("Некоректна дата: {$date}");
    }

    return $parsed;
}
```
`!` на початку формату скидає час до `00:00:00` — без нього
`createFromFormat()` бере поточний час доби з `now`.
</details>

<details>
<summary>Підказка 2</summary>

```php
function add_business_days(\DateTimeImmutable $date, int $days): \DateTimeImmutable
{
    $result = $date;
    $added = 0;
    while ($added < $days) {
        $result = $result->add(new \DateInterval('P1D'));
        if ((int) $result->format('N') < 6) { // 1..5 = Пн..Пт
            $added++;
        }
    }

    return $result;
}
```
</details>

<details>
<summary>Підказка 3</summary>

```php
function age_in_years(\DateTimeImmutable $birthDate, \DateTimeImmutable $today): int
{
    return $birthDate->diff($today)->y;
}
```
`->diff()` вже враховує, чи був день народження цього року — компонент
`y` у результаті рахує саме повні прожиті роки, не просто `today->year -
birthDate->year`.
</details>
