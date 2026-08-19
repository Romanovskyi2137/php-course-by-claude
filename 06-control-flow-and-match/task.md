# Завдання 06: керуючі конструкції та `match`

## Мета

1. Впевнено писати `match`, включно з патерном `match(true)` для діапазонів.
2. Розуміти, коли `foreach` копіює значення, а коли працює за посиланням.

## Що робимо

Функції на `match` для реальних задач (оцінки, дні тижня) плюс дві функції
на `foreach`, одна з яких навмисно демонструє посилання.

## Вимоги

1. `grade_letter(int $score)` повертає літерну оцінку за шкалою:
   `90..100 -> 'A'`, `75..89 -> 'B'`, `60..74 -> 'C'`, `0..59 -> 'F'`.
   Використай `match (true)` з умовами `>=`.
2. `describe_weekday(int $isoWeekday)` повертає `'будній день'` для `1..5`
   (ISO: 1=понеділок, 7=неділя) і `'вихідний'` для `6..7`. Для будь-якого
   іншого значення кидає `\InvalidArgumentException`.
3. `sum_positive(array $numbers)` повертає суму лише додатних чисел масиву
   (пропускаючи нульові й від'ємні через `continue`, без `array_filter`/`array_sum`).
4. `doubled(array $numbers)` повертає **новий** масив, де кожне число подвоєне,
   реалізований через `foreach` **за посиланням** на копію вхідного масиву
   (щоб продемонструвати `&$item` і обов'язковий `unset()` після циклу).

## Контракт (сигнатури)

```php
function grade_letter(int $score): string
function describe_weekday(int $isoWeekday): string
function sum_positive(array $numbers): int
function doubled(array $numbers): array
```

## Обмеження

- Файл починається з `declare(strict_types=1);`.
- Вимога 1 і 2 — тільки через `match`, без `if/elseif`.
- Вимога 3 — тільки через `foreach` з `continue`, без `array_filter`/`array_sum`
  (ці функції — тема 10).
- Вимога 4 — обов'язково `foreach (... as &$item)` і `unset()` одразу після
  циклу. `check.php` не може напряму перевірити наявність `unset()` у твоєму
  коді (це питання стилю, а не результату виклику), тому переглянь `solution.php`
  після зеленого check.php й порівняй — забутий `unset` тут не ламає саме цю
  функцію (нова локальна область для кожного виклику), але стає реальним багом,
  щойно змінна `$number` використовується ще раз у тій самій області видимості
  (наприклад, у другому `foreach` без `&`).

## Як перевірити

```bash
php check.php
```

## Definition of Done

- [ ] усі перевірки зелені
- [ ] можеш пояснити, чому забутий `unset()` після `foreach (&$item)` — реальний баг,
      а не теоретична дрібниця

## Бонус (не перевіряється автоматично)

Напиши `first_negative_index(array $numbers): ?int`, яка повертає індекс першого
від'ємного числа або `null`, якщо такого немає — використай `foreach` з `break`.

<details>
<summary>Підказка 1</summary>

`grade_letter`:
```php
return match (true) {
    $score >= 90 => 'A',
    $score >= 75 => 'B',
    $score >= 60 => 'C',
    default => 'F',
};
```
</details>

<details>
<summary>Підказка 2</summary>

Для `doubled()`: спочатку `$result = $numbers;` (копія — масиви в PHP копіюються
за значенням, тема 09), потім `foreach ($result as &$n) { $n *= 2; }`, потім
`unset($n);`, потім `return $result;`.
</details>
